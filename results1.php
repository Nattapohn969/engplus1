<?php
// รับค่าคะแนนและข้อมูลอื่นๆ จาก URL
$score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$totalQuestions = isset($_GET['total']) ? intval($_GET['total']) : 0;
$lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
$testType_ID = 1; // กำหนดค่า testType_ID เป็น 1

// ข้อความแสดงผลลัพธ์
$resultMessage = "คุณทำได้ $score จากทั้งหมด $totalQuestions ข้อ";

// บันทึกผลลัพธ์ลงฐานข้อมูล
include 'connect.php';

// สมมติว่ามีตาราง test_results ที่ต้องการเก็บผลลัพธ์
$sql = "INSERT INTO test_results (lessonID, testType_ID, user_ID, total_score, timestamp) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

// กำหนดค่า user_ID จาก session (สมมติว่าคุณใช้ session ในการจัดการผู้ใช้)
session_start();
$user_ID = isset($_SESSION['user_ID']) ? intval($_SESSION['user_ID']) : 0;

$stmt->bind_param("iiii", $lessonID, $testType_ID, $user_ID, $score);

if ($stmt->execute()) {
    $resultMessage .= " และบันทึกผลลัพธ์สำเร็จ!";
} else {
    $resultMessage .= " แต่เกิดข้อผิดพลาดในการบันทึกผลลัพธ์.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>ผลลัพธ์แบบทดสอบ</title>

</head>

<body>
    <?php include 'rating.php'; ?>
</body>

</html>