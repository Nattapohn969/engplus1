<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <style>
       form {
    display: flex;
    flex-direction: column; /* จัดเรียงในแนวตั้ง */
    max-width: 600px; /* ขนาดที่เหมาะสมสำหรับฟอร์ม */
    margin: 20px auto;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    background-color: #f9f9f9;
}

.form-group {
    margin-bottom: 15px;
}

label {
    margin-bottom: 5px;
    font-weight: bold;
}

input[type="text"] {
    padding: 8px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
}

input[type="submit"] {
    background-color: #28a745; 
    color: white; 
    padding: 10px 20px; 
    font-size: 16px; 
    border: none;
    border-radius: 5px; 
    cursor: pointer; 
    transition: background-color 0.3s ease; 
}

input[type="submit"]:hover {
    background-color: #218838; 
}

@media (max-width: 768px) {
    form {
        width: 90%; /* ปรับขนาดฟอร์มบนหน้าจอขนาดเล็ก */
    }
}

    </style>
</head>
<body>
    <form method="POST" action="Add-test2-sql.php">
        <div class="lesson-name-container">
            <?php
            // รับค่าจาก query string (หรือที่มาจากการเชื่อมต่อฐานข้อมูล)
            $lessonID = isset($_GET['lessonID']) ? htmlspecialchars($_GET['lessonID']) : '';
            $lessonName = isset($_GET['lessonName']) ? htmlspecialchars($_GET['lessonName']) : '';
            ?>
            <input type="hidden" id="lessonID" name="lessonID" value="<?php echo $lessonID; ?>" />
            <input type="hidden" id="testType_ID" name="testType_ID" value="2" />
            <!-- กำหนด testType_ID เป็น 2 โดยค่าเริ่มต้น -->
            <p><strong>บทเรียน:</strong> <?php echo $lessonName; ?></p>
        </div>

        <div class="form-group">
            <label for="w1">Word 1:</label>
            <input type="text" id="w1" name="w1">
        </div>
        <div class="form-group">
            <label for="w2">Word 2:</label>
            <input type="text" id="w2" name="w2">
        </div>
        <div class="form-group">
            <label for="w3">Word 3:</label>
            <input type="text" id="w3" name="w3">
        </div>
        <div class="form-group">
            <label for="w4">Word 4:</label>
            <input type="text" id="w4" name="w4">
        </div>
        <div class="form-group">
            <label for="w5">Word 5:</label>
            <input type="text" id="w5" name="w5">
        </div>
        <div class="form-group">
            <label for="w6">Word 6:</label>
            <input type="text" id="w6" name="w6">
        </div>
        <div class="form-group">
            <label for="w7">Word 7:</label>
            <input type="text" id="w7" name="w7">
        </div>
        <div class="form-group">
            <label for="w8">Word 8:</label>
            <input type="text" id="w8" name="w8">
        </div>
        <div class="form-group">
            <label for="w9">Word 9:</label>
            <input type="text" id="w9" name="w9">
        </div>
        <div class="form-group">
            <label for="w10">Word 10:</label>
            <input type="text" id="w10" name="w10">
        </div>

        <input type="hidden" name="score[]" value="1"/>

        <input type="submit" value="Submit">
    </form>
</body>
</html>
