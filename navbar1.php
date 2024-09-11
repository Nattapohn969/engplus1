<?php
session_start();

// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['user_ID']) || $_SESSION['role'] !== 'Learner') {
    header("Location: login.php");
    exit();
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);
?>
<?php
session_start();

// Check if the Learner is logged in and has the Teacher role
if (!isset($_SESSION['user_ID']) || $_SESSION['role'] !== 'Learner') {
    header("Location: login.php");
    exit();
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);
$userID = $_SESSION['user_ID'];

?>
<div class='navbar'>
    <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'></img>
    <div class='innavbar'>
        <ul><a href='HomePage.html' class='blacktext'>Home</a></ul>
        <ul><a href='CoursesPage.php' class='blacktext'>Courses</a></ul>
        <ul><a href='MycoursesPage.html' class='blacktext'>My Courses</a></ul>
        <ul><a href='#' class='blacktext'>Transform</a></ul>
        <ul><a href='Login.php' class='blacktext'>Log in</a></ul>
        <ul><a href='Register.html' class='createacc'>Sign up</a></ul>
    </div>

</div>