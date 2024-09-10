<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>แก้ไขคำถามในแบบทดสอบ</title>
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

        label {
            display: block;
            margin-bottom: 4px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        textarea {
            width: calc(100% - 18px);
            height: 50px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        select {
            width: calc(100% - 18px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .save-btn {
            display: block;
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .save-btn:hover {
            background-color: #45a049;
        }

        .btn-container {
            text-align: center;
        }

        .btn-container button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #007BFF;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-container button:hover {
            background-color: #0056b3;
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
            <h1>แก้ไขคำถามในแบบทดสอบ</h1>
        </div>

        <!-- แสดงชื่อบทเรียน -->
        <div class="lesson-name">
            <?php
            // ตรวจสอบว่ามีการส่งค่า lessonID และ lessonName มาจาก URL หรือไม่
            $lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
            $lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';

            // แสดงชื่อบทเรียน หากพบค่า lessonName
            if (!empty($lessonName)) {
                echo "<p><strong>บทเรียน:</strong> $lessonName</p>";
            } else {
                echo "<p><strong>บทเรียน:</strong> ไม่พบข้อมูลชื่อบทเรียน</p>";
            }
            ?>
        </div>

        <!-- ฟอร์มสำหรับแก้ไขคำถาม -->
        <form action="test1_update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="lessonID" value="<?php echo $lessonID; ?>">
            <div id="questions-container">
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
                        echo "<input type='hidden' name='test1_ID[]' value='" . $row['test1_ID'] . "'>";

                        echo "<label>คำถาม $questionNumber:</label>";
                        echo "<textarea name='question[]'>" . htmlspecialchars($row['question']) . "</textarea><br><br>";

                        echo "<label>ตัวเลือก A:</label>";
                        echo "<textarea name='choice_a[]'>" . htmlspecialchars($row['choice_A']) . "</textarea><br><br>";

                        echo "<label>ตัวเลือก B:</label>";
                        echo "<textarea name='choice_b[]'>" . htmlspecialchars($row['choice_B']) . "</textarea><br><br>";

                        echo "<label>ตัวเลือก C:</label>";
                        echo "<textarea name='choice_c[]'>" . htmlspecialchars($row['choice_C']) . "</textarea><br><br>";

                        echo "<label>ตัวเลือก D:</label>";
                        echo "<textarea name='choice_d[]'>" . htmlspecialchars($row['choice_D']) . "</textarea><br><br>";

                        echo "<label>คำตอบ:</label>";
                        echo "<select name='answer[]'>";
                        echo "<option value='A' " . ($row['answer'] == 'A' ? "selected" : "") . ">ตัวเลือก A</option>";
                        echo "<option value='B' " . ($row['answer'] == 'B' ? "selected" : "") . ">ตัวเลือก B</option>";
                        echo "<option value='C' " . ($row['answer'] == 'C' ? "selected" : "") . ">ตัวเลือก C</option>";
                        echo "<option value='D' " . ($row['answer'] == 'D' ? "selected" : "") . ">ตัวเลือก D</option>";
                        echo "</select><br><br>";

                        echo "<input type='hidden' name='score[]' value='1'>";
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

            <div class="btn-container">
                <button type="submit" class="save-btn">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</body>

</html>
