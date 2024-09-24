<?php
// เริ่มต้นด้วยการเชื่อมต่อฐานข้อมูล
include 'connect.php';

// ตรวจสอบว่ามีค่า lessonID ส่งมาหรือไม่ ถ้าไม่มีกำหนดค่าเริ่มต้นเป็น 1
$lessonID = isset($_GET['lessonID']) ? intval($_GET['lessonID']) : 1;

// กำหนดคำสั่ง SQL เพื่อดึงคำจากตาราง test2 ที่มี lessonID ตรงกับค่าที่กำหนด
$query = "SELECT word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8, word_9, word_10 FROM test2 WHERE lessonID = ?";

// เตรียมคำสั่ง SQL
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    // หากเกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL ให้แสดงข้อความและหยุดการทำงาน
    die("ไม่สามารถเตรียมคำสั่ง SQL ได้: " . mysqli_error($conn));
}

// ผูกค่า lessonID เข้ากับคำสั่ง SQL
mysqli_stmt_bind_param($stmt, "i", $lessonID);

// ดำเนินการคำสั่ง SQL
mysqli_stmt_execute($stmt);

// ดึงผลลัพธ์จากการดำเนินการคำสั่ง SQL
$result = mysqli_stmt_get_result($stmt);

// สร้างอาร์เรย์เพื่อเก็บคำถาม
$questions = array();
while ($row = mysqli_fetch_assoc($result)) {
    // เพิ่มข้อมูลที่ดึงได้ในแต่ละแถวเข้าไปในอาร์เรย์ $questions
    $questions[] = $row;
}

// ปิดการใช้งาน statement และการเชื่อมต่อฐานข้อมูล
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การฝึกเรียงลำดับคำ</title>
    <link href="test2.css" rel="stylesheet" />
</head>

<body>
    <div id="questions-container">
        <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-block">
                    <div class="question-number">Question <?php echo $index + 1; ?></div>
                    <div id="words-container-<?php echo $index; ?>"></div>
                    <div id="answer-box-<?php echo $index; ?>"></div>
                    <button id="reset-button-<?php echo $index; ?>" onclick="reset(<?php echo $index; ?>)">รีเซ็ต</button>
                    <div id="feedback-<?php echo $index; ?>" class="question-feedback"></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ไม่พบคำถามสำหรับ lessonID ที่ระบุ.</p>
        <?php endif; ?>
    </div>

    <button id="check-answers-button" onclick="checkAllAnswers()">ตรวจสอบคำตอบทั้งหมด</button>

    <script>
        // สร้างตัวแปร questions ขึ้นจากข้อมูลที่ดึงมาจาก PHP
        const questions = <?php echo json_encode($questions); ?>;
        const lessonID = <?php echo $lessonID; ?>; // ส่งค่า lessonID จาก PHP ไปยัง JavaScript
        // เตรียมคำตอบที่ถูกต้องโดยกรองคำที่มีอยู่และลบช่องว่าง
        const correctAnswers = questions.map(question => {
            return Object.values(question).filter(word => word).map(word => word.trim());
        });

        // ฟังก์ชันแสดงคำในแต่ละคำถาม
        function displayWords(questionIndex) {
            // ดึงคำที่ไม่ใช่ค่า null ออกมาและทำการสับเรียงลำดับใหม่แบบสุ่ม
            const words = Object.values(questions[questionIndex]).filter(word => word).map(word => word.trim()).slice(0, 10); 
            const shuffledWords = words.sort(() => 0.5 - Math.random());
            const wordsContainer = document.getElementById('words-container-' + questionIndex);
            wordsContainer.innerHTML = ''; 
            shuffledWords.forEach(word => {
                const wordElement = document.createElement('div');
                wordElement.classList.add('word');
                wordElement.innerText = word;
                wordElement.onclick = () => addToAnswer(wordElement, questionIndex);
                wordsContainer.appendChild(wordElement);
            });
        }

        // ฟังก์ชันเพิ่มคำไปยังช่องคำตอบ
        function addToAnswer(wordElement, questionIndex) {
            const answerBox = document.getElementById('answer-box-' + questionIndex);
            const wordClone = wordElement.cloneNode(true); // ทำสำเนาคำ
            answerBox.appendChild(wordClone); // เพิ่มคำไปที่กล่องคำตอบ
            wordElement.classList.add('used'); // ทำเครื่องหมายว่าคำนี้ถูกใช้แล้ว
            wordElement.onclick = null; // ปิดการใช้งานคลิกซ้ำ
        }

        // ฟังก์ชันรีเซ็ตคำตอบ
        function reset(questionIndex) {
            const answerBox = document.getElementById('answer-box-' + questionIndex);
            const wordsContainer = document.getElementById('words-container-' + questionIndex);
            const words = Array.from(wordsContainer.children);

            answerBox.innerHTML = ''; // ล้างกล่องคำตอบ

            words.forEach(wordElement => {
                wordElement.classList.remove('used'); // นำเครื่องหมายว่าใช้แล้วออก
                wordElement.onclick = () => addToAnswer(wordElement, questionIndex); // เปิดการใช้งานการคลิกอีกครั้ง
            });
        }

        // ฟังก์ชันตรวจสอบคำตอบทั้งหมด
        function checkAllAnswers() {
            let score = 0; // กำหนดตัวแปรคะแนนเริ่มต้นเป็น 0

            questions.forEach((_, index) => {
                const answerBox = document.getElementById('answer-box-' + index);
                const selectedWords = Array.from(answerBox.children).map(el => el.innerText.trim());
                const correctOrder = correctAnswers[index];

                // ตรวจสอบว่าคำที่เลือกเรียงลำดับตรงกับคำตอบที่ถูกต้องหรือไม่
                if (selectedWords.join(' ') === correctOrder.join(' ')) {
                    document.getElementById('feedback-' + index).innerText = 'ถูกต้อง!';
                    score++; // เพิ่มคะแนนถ้าตอบถูก
                } else {
                    document.getElementById('feedback-' + index).innerText = 'ไม่ถูกต้อง ลองใหม่อีกครั้ง.';
                }
            });

            // ส่งคะแนนและ lessonID ไปยัง result.php
            window.location.href = `results2.php?score=${score}&lessonID=${lessonID}`;
        }

        // เรียกฟังก์ชันแสดงคำเมื่อโหลดหน้าเว็บ
        window.onload = () => {
            questions.forEach((_, index) => displayWords(index));
        };
    </script>
</body>

</html>
