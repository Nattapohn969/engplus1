<?php
session_start();

// Check if the user is logged in and has the Learner role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Learner') {
    header('Location: login.php');
    exit;
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);
$user_ID = $_SESSION['user_ID'];


include 'connect.php'; // Connect to the database

// Fetch favorite lessons from the database
$sql = "SELECT lessons.lessonID, lessons.lessonName, lessons.cover_image 
        FROM favorite_lesson 
        JOIN lessons ON favorite_lesson.lessonID = lessons.lessonID 
        WHERE favorite_lesson.user_ID = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('i', $user_ID);

if (!$stmt->execute()) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="MycoursesPage.css" rel="stylesheet" />
    <title>user report</title>
</head>
<style>
    /* รูปแบบสำหรับ container */
    .page-container {
        background-color: white;
        /* สีพื้นหลังของ container */
        padding: 20px;
        /* ช่องว่างภายใน */
        border-radius: 8px;
        /* มุมมน */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        /* เงา */
        width: 100%;
        /* ให้เต็มความกว้าง */
        max-width: 600px;
        /* จำกัดความกว้างสูงสุด */
    }

    .flax {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* รูปแบบสำหรับหัวข้อ */
    h1 {
        text-align: center;
        /* จัดกลางข้อความ */
        color: #333;
        /* สีข้อความ */
        margin-bottom: 20px;
        /* ช่องว่างด้านล่าง */
    }

    /* รูปแบบสำหรับ textarea */
    textarea {
        width: 100%;
        /* ให้เต็มความกว้าง */
        height: 150px;
        /* ความสูงของ textarea */
        padding: 10px;
        /* ช่องว่างภายใน */
        border: 1px solid #ccc;
        /* กรอบ */
        border-radius: 4px;
        /* มุมมน */
        resize: none;
        /* ปิดการปรับขนาด */
        font-size: 16px;
        /* ขนาดฟอนต์ */
        box-sizing: border-box;
        /* ให้นับ padding และ border ใน width */
    }

    /* รูปแบบสำหรับปุ่มส่งรายงาน */
    input[type="submit"] {
        background-color: #5cb85c;
        /* สีพื้นหลังของปุ่ม */
        color: white;
        /* สีข้อความของปุ่ม */
        border: none;
        /* ไม่มีกรอบ */
        padding: 10px 15px;
        /* ช่องว่างภายใน */
        cursor: pointer;
        /* เปลี่ยนเคอร์เซอร์เป็น pointer */
        border-radius: 4px;
        /* มุมมน */
        font-size: 16px;
        /* ขนาดฟอนต์ */
        transition: background-color 0.3s;
        /* เพิ่มการเปลี่ยนสี */
    }

    /* เปลี่ยนสีปุ่มเมื่อชี้เมาส์ */
    input[type="submit"]:hover {
        background-color: #4cae4c;
        /* สีใหม่เมื่อชี้เมาส์ */

    }
</style>

<body>
    <div class='navbar'>
        <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'>
        <div class='innavbar'>
            <!-- <ul><a href='HomePage.php' class='blacktext' style="margin-right: 5px">Home</a></ul> -->
            <ul><a href='CoursesPage.php' class='blacktext' style="margin-right: 5px">Courses</a></ul>
            <ul><a href='MycoursesPage.php' class='blacktext' style="margin-right: 5px">My Courses</a></ul>
            <ul><a href='#' class='blacktext' style="margin-right: 5px">Transform</a></ul>
            <!-- <ul><a href='edit1.php' class='blacktext' style="margin-right: 30px">Profile</a></ul> -->
            <div id="accountMenu" class="dropdown">
                <button class="dropbtn" id="accountButton">
                    <?php
                    echo htmlspecialchars($_SESSION['username']);
                    // แสดงบทบาทของผู้ใช้
                    if (isset($_SESSION['role'])) {
                        echo " (" . htmlspecialchars($_SESSION['role']) . ")";
                    }
                    ?>
                </button>
                <div id="dropdownContent" class="dropdown-content">
                    <a href="edit1.php">Profile</a>
                    <a href="report.php">Report</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>

        </div>
    </div>

    <div class="flax">
        <div class="page-container">
            <h1>รายงานข้อผิดพลาด</h1>
            <form action="submit_report.php" method="POST">
                <textarea name="report_text" required placeholder="กรุณาใส่รายละเอียดการรายงาน"></textarea>
                <input type="submit" value="ส่งรายงาน">
            </form>
        </div>
    </div>

    <script>

    </script>
</body>

</html>