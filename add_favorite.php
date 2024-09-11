<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_ID'])) {
    http_response_code(403);
    exit;
}

$user_ID = $_SESSION['user_ID'];
$LessonID = intval($_POST['LessonID']);

$sql = "INSERT INTO favorite_lesson (user_ID, LessonID, added_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_ID, $LessonID);
$stmt->execute();
$stmt->close();
$conn->close();
?>
