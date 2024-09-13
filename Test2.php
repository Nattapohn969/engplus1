<?php
include 'connect.php';

// รับค่า lessonID จาก URL (หรือใช้ค่าเริ่มต้นเป็น 1)
$lessonID = isset($_GET['lessonID']) ? intval($_GET['lessonID']) : 1;

// สร้างคำสั่ง SQL เพื่อดึงคำถามทั้งหมดสำหรับ lessonID ที่ระบุ
$query = "SELECT word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8, word_9, word_10 FROM test2 WHERE lessonID = ?";

// เตรียมคำสั่ง SQL
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("ไม่สามารถเตรียมคำสั่ง SQL ได้: " . mysqli_error($conn));
}

// ผูกพารามิเตอร์
mysqli_stmt_bind_param($stmt, "i", $lessonID);

// ดำเนินการคำสั่ง
mysqli_stmt_execute($stmt);

// รับผลลัพธ์
$result = mysqli_stmt_get_result($stmt);

$questions = array();
while ($row = mysqli_fetch_assoc($result)) {
    $questions[] = $row;
}

// ปิดการเชื่อมต่อฐานข้อมูล
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .word {
            display: inline-block;
            padding: 10px;
            margin: 5px;
            background-color: #c1e1c1;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .word:hover {
            background-color: #a0d7a4;
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
            background-color: #fafafa;
            border-radius: 4px;
        }

        #reset-button {
            margin-top: 10px;
            background-color: #f4b400;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #reset-button:hover {
            background-color: #e6a400;
        }

        .question-block {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f7f7f7;
        }

        .question-number {
            font-weight: bold;
            color: #555;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .question-feedback {
            margin-top: 10px;
            font-weight: bold;
            color: #e74c3c;
            /* สีแดงเพื่อแสดงข้อความผิดพลาด */
        }

        .feedback-correct {
            color: #2ecc71;
            /* สีเขียวเพื่อแสดงข้อความถูกต้อง */
        }

        #check-answers-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #check-answers-button:hover {
            background-color: #2980b9;
        }
    </style>
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
        // ข้อมูลจาก PHP
        const questions = <?php echo json_encode($questions); ?>;
        const correctAnswers = questions.map(question => {
            return Object.values(question).filter(word => word).map(word => word.trim());
        });

        function displayWords(questionIndex) {
            const words = Object.values(questions[questionIndex]).filter(word => word).map(word => word.trim()).slice(0, 10); // กรองและจำกัดเป็น 10 คำ
            const shuffledWords = words.sort(() => 0.5 - Math.random());
            const wordsContainer = document.getElementById('words-container-' + questionIndex);
            wordsContainer.innerHTML = ''; // ล้างคำที่มีอยู่
            shuffledWords.forEach(word => {
                const wordElement = document.createElement('div');
                wordElement.classList.add('word');
                wordElement.innerText = word;
                wordElement.onclick = () => addToAnswer(wordElement, questionIndex);
                wordsContainer.appendChild(wordElement);
            });
        }

        function addToAnswer(wordElement, questionIndex) {
            const answerBox = document.getElementById('answer-box-' + questionIndex);
            const wordClone = wordElement.cloneNode(true);
            answerBox.appendChild(wordClone);
            wordElement.classList.add('used');
            wordElement.onclick = null;
        }

        function reset(questionIndex) {
            const answerBox = document.getElementById('answer-box-' + questionIndex);
            const wordsContainer = document.getElementById('words-container-' + questionIndex);
            const words = Array.from(wordsContainer.children);

            answerBox.innerHTML = ''; // ล้างกล่องคำตอบ

            words.forEach(wordElement => {
                wordElement.classList.remove('used'); // ลบคลาส 'used'
                wordElement.onclick = () => addToAnswer(wordElement, questionIndex); // เปิดใช้งานการคลิก
            });
        }

        function checkAllAnswers() {
            let score = 0;

            questions.forEach((_, index) => {
                const answerBox = document.getElementById('answer-box-' + index);
                const selectedWords = Array.from(answerBox.children).map(el => el.innerText.trim());
                const correctOrder = correctAnswers[index];

                // แสดงข้อมูลเพื่อดีบัก
                console.log(`คำที่เลือก: ${selectedWords.join(' ')}`);
                console.log(`คำที่ถูกต้อง: ${correctOrder.join(' ')}`);

                // เปรียบเทียบคำที่เลือกกับคำที่ถูกต้อง
                if (selectedWords.join(' ') === correctOrder.join(' ')) {
                    document.getElementById('feedback-' + index).innerText = 'ถูกต้อง!';
                    score++;
                } else {
                    document.getElementById('feedback-' + index).innerText = 'ไม่ถูกต้อง ลองใหม่อีกครั้ง.';
                }
            });

            alert(`คะแนนรวมของคุณคือ ${score} จาก ${questions.length}`);
        }

        // เริ่มต้นคำถามทั้งหมดเมื่อหน้าเว็บโหลดเสร็จ
        window.onload = () => {
            questions.forEach((_, index) => displayWords(index));
        };
    </script>
</body>

</html>