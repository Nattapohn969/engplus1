<?php
session_start();
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_ID']; // สมมติว่ามีการเก็บ user_ID ใน session
    $reportText = $_POST['report_text'];

    // บันทึกข้อมูลรายงาน
    $stmt = $conn->prepare("INSERT INTO user_reports (user_ID, report_text) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $reportText);
    $stmt->execute();

    // ส่งอีเมลไปยัง Admin
    $to = "admin@example.com"; // อีเมล Admin
    $subject = "มีรายงานใหม่จากผู้ใช้";
    $message = "ผู้ใช้ ID: $userId\n\nรายงาน: $reportText";
    $headers = "From: no-reply@example.com";

    mail($to, $subject, $message, $headers);
    
    // แจ้งผู้ใช้ว่ารายงานถูกส่งแล้ว
    echo "<script>alert('รายงานของคุณถูกส่งเรียบร้อยแล้ว'); window.location.href='report.php';</script>";
}
?>
