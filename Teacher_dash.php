<?php
session_start();

// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['user_ID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: login.php");
    exit();
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/stylead.css" />
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="assets/img/Logonew.png" alt="Logo" />
        </div>
        <div class="menu" id="menu">
            <a href="index.html">Dashboard</a>
            <!-- <a href="user.php">Manage Account</a> -->
            <div class="dropdown">
                <button class="dropbtn">Manage Lesson</button>
                <div class="dropdown-content">
                    <a href="add_lesson.php">Create Lesson</a>
                    <a href="lesson_show.php">Manage Lesson</a><!-- เพิ่มลิงค์เพิ่มเติมตามที่ต้องการ -->
                </div>

            </div>
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

</body>

</html>