<?php
include 'connect.php';  // เชื่อมต่อฐานข้อมูล

// ดึง lessonID และ lessonName จากตาราง lessons
$sql = "SELECT lessonID, lessonName FROM lessons";
$result = $conn->query($sql);

$lessons = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }
}

$lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
$lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>เพิ่มแบบฝึกหัดเรียงคำ</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h1>เพิ่มแบบฝึกหัดเรียงคำ</h1>
        
        <div class="lesson-name-container">
            <input type="hidden" id="lessonID" name="lessonID" value="<?php echo $lessonID; ?>" />
            <input type="hidden" id="testType_ID" name="testType_ID" value="1" />
            <p><strong>บทเรียน:</strong> <?php echo $lessonName; ?></p>
        </div>

        <form action="test2_insert.php" method="POST">
            <div class="form-group">
                <label for="words">คำที่ต้องเรียง:</label>
                <textarea id="words" name="words" required></textarea>
                <small>ใส่คำที่ต้องเรียงแต่ละคำในบรรทัดใหม่</small>
            </div>

            <div class="form-group">
                <label for="correct_order">ลำดับที่ถูกต้อง:</label>
                <textarea id="correct_order" name="correct_order" required></textarea>
                <small>ใส่ลำดับของคำที่ถูกต้องในบรรทัดใหม่</small>
            </div>

            <div class="form-group">
                <button type="submit">บันทึก</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
