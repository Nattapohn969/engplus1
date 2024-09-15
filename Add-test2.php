<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="css/stylead.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="css/stylead.css" rel="stylesheet">
    <title>เพิ่มคำถามในแบบทดสอบ</title>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
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
            font-size: 24px;
            color: #333;
        }

        .lesson-name-container {
            margin-bottom: 15px;
            font-size: 16px;
            color: #555;
        }

        .question-block {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f7f7f7;
            position: relative;
        }

        .question-number {
            font-weight: bold;
            color: #555;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .words {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }

        .words input {
            width: calc(10% - 10px);
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .submit-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #45a049;
        }

        .divider {
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="header">
            <h1>เพิ่มคำถามในแบบทดสอบใหม่</h1>
        </div>

        <div class="lesson-name-container">
            <?php
            $lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
            $lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';
            ?>
            <input type="hidden" id="lessonID" name="lessonID" value="<?php echo $lessonID; ?>" />
            <p><strong>บทเรียน:</strong> <?php echo $lessonName; ?></p>
        </div>


        <!-- Start of the form -->
        <form id="testForm" action='Add-test2-sql.php' method="post" enctype="multipart/form-data">
            <input type="hidden" name="lessonID" value="<?php echo $lessonID; ?>" />
            <!-- Create each question block -->
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="question-block">
                    <div class="question-number">Question <?php echo $i; ?></div>
                    <div class="words">
                        <?php for ($j = 1; $j <= 10; $j++): ?>
                            <input type="text" name="word_<?php echo $i; ?>[]" placeholder="Word <?php echo $j; ?>" />
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="divider"></div>
            <?php endfor; ?>

            <!-- Save button -->
            <div class="btn-container">
                <button type="submit" class="submit-btn">Save Lesson</button>
            </div>
        </form>
    </div>
</body>

</html>