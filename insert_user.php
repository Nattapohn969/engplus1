<?php
include 'connect1.php';

// รับค่า
$name = $_POST['name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$c_password = $_POST['c_password'];
$role= 'User';


// ตรวจสอบ email ซ้ำ
$sql1 = "SELECT * FROM users WHERE email = '$email'";
$result1 = mysqli_query($conn, $sql1);
$count1 = mysqli_fetch_assoc($result1);
if ($count1 > 0) {
  echo "<script>alert('Email นี้ถูกใช้งานแล้ว');</script>";
  echo "<script>location ='register1.html';</script>";
  exit;
}

// ตรวจสอบ username ซ้ำ
$sql2 = "SELECT * FROM users WHERE username = '$username'";
$result2 = mysqli_query($conn, $sql2);
$count2 = mysqli_fetch_assoc($result2);
if($count2 > 0) {
  echo "<script>alert('Username นี้ถูกใช้งานแล้ว');</script>";
  echo "<script>location ='register1.html';</script>";
  exit;
}

// ตรวจสอบรหัสผ่านที่ยืนยัน
if ($password != $c_password) {
    echo "<script>alert('รหัสผ่านไม่ตรงกัน');</script>";
    echo "<script>location ='register1.html';</script>";
    exit;
} 
// แปลงรหัสเป็นค่าแฮช
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// บันทึกข้อมูล
$sql = "INSERT INTO users(name,email,username,password,role) VALUES('$name', '$email', '$username', '$hashedPassword', '$role')";
$result = mysqli_query($conn,$sql);

// ตรวจสอบการบันทึก
if($result) {
    echo "<script>alert('บันทึกข้อมูลเรียบร้อย');</script>";
    echo "<script>location ='index.html';</script>";
} else {
    echo "<script>alert('ไม่สามารถบันทึกข้อมูลได้');</script>";
}

mysqli_close($conn);

?>