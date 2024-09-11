<?php session_start();
require_once "connect.php"; // แสดงข้อผิดพลาดของ PHP ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (
  isset($_POST['username']) &&
  isset($_POST['password'])
) {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = $_POST['password']; // ตรวจสอบว่ามี username และ password ถูกต้องหรือไม่
  $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    // ตั้งค่า session สำหรับผู้ใช้ที่เข้าสู่ระบบ
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_ID'] = $user['user_ID']; // เก็บ user_ID ใน session

    // ตรวจสอบว่า role เป็น Admin หรือไม่
    if ($user['role'] === 'Teacher') {
      header("Location: Teacher_dash.php");
      exit();
    } else if ($user['role'] === 'Learner') {
      header("Location: CoursesPage.php"); // ปรับ URL ตามหน้าที่คุณต้องการ
      exit();
    }
  } else {
    // ข้อมูลเข้าสู่ระบบไม่ถูกต้อง
    $_SESSION['error'] = 'Invalid username or password';
    header("Location: login.php");
    exit();
  }
}

// ถ้ายังไม่ได้ส่งข้อมูล POST หรือการล็อกอินล้มเหลว จะมาแสดง HTML ด้านล่างนี้
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="css/LoginPage.css">
  <title>ENG PLUS</title>
</head>

<body>
  <div class='navbar'>
    <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'></img>
    <div class='innavbar'>
      <ul><a href='HomePage.php' class='blacktext'>Home</a></ul>
      <ul><a href='CoursesPage.php' class='blacktext'>Courses</a></ul>
      <ul><a href='MycoursesPage.php' class='blacktext'>My Courses</a></ul>
      <ul><a href='#' class='blacktext'>Transform</a></ul>
      <ul><a href='register.php' class='blacktext'>Register</a></ul>
      <ul><a href='login.php' class='createacc'>Login</a></ul>
    </div>
  </div>

  </div>

  <form action="login.php" method="post">
    <div class='intro-login'>
      <div class='login-icon'>
        <img src='assets/img/Login-icon.png' width='550px' height='auto'></img>
      </div>
      <div class='login-01'>

        <div class='login-logo'>
          <img src='assets/img/LogoEngPlusNew.png' width='350px' height='auto'></img>
        </div>
        <form class='form-group' action="login.php" method="post"></form>
        <div class='login-form'>

          <div class="form-group-1">
            <label for="username">Username</label>
            <input class='field-1' type='text' id="username" name="username" required></input>
          </div>

          <div class="form-group-2">
            <label for="username">Password</label>
            <input class='field-2' type='password' id="password" name="password" required></input>
          </div>

        </div>
        <div class='login-forget'>
          <a href='#'>Forget Password</a>
        </div>
        <div class='yes-login'>
          <button type='submit' name='myButton'>เข้าสู่ระบบ</button>
        </div>
  </form>
</body>

</html>