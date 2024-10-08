<?php
include 'connect.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// รับ lessonID จาก URL และตรวจสอบว่าเป็นค่าตัวเลข
$lessonID = isset($_GET['lessonID']) ? intval($_GET['lessonID']) : 0;
if ($lessonID <= 0) {
    die('Invalid lesson ID');
}

// ดึงข้อมูลบทเรียน
$stmt = $conn->prepare("SELECT * FROM lessons WHERE lessonID = ?");
$stmt->bind_param("i", $lessonID);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();
if (!$lesson) {
    die('Lesson not found');
}

// Retrieve testType_ID from the lesson data
$testType_ID = isset($lesson['testType_ID']) ? intval($lesson['testType_ID']) : 1;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Test Page</title>
    <link href="css/style1.css" rel="stylesheet" />
</head>
<style>
     h3 {
            text-align: center;
            margin: 20px;
            font-size: 15px;
            border: 2px solid black;
        }

        /* สร้างปุ่มย้อนกลับ */
        .back-button {
            position: absolute;
            left: 10px; /* ตำแหน่งซ้ายมือ */
            top: 20px; /* ระยะห่างจากขอบด้านบน */
            background-color: #4CAF50; /* สีพื้นหลัง */
            color: white; /* สีตัวอักษร */
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #45a049; /* เปลี่ยนสีเมื่อวางเมาส์ */
        }
</style>

<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-button">ย้อนกลับ</a>

        <h1>ทำแบบทดสอบ</h1>

        <?php
        // Determine the test page to include based on testType_ID
        if ($testType_ID == 1) {
            include 'test1.php';
        } elseif ($testType_ID == 2) {
            include 'Test2.php';
        } else {
            echo '<p>Test type not found.</p>';
        }
        ?>

    </div>
</body>

</html>

<?php
// No need to manually close the connection; PHP will handle it.
?>