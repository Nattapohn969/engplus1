<?php
// เชื่อมต่อกับฐานข้อมูล
include 'connect.php';

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lessonID = isset($_POST['lessonID']) ? htmlspecialchars($_POST['lessonID']) : '';
    $userID = isset($_POST['user_ID']) ? htmlspecialchars($_POST['user_ID']) : ''; // Assume user ID is passed in form
    $testTypeID = isset($_POST['testType_ID']) ? htmlspecialchars($_POST['testType_ID']) : ''; // Assume test type ID is passed in form

    // ดึงคำถามและคำตอบที่ถูกต้องจากฐานข้อมูลสำหรับ lessonID นี้
    $sql = "SELECT * FROM test1 WHERE lessonID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lessonID);
    $stmt->execute();
    $result = $stmt->get_result();

    // เก็บคะแนนของผู้ใช้
    $score = 0;
    $totalQuestions = 0;

    // ตรวจสอบคำตอบที่ถูกต้อง
    while ($row = $result->fetch_assoc()) {
        $totalQuestions++;
        $questionNumber = $totalQuestions;
        $correctAnswer = $row['answer']; // Changed from 'answer' to 'correct_answer'

        // ชื่อของ input radio ที่ตรงกับคำถาม
        $userAnswer = isset($_POST['answer_' . $questionNumber]) ? $_POST['answer_' . $questionNumber] : '';

        // ตรวจสอบว่าคำตอบของผู้ใช้ตรงกับคำตอบที่ถูกต้องหรือไม่
        if ($userAnswer == $correctAnswer) {
            $score++;
        }
    }

    // ปิด connection ฐานข้อมูล
    $stmt->close();

    // คำนวณคะแนนเป็น 10 คะแนน
    $scaledScore = ($totalQuestions > 0) ? ($score / $totalQuestions) * 10 : 0;

    // บันทึกผลลัพธ์ในตาราง test_results
    $insertSQL = "INSERT INTO test_results (lessonID, testType_ID, user_ID, total_score, timestamp) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertSQL);
    $stmt->bind_param("iids", $lessonID, $testTypeID, $userID, $scaledScore);
    $stmt->execute();

    // ปิด connection ฐานข้อมูล
    $stmt->close();
    $conn->close();

    // แสดงผลลัพธ์
    echo "<div style='text-align: center; padding: 20px;'>";
    echo "<h1>ผลลัพธ์ของคุณ</h1>";
    echo "<p><strong>คุณตอบถูก:</strong> $score/$totalQuestions</p>";
    echo "<p><strong>คะแนนรวม:</strong> " . round($scaledScore, 2) . "/10</p>";

    // ข้อความให้กำลังใจ
    if ($scaledScore == 10) {
        echo "<p>ยอดเยี่ยม! คุณเก่งสุดๆ!</p>";
    } elseif ($scaledScore >= 8) {
        echo "<p>ดีมาก! คุณทำได้ดีจริงๆ</p>";
    } elseif ($scaledScore >= 5) {
        echo "<p>ดีแล้ว! แต่อาจจะต้องทบทวนอีกนิดหน่อย</p>";
    } else {
        echo "<p>ไม่เป็นไร! คุณจะเก่งขึ้นถ้าลองทำซ้ำ!</p>";
    }

    echo "<a href='index.php' style='display: block; margin-top: 20px; padding: 10px; background-color: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px;'>กลับไปหน้าหลัก</a>";
    echo "</div>";
} else {
    echo "ไม่มีข้อมูลถูกส่งมา!";
}
?>
