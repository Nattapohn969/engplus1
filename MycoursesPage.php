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
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="MycoursesPage.css" rel="stylesheet" />
    <title>ENG PLUS</title>
</head>
<style>
    h1 {
        padding-left: 20px;
    }
</style>

<body>
    <div class='navbar'>
        <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'>
        <div class='innavbar'>
            <!-- <ul><a href='HomePage.php' class='blacktext' style="margin-right: 5px">Home</a></ul> -->
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

    <div class="page-container">
        <div class="header-section">
            <h1>บทเรียนที่ชื่นชอบ</h1>
        </div>

        <div id="course-list" class="course-grid">
            <?php if (empty($courses)): ?>
                <p>No favorite courses found.</p>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card" data-lesson-id="<?php echo $course['lessonID']; ?>">
                        <img src="<?php echo htmlspecialchars($course['cover_image']); ?>"
                            alt="<?php echo htmlspecialchars($course['lessonName']); ?> Image" width="200" height="150">
                        <div class="course-details">
                            <h4><?php echo htmlspecialchars($course['lessonName']); ?></h4>
                            <span class="remove-button" onclick="removeCourse(<?php echo $course['lessonID']; ?>)">Remove</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
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