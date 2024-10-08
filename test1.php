<?php
ob_start(); // เริ่มการบัฟเฟอร์เอาท์พุต

// รวมการเชื่อมต่อฐานข้อมูล
include 'connect.php';

// กำหนดตัวแปร
$lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
$lessonName = '';

// ดึงชื่อบทเรียนโดยใช้ lessonID
if ($lessonID) {
    $sqlLesson = "SELECT lessonName FROM lessons WHERE lessonID = ?"; // สร้างคำสั่ง SQL เพื่อดึงชื่อบทเรียน
    $stmtLesson = $conn->prepare($sqlLesson); // เตรียมคำสั่ง SQL
    $stmtLesson->bind_param("i", $lessonID); // ผูกค่าที่จะส่งเข้าไป
    $stmtLesson->execute(); // รันคำสั่ง SQL
    $resultLesson = $stmtLesson->get_result(); // รับผลลัพธ์จากการรันคำสั่ง SQL

    if ($resultLesson->num_rows > 0) { // ถ้ามีผลลัพธ์
        $lessonRow = $resultLesson->fetch_assoc(); // ดึงข้อมูลบทเรียน
        $lessonName = htmlspecialchars($lessonRow['lessonName']); // รับชื่อบทเรียนและป้องกัน XSS
    }
}

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if (isset($_POST['submit'])) {
    // รับคำตอบที่ถูกต้องจากฟอร์ม
    $correctAnswers = json_decode($_POST['correctAnswers'], true);
    $score = 0; // เริ่มต้นคะแนนเป็น 0
    $totalQuestions = count($correctAnswers); // จำนวนคำถามทั้งหมด

    // ตรวจสอบคำตอบของผู้ใช้กับคำตอบที่ถูกต้อง
    foreach ($correctAnswers as $questionNumber => $correctAnswer) {
        if (isset($_POST["answer_$questionNumber"]) && $_POST["answer_$questionNumber"] === $correctAnswer) {
            $score++; // เพิ่มคะแนนถ้าคำตอบถูกต้อง
        }
    }

    // เปลี่ยนเส้นทางไปยังหน้าผลลัพธ์พร้อมคะแนน, lessonID และจำนวนคำถาม
    header("Location: results1.php?score=$score&lessonID=$lessonID&total=$totalQuestions");
    exit(); // หยุดการทำงานของสคริปต์
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="test1.css" rel="stylesheet" />
    <title>ทำแบบทดสอบ</title>
    
</head>

<body>
    
    <div class="container">
        <!-- <div class="header">
            <h1>ทำแบบทดสอบ</h1> 
        </div> -->

        <!-- แสดงชื่อบทเรียน -->
        <div class="lesson-name">
            <?php
            if ($lessonName) {
                echo "<p><strong>บทเรียน:</strong> $lessonName </p>"; // แสดงชื่อบทเรียน
            } else {
                echo "<p><strong>บทเรียน:</strong> ไม่พบชื่อบทเรียน</p>"; // แสดงข้อความเมื่อไม่พบชื่อบทเรียน
            }
            ?>
        </div>

        <!-- ฟอร์มสำหรับตอบคำถามในแบบทดสอบ -->
        <form action="" method="POST">
            <input type="hidden" name="lessonID" value="<?php echo $lessonID; ?>"> <!-- ส่ง lessonID ผ่านฟอร์ม -->

            <!-- ดึงและแสดงคำถามจากฐานข้อมูล -->
            <?php
            // ดึงคำถามที่เกี่ยวข้องกับ lessonID
            $sql = "SELECT * FROM test1 WHERE lessonID = ?"; // สร้างคำสั่ง SQL
            $stmt = $conn->prepare($sql); // เตรียมคำสั่ง SQL
            $stmt->bind_param("i", $lessonID); // ผูกค่าที่จะส่งเข้าไป
            $stmt->execute(); // รันคำสั่ง SQL
            $result = $stmt->get_result(); // รับผลลัพธ์จากการรันคำสั่ง SQL
            
            if ($result->num_rows > 0) { // ถ้ามีคำถาม
                $questionNumber = 1; // เริ่มต้นหมายเลขคำถาม
                $correctAnswers = []; // สร้างอาเรย์สำหรับเก็บคำตอบที่ถูกต้อง
            
                // แสดงคำถามแต่ละข้อ
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='question-block'>"; // เริ่มบล็อกคำถาม
                    echo "<span class='question-number'>คำถาม $questionNumber:</span> " . htmlspecialchars($row['question']) . "<br><br>"; // แสดงคำถาม
            
                    // แสดงตัวเลือก A, B, C, D ด้วยปุ่มวิทยุ
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='A' required> <span class='choice-label'>A: " . htmlspecialchars($row['choice_A']) . "</span></label></div>";
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='B'> <span class='choice-label'>B: " . htmlspecialchars($row['choice_B']) . "</span></label></div>";
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='C'> <span class='choice-label'>C: " . htmlspecialchars($row['choice_C']) . "</span></label></div>";
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='D'> <span class='choice-label'>D: " . htmlspecialchars($row['choice_D']) . "</span></label></div>";

                    // เก็บคำตอบที่ถูกต้อง
                    $correctAnswers[$questionNumber] = $row['answer'];

                    echo "</div>"; // จบบล็อกคำถาม
                    echo "<div class='question-divider'></div>"; // แสดงเส้นแบ่งระหว่างคำถาม
            
                    $questionNumber++; // เพิ่มหมายเลขคำถาม
                }

                // เก็บคำตอบที่ถูกต้องเป็น input ซ่อนเพื่อส่งพร้อมฟอร์ม
                echo '<input type="hidden" name="correctAnswers" value="' . htmlspecialchars(json_encode($correctAnswers)) . '">';
            } else {
                echo "<div class='no-questions'>ไม่มีคำถามในแบบทดสอบสำหรับบทเรียนนี้</div>"; // ข้อความเมื่อไม่มีคำถาม
            }

            $stmt->close(); // ปิดการเตรียมคำสั่ง
            $stmtLesson->close(); // ปิดการเตรียมคำสั่งสำหรับชื่อบทเรียน
            $conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
            ?>

            <button type="submit" name="submit" class="submit-btn">ส่งคำตอบ</button> <!-- ปุ่มส่งคำตอบ -->
        </form>
    </div>
</body>

</html>

<?php ob_end_flush(); // สิ้นสุดการบัฟเฟอร์เอาท์พุตและส่งข้อมูลที่บัฟเฟอร์ออกไป ?>