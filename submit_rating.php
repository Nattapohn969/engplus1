<?php
session_start();
require 'connect.php'; // Your database connection

$lessonID = $_POST['lessonID'];
$userID = $_POST['user_ID'];
$rating = $_POST['rating'];

$stmt = $conn->prepare("INSERT INTO ratings (lessonID, user_ID, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
$stmt->bind_param("iii", $lessonID, $userID, $rating);

if ($stmt->execute()) {
    echo "Rating saved successfully.";
} else {
    echo "Error saving rating.";
}

$stmt->close();
$conn->close();
?>