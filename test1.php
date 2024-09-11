<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>ทำแบบทดสอบ</title>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 15px;
            background-color: #fff;
            border-radius: 6px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            color: #333;
        }

        .lesson-name {
            margin-bottom: 20px;
            font-size: 16px;
            color: #555;
        }

        .question-block {
            border: 1px solid #ddd;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 4px;
            background-color: #fafafa;
        }

        .question-number {
            font-weight: bold;
            color: #555;
            font-size: 16px;
        }

        .choice {
            margin-left: 20px;
            margin-bottom: 5px;
        }

        .choice input {
            margin-right: 10px;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ทำแบบทดสอบ</h1>
        </div>

        <!-- แสดงชื่อบทเรียน -->
        <div class="lesson-name">
            <?php
            $lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
            $lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';

            echo "<p><strong>บทเรียน:</strong> $lessonName</p>";
            ?>
        </div>

        <!-- แบบฟอร์มสำหรับตอบแบบทดสอบ -->
        <form action="submit_test.php" method="POST">
            <input type="hidden" name="lessonID" value="<?php echo $lessonID; ?>">

            <!-- ดึงคำถามจากฐานข้อมูลและแสดง -->
            <?php
            include 'connect.php';

            // ดึงคำถามที่เกี่ยวข้องกับ lessonID
            $sql = "SELECT * FROM test1 WHERE lessonID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $lessonID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $questionNumber = 1;

                // แสดงคำถามทีละข้อ
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='question-block'>";
                    echo "<span class='question-number'>คำถาม $questionNumber:</span> " . htmlspecialchars($row['question']) . "<br><br>";

                    // แสดงตัวเลือก A, B, C, D ด้วย radio buttons
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='A' required> A: " . htmlspecialchars($row['choice_A']) . "</label></div>";
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='B'> B: " . htmlspecialchars($row['choice_B']) . "</label></div>";
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='C'> C: " . htmlspecialchars($row['choice_C']) . "</label></div>";
                    echo "<div class='choice'><label><input type='radio' name='answer_$questionNumber' value='D'> D: " . htmlspecialchars($row['choice_D']) . "</label></div>";

                    echo "</div>";
                    echo "<div class='question-divider'></div>";

                    $questionNumber++;
                }
            } else {
                echo "<p>ไม่มีคำถามในแบบทดสอบสำหรับบทเรียนนี้</p>";
            }

            $stmt->close();
            $conn->close();
            ?>

            <button type="submit" class="submit-btn">ส่งคำตอบ</button>
        </form>
    </div>
</body>

</html>
