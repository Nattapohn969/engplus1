<?php
session_start();

// Check if the Learner is logged in and has the Teacher role
if (!isset($_SESSION['user_ID']) || $_SESSION['role'] !== 'Teacher') {
    header("Location: login.php");
    exit();
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);
$userID = $_SESSION['user_ID'];

?>

<div class="navbar">
    <div class="logo">
        <img src="assets/img/Logonew.png" alt="Logo" />
    </div>
    <div class="menu" id="menu">
        <a href="Teacher_dash.php">Dashboard</a>
        <!-- <a href="user.php">Manage Account</a> -->
        <div class="dropdown">
            <button class="dropbtn">Manage Lesson</button>
            <div class="dropdown-content">
                <a href="lesson_add.php">Create Lesson</a>
                <a href="lessons_manage.php">Manage Lesson</a>
                <!-- เพิ่มลิงค์เพิ่มเติมตามที่ต้องการ -->
            </div>
        </div>
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