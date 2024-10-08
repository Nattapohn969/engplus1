<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผลลัพธ์แบบทดสอบ</title>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header {
            text-align: center;
        }
        .result-message {
            text-align: center;
            font-size: 1.2rem;
            margin: 20px 0;
        }
        .back-to-lesson {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ผลลัพธ์แบบทดสอบ</h1>
            <h2>บทเรียน : </h2>
        </div>

        <div class="result-message">
            <p><?php echo $resultMessage; ?></p>
        </div>

        <!-- Back to Lesson Button -->
        <div class="back-to-lesson">
            <a href="lesson.php?lessonID=<?php echo $lessonID; ?>" class="btn">กลับไปที่บทเรียน</a>
        </div>
    </div>
</body>
</html>
