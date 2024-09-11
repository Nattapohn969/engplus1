<?php
session_start();
include 'connect.php';

$user_ID = $_SESSION['user_ID'];
$LessonID = intval($_GET['LessonID']);

$sql = "SELECT COUNT(*) as count FROM favorite_lesson WHERE user_ID = ? AND LessonID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_ID, $LessonID);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$isFavorite = $data['count'] > 0;

echo json_encode(['isFavorite' => $isFavorite]);

$stmt->close();
$conn->close();
?>
