<?php
include 'connect.php';

// รับคำตอบจากฟอร์ม
$selectedWords = isset($_POST['selectedWords']) ? $_POST['selectedWords'] : [];
$selectedWords = json_decode($selectedWords, true);

// ดึงข้อมูลจากตาราง test2
$query = "SELECT word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8, word_9, word_10 FROM test2 WHERE test2_ID = 56";
$result = mysqli_query($conn, $query);

$correctOrder = array();
if ($row = mysqli_fetch_assoc($result)) {
    foreach ($row as $value) {
        if (!empty($value)) {
            $correctOrder[] = $value;
        }
    }
}

// ตรวจสอบว่าคำตอบถูกต้องหรือไม่
if ($selectedWords === $correctOrder) {
    echo 'ถูกต้อง!';
} else {
    echo 'ไม่ถูกต้อง ลองใหม่อีกครั้ง.';
}
?>
