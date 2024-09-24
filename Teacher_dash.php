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
    <!-- <link rel="stylesheet" href="css/stylead.css" /> -->
</head>
<style>
   
</style>

<body>
    <?php include 'navbar_Tr.php' ?>

</body>
</html>