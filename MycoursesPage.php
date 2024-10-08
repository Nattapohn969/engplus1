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
    <link
        href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <link href="MycoursesPage.css" rel="stylesheet" />
    <title>ENG PLUS</title>
</head>
<style>
    h1 {
        padding-left: 20px;
    }

    body {
        background-image: url("assets/img/mycou.png");
        background-size: cover;
        /* ขยายรูปให้เต็มพื้นที่แต่คงสัดส่วน */
        background-attachment: fixed;

        font-family: "Mali",
            serif;

        font-style: normal;
    }

    .course-card {
        border: 1px solid #ddd;
        padding: 16px;
        margin: 10px;
        display: inline-block;
        width: 350px;
        height: 350px;
        text-align: center;
        position: relative;
        background-color: #f9f9f9f5;
        border-radius: 20px;
    }


    .remove-button {
        color: #fff;
        cursor: pointer;
        margin-top: 10px;
        display: block;
        text-align: center;
        border: 2px solid red;
        background-color: red;
        width: 150px;
        height: 30px;
        border-radius: 10px;
        font-size: 16px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
        text-decoration: none;
        margin-left: 90px;


    }

    /* Hover state */
    .remove-button:hover {
        background-color: darkred;
        border-color: #fff;
        text-decoration: none;

    }



    .navbar {
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: "Mali", serif;
        font-weight: 700;
        font-style: normal;
    }



    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #333;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 5px;
    }


    .dropdown-content a {
        color: #fff;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        border-bottom: 1px solid #ddd;
        font-family: "Mali", serif;
        font-weight: 500;
        font-style: normal;
    }


    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background-color: #3f9965;
        color: #fff;
        border: none;
        padding: 10px 15px;
        font-size: 17px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s;
        font-family: "Mali", serif;
        font-weight: 700;
        font-style: normal;
    }

    .dropbtn:hover {
        background-color: #ddd;
        color: #333;
    }

    .dropbtn1:hover {
        background-color: #ddd;
        color: #333;
    }



    .dropdown-content a:hover {
        background-color: #ddd;
        color: #333;
    }

    .dropdown:hover .dropdown-content {
        display: block;
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
            <!-- <ul><a href='edit1.php' class='blacktext' style="margin-right: 30px">Profile</a></ul> -->
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
                    <a href="edit1.php">Profile</a>
                    <a href="report.php">Report</a>
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
                            <button type="button" class="remove-button"
                                onclick="removeCourse(<?php echo $course['lessonID']; ?>)">Remove</button>
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