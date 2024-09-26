<?php
include 'connect.php'; // รวมไฟล์เชื่อมต่อฐานข้อมูล

session_start(); // เริ่มต้น session

// ตรวจสอบว่ามีผู้ใช้เข้าสู่ระบบ
if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php"); // ถ้ายังไม่ได้เข้าสู่ระบบ ให้ไปที่หน้า login
    exit;
}

// รับค่าคะแนนรวมจาก URL
$total_score = isset($_GET['score']) ? intval($_GET['score']) : 0;
$lessonID = isset($_GET['lessonID']) ? intval($_GET['lessonID']) : 1; // ตัวอย่าง lessonID
$user_ID = $_SESSION['user_ID']; // user_ID ที่เข้าสู่ระบบ
$testType_ID = 2; // กำหนด testType_ID เป็น 2
$timestamp = date("Y-m-d H:i:s"); // รับเวลาปัจจุบัน

// สร้างคำสั่ง SQL เพื่อบันทึกข้อมูลลงในตาราง test_results
$query = "INSERT INTO test_results (lessonID, testType_ID, user_ID, total_score, timestamp) VALUES (?, ?, ?, ?, ?)";

// เตรียมคำสั่ง SQL
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("ไม่สามารถเตรียมคำสั่ง SQL ได้: " . mysqli_error($conn));
}

// ผูกพารามิเตอร์
mysqli_stmt_bind_param($stmt, "iiiss", $lessonID, $testType_ID, $user_ID, $total_score, $timestamp);

// ตัวแปรสำหรับเก็บข้อความผลลัพธ์
$resultMessage = '';

if (mysqli_stmt_execute($stmt)) {
    // บันทึกสำเร็จ
    $resultMessage = "บันทึกคะแนนเรียบร้อยแล้ว! คะแนนรวมของคุณคือ: $total_score";
} else {
    // บันทึกไม่สำเร็จ
    $resultMessage = "เกิดข้อผิดพลาดในการบันทึกคะแนน: " . mysqli_error($conn);
}

// ปิดการเชื่อมต่อฐานข้อมูล
mysqli_stmt_close($stmt);
mysqli_close($conn);
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
  <?php include 'rating.php';?>
</body>

</html>