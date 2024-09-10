<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "engplus";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error()); // แสดงข้อความข้อผิดพลาดเมื่อเชื่อมต่อล้มเหลว
} else {
}

// ปิดการเชื่อมต่อเมื่อไม่ต้องการใช้งาน
// mysqli_close($conn);
?>