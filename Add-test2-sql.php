<?php
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lessonID = $_POST['lessonID'];
    $testType_ID = 2; // ประเภทแบบทดสอบเป็น 2
    $score = 1; // กำหนดคะแนนเป็น 1 โดยค่าเริ่มต้น

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO test2 (lessonID, testType_ID, word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8, word_9, word_10, score) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Loop through each question's words
    $allSuccess = true; // Track if all inserts were successful
    for ($i = 1; $i <= 5; $i++) {
        // Get the words for this question
        $words = isset($_POST['word_' . $i]) ? $_POST['word_' . $i] : [];

        // Set default values for any missing words
        $w1 = isset($words[0]) ? $words[0] : '';
        $w2 = isset($words[1]) ? $words[1] : '';
        $w3 = isset($words[2]) ? $words[2] : '';
        $w4 = isset($words[3]) ? $words[3] : '';
        $w5 = isset($words[4]) ? $words[4] : '';
        $w6 = isset($words[5]) ? $words[5] : '';
        $w7 = isset($words[6]) ? $words[6] : '';
        $w8 = isset($words[7]) ? $words[7] : '';
        $w9 = isset($words[8]) ? $words[8] : '';
        $w10 = isset($words[9]) ? $words[9] : '';

        // Bind parameters and execute the statement
        $stmt->bind_param("iissssssssssi", $lessonID, $testType_ID, $w1, $w2, $w3, $w4, $w5, $w6, $w7, $w8, $w9, $w10, $score);

        if (!$stmt->execute()) {
            error_log("Error executing statement: " . $stmt->error); // Log error to a file
            $allSuccess = false; // At least one insert failed
            break;
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Determine the final result
    if ($allSuccess) {
        header('Location: lessons_manage.php'); // Redirect to lessons_manage.php after success
        exit(); // Ensure no further code is executed
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']); // Redirect back to the previous page
        exit();
    }
}
?>
