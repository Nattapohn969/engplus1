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
    <link href="CoursesPage.css" rel="stylesheet" />
    <title>ENG PLUS</title>
    <!-- <style>
        .course-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin: 20px;
        }

        .course-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background-color: #f0f0f0;
            width: 80%;
            height: 200px; /* Set a fixed height to match the design */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .course-image {
            width: 200px;
            height: 150px;
            background-color: #e0e0e0;
            background-image: url('path_to_your_placeholder_image'); /* Use a placeholder image */
            background-size: cover;
            background-position: center;
            border-radius: 10px;
        }

        .course-info {
            flex-grow: 1;
            margin-left: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-info h4 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        .course-info p {
            margin: 10px 0;
            font-size: 1rem;
            color: #777;
        }

        .heart-icon {
            font-size: 2rem;
            cursor: pointer;
            margin-right: 20px;
        }

        .heart-icon.saved {
            color: red;
        }

        .access-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            align-self: flex-end; /* Align the button to the right */
        }

        .access-btn:hover {
            background-color: #45a049;
        }
        
        .heart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .heart-container .heart-icon {
            font-size: 1.8rem;
            cursor: pointer;
        }

        .heart-container .heart-icon.saved {
            color: red;
        }
    </style> -->
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
                    <?php
                    echo htmlspecialchars($_SESSION['username']);
                    // แสดงบทบาทของผู้ใช้
                    if (isset($_SESSION['role'])) {
                        echo " (" . htmlspecialchars($_SESSION['role']) . ")";
                    }
                    ?>
                </button>
                <div id="dropdownContent" class="dropdown-content">
                    <a href="logout.php">Logout</a>
                </div>
            </div>

        </div>
    </div>

    <h1>บทเรียนทั้งหมด</h1>

    <div class="course-list">
        <?php
        include 'connect.php';
        // Fetch lessons from the database
        $sql = "SELECT * FROM lessons";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_array($result)) {
            ?>
            <div class="course-card">
                <div class="course-image" style="background-image: url('<?= htmlspecialchars($row['cover_image']) ?>');">
                </div>
                <div class="course-info">
                    <h4><?= htmlspecialchars($row['lessonName']); ?></h4>
                    <p>รายละเอียด: Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                </div>
                <div class="heart-container">
                    <span class="heart-icon" onclick="toggleSaveLesson(this, <?= $row['lessonID'] ?>)">&#x2661;</span>
                    <a href="lesson.php?lessonID=<?= $row['lessonID'] ?>" class="access-btn">เข้าสู่บทเรียน</a>
                </div>
            </div>
            <?php
        }
        mysqli_close($conn);
        ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const savedCourses = JSON.parse(localStorage.getItem("myCourses")) || [];
            document.querySelectorAll('.course-card').forEach(card => {
                const heartIcon = card.querySelector('.heart-icon');
                const lessonID = heartIcon.getAttribute('data-lesson-id');
                if (savedCourses.some(course => course.id === parseInt(lessonID))) {
                    heartIcon.classList.add('saved');
                    heartIcon.innerHTML = '&#x2665;';
                }
            });
        });

        function toggleSaveLesson(element, lessonID) {
            const card = element.closest('.course-card');
            const lessonName = card.querySelector('h4').textContent;
            const lessonImage = card.querySelector('.course-image').style.backgroundImage;

            let savedCourses = JSON.parse(localStorage.getItem("myCourses")) || [];
            const course = { name: lessonName, image: lessonImage, id: lessonID };

            const courseIndex = savedCourses.findIndex(course => course.id === lessonID);
            if (courseIndex !== -1) {
                savedCourses.splice(courseIndex, 1);
                element.classList.remove('saved');
                element.innerHTML = '&#x2661;';
                removeFromDatabase(lessonID);
            } else {
                savedCourses.push(course);
                element.classList.add('saved');
                element.innerHTML = '&#x2665;';
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
                body: `lessonID=${lessonID}`
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('เพิ่มลงในฐานข้อมูลแล้ว');
                    }
                });
        }

        function removeFromDatabase(lessonID) {
            fetch('remove_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `lessonID=${lessonID}`
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('ลบออกจากฐานข้อมูลแล้ว');
                    }
                });
        }
    </script>
</body>

</html>