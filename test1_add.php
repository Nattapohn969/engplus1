<?php
include 'connect.php'; // การเชื่อมต่อฐานข้อมูล

$sql = "SELECT lessonID, lessonName FROM lessons";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="css/stylead.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>เพิ่มคำถามในแบบทดสอบ</title>
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

        .lesson-name-container {
            margin-bottom: 15px;
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
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="header">
            <h1>เพิ่มคำถามในแบบทดสอบใหม่</h1>
        </div>

        <!-- ฟอร์มสำหรับเพิ่มคำถามในแบบทดสอบ -->
        <form id="testForm" action="test1_insert.php" method="post" enctype="multipart/form-data">
            <div class="lesson-name-container">
                <?php
                $lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
                $lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';
                ?>
                <input type="hidden" id="lessonID" name="lessonID" value="<?php echo $lessonID; ?>" />
                <input type="hidden" id="testType_ID" name="testType_ID" value="1" />
                <!-- กำหนด testType_ID เป็น 1 โดยค่าเริ่มต้น -->
                <p><strong>บทเรียน:</strong> <?php echo $lessonName; ?></p>
            </div>

            <div id="questions-container">
                <!-- สร้างบล็อกคำถาม 10 ข้อโดยอัตโนมัติ -->
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <div class="question-block">
                        <span class="question-number"><?php echo $i; ?></span>. <!-- หมายเลขคำถาม -->
                        <label for="question_<?php echo $i; ?>">คำถาม:</label>
                        <textarea id="question-editor-<?php echo $i; ?>" name="question[]"></textarea><br><br>

                        <label for="choice_a_<?php echo $i; ?>">ตัวเลือก A:</label>
                        <textarea id="choice-editor-a-<?php echo $i; ?>" name="choice_a[]"></textarea><br><br>

                        <label for="choice_b_<?php echo $i; ?>">ตัวเลือก B:</label>
                        <textarea id="choice-editor-b-<?php echo $i; ?>" name="choice_b[]"></textarea><br><br>

                        <label for="choice_c_<?php echo $i; ?>">ตัวเลือก C:</label>
                        <textarea id="choice-editor-c-<?php echo $i; ?>" name="choice_c[]"></textarea><br><br>

                        <label for="choice_d_<?php echo $i; ?>">ตัวเลือก D:</label>
                        <textarea id="choice-editor-d-<?php echo $i; ?>" name="choice_d[]"></textarea><br><br>

                        <label for="answer_<?php echo $i; ?>">คำตอบ:</label>
                        <select name="answer[]">
                            <option value="">-- เลือกคำตอบที่ถูกต้อง --</option>
                            <option value="A">ตัวเลือก A</option>
                            <option value="B">ตัวเลือก B</option>
                            <option value="C">ตัวเลือก C</option>
                            <option value="D">ตัวเลือก D</option>
                        </select><br><br>

                        <input type="hidden" name="score[]" value="1" />
                    </div>
                    <!-- เส้นแบ่งระหว่างคำถาม -->
                    <div class="question-divider"></div>
                <?php endfor; ?>
            </div>

            <div class="btn-container">
                <button type="submit" class="save-btn">บันทึกคำถามในแบบทดสอบ</button>
            </div>
        </form>
    </div>
</body>

</html>