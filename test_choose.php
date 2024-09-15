<?php
include 'connect.php';

// Retrieve values from the GET parameters
$lessonID = isset($_GET['lessonID']) ? $_GET['lessonID'] : '';
$lessonName = isset($_GET['lessonName']) ? $_GET['lessonName'] : '';

// Ensure that lessonName is sanitized before using it
$lessonName = htmlspecialchars($lessonName);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/stylead.css" rel="stylesheet">
    <title>Choose Test</title>
    <style>
        /* Navbar styling */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #90cedf;
            color: #fff;
            padding: 15px 0;
            text-align: center;
            z-index: 1000;
            /* Ensures it stays above other content */
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: inline-block;
            font-size: 18px;
        }

        .navbar a:hover {
            background-color: #00796b;
            border-radius: 4px;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            padding-top: 70px; /* Adjusted for navbar height */
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            margin-top: 20px; /* Space between navbar and container */
        }

        .container h1 {
            color: #00796b;
        }

        .container p {
            font-size: 18px;
            margin: 10px 0;
        }

        .container button {
            background-color: #00796b;
            border: none;
            color: #fff;
            padding: 15px 25px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .container button:hover {
            background-color: #004d40;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Lesson Created Successfully</h1>
        <p>Lesson Name: <?php echo $lessonName; ?></p>
        <p>Select which test to create for this lesson:</p>
        <form action="test_create.php" method="get">
            <input type="hidden" name="lessonID" value="<?php echo $lessonID; ?>">
            <input type="hidden" name="lessonName" value="<?php echo $lessonName; ?>">
            <button type="submit" name="testType_ID" value="1">Create Test 1</button>
            <button type="submit" name="testType_ID" value="2">Create Test 2</button>
        </form>
    </div>
</body>

</html>
