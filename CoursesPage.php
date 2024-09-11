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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/CoursesPage.css">
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
            <ul><a href='ProfilePage.php' class='blacktext' style="margin-right: 10px">Profile</a></ul>


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

    <h1>บทเรียนทั้งหมด</h1>
    <?php
    include 'connect.php';
    // Fetch lessons from the database
    $sql = "SELECT * FROM lessons";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($result)) {
        ?>
        <div class="course-card">
            <span class="heart-icon" onclick="toggleSaveLesson(this, <?= $row['lessonID'] ?>)">&#x2661;</span>
            <a href="lesson.php?LessonID=<?= $row['LessonID'] ?>">
                <img src="../image/<?= htmlspecialchars($row['Image']) ?>" alt="Image">
            </a>
            <h4><?= htmlspecialchars($row['LessonName']); ?></h4>
        </div>

        <?php
    }
    mysqli_close($conn);
    ?>

    <script src="js/jsdropdown.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const savedCourses = JSON.parse(localStorage.getItem("myCourses")) || [];
            document.querySelectorAll('.course-card').forEach(card => {
                const lessonID = card.getAttribute('data-lesson-id');
                fetch(`check_favorite.php?LessonID=${lessonID}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.isFavorite) {
                            const heartIcon = card.querySelector('.heart-icon');
                            heartIcon.classList.add('saved');
                            heartIcon.innerHTML = '&#x2665;'; // Filled heart
                        }
                    });
            });
        });

        function toggleSaveLesson(element, lessonID) {
            const card = element.closest('.course-card');
            const lessonName = card.getAttribute('data-lesson');
            const lessonImage = card.querySelector('img').src;

            let savedCourses = JSON.parse(localStorage.getItem("myCourses")) || [];
            const course = { name: lessonName, image: lessonImage, id: lessonID };

            const courseIndex = savedCourses.findIndex(course => course.id === lessonID);
            if (courseIndex !== -1) {
                savedCourses.splice(courseIndex, 1);
                element.classList.remove('saved');
                element.innerHTML = '&#x2661;'; // Outline heart
                removeFromDatabase(lessonID);
            } else {
                savedCourses.push(course);
                element.classList.add('saved');
                element.innerHTML = '&#x2665;'; // Filled heart
                addToDatabase(lessonID);
            }

            localStorage.setItem("myCourses", JSON.stringify(savedCourses));
        }

        function addToDatabase(lessonID) {
            fetch('add_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `LessonID=${lessonID}`
            });
        }

        function removeFromDatabase(lessonID) {
            fetch('remove_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `LessonID=${lessonID}`
            });
        }
    </script>
</body>

</html>