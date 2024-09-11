<?php
session_start();

// Check if the user is logged in and has the Learner role
if (!isset($_SESSION['user_ID']) || $_SESSION['role'] !== 'Learner') {
    header('Location: login.php');
    exit;
}

$user_ID = $_SESSION['user_ID'];
$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/MycoursesPage.css">
    <title>ENG PLUS</title>
</head>

<body>
    <div class='navbar'>
        <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'>
        <div class='innavbar'>
            <ul><a href='HomePage.html' class='blacktext' style="margin-right: 10px">Home</a></ul>
            <ul><a href='CoursesPage.php' class='blacktext' style="margin-right: 10px">Courses</a></ul>
            <ul><a href='MycoursesPage.php' class='blacktext' style="margin-right: 10px">My Courses</a></ul>
            <ul><a href='#' class='blacktext' style="margin-right: 30px">Transform</a></ul>

            <div id="accountMenu" class="dropdown">
                <button class="dropbtn" id="accountButton">
                    <?php echo $username; ?>
                </button>
                <div id="dropdownContent" class="dropdown-content">
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <body>
        <div class="text-h1">
            <h1>บทเรียนที่ชื่นชอบ</h1>
        </div>

        <div id="my-courses"></div>

        <?php
        include 'connect.php';
        // Fetch favorite lessons from the database
        $sql = "SELECT all_lesson.LessonID, all_lesson.LessonName, all_lesson.Image1 
                FROM favorite_lesson 
                JOIN all_lesson ON favorite_lesson.LessonID = all_lesson.LessonID 
                WHERE favorite_lesson.user_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        $stmt->close();
        $conn->close();
        ?>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const courses = <?php echo json_encode($courses); ?>;
                renderMyCourses(courses);
            });

            function renderMyCourses(courses) {
                const myCoursesDiv = document.getElementById("my-courses");
                myCoursesDiv.innerHTML = "";

                courses.forEach((course, index) => {
                    const card = document.createElement("div");
                    card.className = "course-card";
                    card.setAttribute('data-lesson-id', course.LessonID);

                    // Add image to the card
                    const image = document.createElement("img");
                    image.src = "../image/" + course.Image1;
                    image.width = 200;
                    image.height = 150;
                    image.alt = `${course.LessonName} Image`;

                    // Add title
                    const title = document.createElement("h4");
                    title.textContent = course.LessonName;

                    // Add remove button
                    const removeButton = document.createElement("span");
                    removeButton.textContent = "Remove";
                    removeButton.className = "remove-button";
                    removeButton.onclick = function () {
                        removeCourse(course.LessonID);
                    };

                    // Append all elements to the card
                    card.appendChild(image);
                    card.appendChild(title);
                    card.appendChild(removeButton);

                    // Append the card to the myCoursesDiv
                    myCoursesDiv.appendChild(card);
                });
            }

            function removeCourse(lessonID) {
                fetch('remove_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `LessonID=${lessonID}`
                }).then(response => response.text())
                    .then(() => {
                        document.querySelector(`.course-card[data-lesson-id="${lessonID}"]`).remove();
                    });
            }
        </script>
    </body>

</html>