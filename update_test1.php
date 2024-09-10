<?php
// เชื่อมต่อฐานข้อมูล
include 'connect.php';

// รับค่าจากฟอร์ม
$lessonID = isset($_POST['lessonID']) ? htmlspecialchars($_POST['lessonID']) : '';
$questions = isset($_POST['question']) ? $_POST['question'] : [];
$choices_a = isset($_POST['choice_a']) ? $_POST['choice_a'] : [];
$choices_b = isset($_POST['choice_b']) ? $_POST['choice_b'] : [];
$choices_c = isset($_POST['choice_c']) ? $_POST['choice_c'] : [];
$choices_d = isset($_POST['choice_d']) ? $_POST['choice_d'] : [];
$answers = isset($_POST['answer']) ? $_POST['answer'] : [];
$scores = isset($_POST['score']) ? $_POST['score'] : [];
$test1_IDs = isset($_POST['test1_ID']) ? $_POST['test1_ID'] : [];

// ตรวจสอบจำนวนข้อมูลให้ตรงกัน
if (count($questions) != count($choices_a) || count($questions) != count($choices_b) || count($questions) != count($choices_c) || count($questions) != count($choices_d) || count($questions) != count($answers) || count($questions) != count($scores) || count($questions) != count($test1_IDs)) {
    die("ข้อมูลที่ส่งมามีปัญหา");
}

// อัปเดตคำถามในฐานข้อมูล
$sql = "UPDATE test1 SET question = ?, choice_A = ?, choice_B = ?, choice_C = ?, choice_D = ?, answer = ?, score = ? WHERE test1_ID = ?";
$stmt = $conn->prepare($sql);

for ($i = 0; $i < count($questions); $i++) {
    $stmt->bind_param("ssssssii", $questions[$i], $choices_a[$i], $choices_b[$i], $choices_c[$i], $choices_d[$i], $answers[$i], $scores[$i], $test1_IDs[$i]);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// ส่งผู้ใช้ไปยังหน้าอื่นหลังจากอัปเดตเสร็จ
header("Location: manage_lessons.php");
exit;
?>
