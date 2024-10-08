<?php
session_start();
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

if (isset($_GET['report_id'])) {
    $reportId = $_GET['report_id'];

    // อัพเดตสถานะรายงาน
    $conn->query("UPDATE user_reports SET status='read' WHERE report_id=$reportId");

    // เปลี่ยนเส้นทางกลับไปยังหน้ารายงาน
    header("Location: get_report.php");
    exit();
}
?>
