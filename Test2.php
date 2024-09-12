<?php
include 'connect.php';

// รับค่า test2_ID จาก URL (หรือกำหนดค่าเอง)
$test2_ID = isset($_GET['id']) ? intval($_GET['id']) : 1; // กำหนดเป็น 1 ถ้าไม่มีค่าใน URL

// ดึงข้อมูลจากตาราง test2 ตาม test2_ID
$query = "SELECT word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8, word_9, word_10 FROM test2 WHERE test2_ID = ?";

// Prepare the statement
$stmt = mysqli_prepare($conn, $query);

// Bind the parameter
mysqli_stmt_bind_param($stmt, "i", $test2_ID);

// Execute the statement
mysqli_stmt_execute($stmt);

// Get the result
$result = mysqli_stmt_get_result($stmt);

$words = array();
if ($result && $row = mysqli_fetch_assoc($result)) {
    // สร้างอาร์เรย์ของคำที่มีค่า (ไม่เป็นค่าว่าง)
    foreach ($row as $key => $value) {
        if (!empty($value)) {
            $words[$key] = $value;
        }
    }
}

// ปิด statement และ connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การฝึกเรียงลำดับคำ</title>
    <style>
        .word {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            background-color: #c1e1c1;
            border-radius: 5px;
            cursor: pointer;
        }
        .word.used {
            background-color: #d3d3d3;
            cursor: not-allowed;
        }
        #answer-box {
            border: 2px dashed #ccc;
            height: 100px;
            padding: 10px;
            min-width: 300px;
            overflow: auto;
        }
        #reset-button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div id="words-container"></div>
    <div id="answer-box"></div>
    <button onclick="checkAnswer()">ตรวจสอบคำตอบ</button>
    <button id="reset-button" onclick="reset()">รีเซ็ต</button>

    <script>
        // รับข้อมูลคำจาก PHP ในรูปแบบ JSON
        const words = <?php echo json_encode(array_values($words)); ?>;

        // ผสมลำดับคำแบบสุ่มและแสดงในหน้าจอ
        function displayWords() {
            const shuffledWords = words.sort(() => 0.5 - Math.random());
            
            const wordsContainer = document.getElementById('words-container');
            wordsContainer.innerHTML = ''; // ล้างคำที่มีอยู่ก่อนหน้านี้
            shuffledWords.forEach(word => {
                const wordElement = document.createElement('div');
                wordElement.classList.add('word');
                wordElement.innerText = word;
                wordElement.onclick = () => addToAnswer(wordElement);
                wordsContainer.appendChild(wordElement);
            });
        }

        // เพิ่มคำที่เลือกลงใน answer box
        function addToAnswer(wordElement) {
            const answerBox = document.getElementById('answer-box');
            const wordClone = wordElement.cloneNode(true);
            answerBox.appendChild(wordClone);
            wordElement.classList.add('used');
            wordElement.onclick = null;
        }

        // ตรวจสอบคำตอบว่าลำดับถูกต้องหรือไม่
        function checkAnswer() {
            const answerBox = document.getElementById('answer-box');
            const selectedWords = Array.from(answerBox.children).map(el => el.innerText);

            if (selectedWords.join(' ') === words.join(' ')) {
                alert('ถูกต้อง!');
            } else {
                alert('ไม่ถูกต้อง ลองใหม่อีกครั้ง.');
            }
        }

        // รีเซ็ตคำตอบและทำให้คำในคำถัดไปคลิกได้อีกครั้ง
        function reset() {
            const answerBox = document.getElementById('answer-box');
            const wordsContainer = document.getElementById('words-container');
            const words = Array.from(wordsContainer.children);

            answerBox.innerHTML = ''; // ล้างกล่องคำตอบ

            words.forEach(wordElement => {
                wordElement.classList.remove('used'); // ลบคลาส 'used'
                wordElement.onclick = () => addToAnswer(wordElement); // เปิดการคลิกอีกครั้ง
            });
        }

        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเสร็จ
        window.onload = displayWords;
    </script>
</body>
</html>
