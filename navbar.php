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
                <a href="add_lesson.php">Create Lesson</a>
                <a href="lesson_show.php">Manage Lesson</a>
                <!-- เพิ่มลิงค์เพิ่มเติมตามที่ต้องการ -->
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