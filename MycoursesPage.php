<?php
session_start();

// Check if the user is logged in and has the Learner role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Learner') {
    header('Location: login.php');
    exit;
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);
$user_ID = $_SESSION['user_ID'];

include 'connect.php'; // Connect to the database

// Fetch favorite lessons from the database
$sql = "SELECT lessons.lessonID, lessons.lessonName, lessons.cover_image 
        FROM favorite_lesson 
        JOIN lessons ON favorite_lesson.lessonID = lessons.lessonID 
        WHERE favorite_lesson.user_ID = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('i', $user_ID);

if (!$stmt->execute()) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="MycoursesPage.css" rel="stylesheet" />
    <title>ENG PLUS</title>
</head>
<body>
    <div class='navbar'>
        <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'>
        <div class='innavbar'>
            <ul><a href='HomePage.php' class='blacktext' style="margin-right: 5px">Home</a></ul>
            <ul><a href='CoursesPage.php' class='blacktext' style="margin-right: 5px">Courses</a></ul>
            <ul><a href='MycoursesPage.php' class='blacktext' style="margin-right: 5px">My Courses</a></ul>
            <ul><a href='#' class='blacktext' style="margin-right: 5px">Transform</a></ul>
            <ul><a href='ProfilePage.php' class='blacktext' style="margin-right: 30px">Profile</a></ul>
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

    <div class="text-h1">
        <h1>บทเรียนที่ชื่นชอบ</h1>
    </div>

    <div id="my-courses"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const courses = <?php echo json_encode($courses); ?>;
            renderMyCourses(courses);
        });

        function renderMyCourses(courses) {
            const myCoursesDiv = document.getElementById("my-courses");
            myCoursesDiv.innerHTML = "";

            courses.forEach((course) => {
                const card = document.createElement("div");
                card.className = "course-card";
                card.setAttribute('data-lesson-id', course.lessonID);

                // Add image to card
                const image = document.createElement("img");
                image.src = course.cover_image;
                image.width = 200;
                image.height = 150;
                image.alt = `${course.lessonName} Image`;

                // Add title
                const title = document.createElement("h4");
                title.textContent = course.lessonName;

                // Add remove button
                const removeButton = document.createElement("span");
                removeButton.textContent = "Remove";
                removeButton.className = "remove-button";
                removeButton.onclick = function () {
                    removeCourse(course.lessonID);
                };

                // Append everything to the card
                card.appendChild(image);
                card.appendChild(title);
                card.appendChild(removeButton);

                // Append card to my-courses div
                myCoursesDiv.appendChild(card);
            });
        }

        function removeCourse(lessonID) {
            fetch('remove_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `lessonID=${lessonID}`
            }).then(response => {
                if (response.ok) {
                    document.querySelector(`.course-card[data-lesson-id="${lessonID}"]`).remove();
                } else {
                    alert('Failed to remove the course.');
                }
            });
        }
    </script>
</body>
</html>
