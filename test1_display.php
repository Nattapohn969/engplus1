<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>แสดงคำถามในแบบทดสอบ</title>
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

        .answer {
            font-weight: bold;
            color: #4CAF50;
        }

        .question-divider {
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>คำถามในแบบทดสอบ</h1>
        </div>

        <!-- แสดงชื่อบทเรียน -->
        <div class="lesson-name">
            <?php
            $lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
            $lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';

            echo "<p><strong>บทเรียน:</strong> $lessonName</p>";
            ?>
        </div>

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
                
                echo "<div class='choice'><strong>A:</strong> " . htmlspecialchars($row['choice_A']) . "</div>";
                echo "<div class='choice'><strong>B:</strong> " . htmlspecialchars($row['choice_B']) . "</div>";
                echo "<div class='choice'><strong>C:</strong> " . htmlspecialchars($row['choice_C']) . "</div>";
                echo "<div class='choice'><strong>D:</strong> " . htmlspecialchars($row['choice_D']) . "</div>";
                
                echo "<div class='answer'>คำตอบที่ถูกต้อง: " . htmlspecialchars($row['answer']) . "</div>";
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
    </div>
</body>

</html>
