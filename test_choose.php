<?php
include 'connect.php';

$sql = "SELECT lessonID, lessonName FROM lessons";
$result = $conn->query($sql);

// Retrieve lessonID and lessonName from query parameters
$lessonID = $_GET['lessonID'] ?? '';
$lessonName = $_GET['lessonName'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/stylead.css" rel="stylesheet">
    <title>Choose Test</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        button {
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            color: #fff;
        }

        button[value="test1"] {
            background-color: #3498db;
        }

        button[value="test1"]:hover {
            background-color: #2980b9;
        }

        button[value="test2"] {
            background-color: #e74c3c;
        }

        button[value="test2"]:hover {
            background-color: #c0392b;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Lesson Created Successfully</h1>
        <p>Lesson Name: <?php echo htmlspecialchars($lessonName); ?></p>
        <p>Select which test to create for this lesson:</p>
        <form action="test_create.php" method="get">
            <input type="hidden" name="lessonID" value="<?php echo htmlspecialchars($lessonID); ?>">
            <input type="hidden" name="lessonName" value="<?php echo htmlspecialchars($lessonName); ?>">
            <button type="submit" name="testType" value="test1">Create Test1</button>
            <button type="submit" name="testType" value="test2">Create Test2</button>
        </form>
    </div>
</body>

</html>