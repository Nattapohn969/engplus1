<?php
include 'connect.php';

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO test2 (lessonID, testType_ID, word_1, word_2, word_3, word_4, word_5, word_6, word_7, word_8, word_9, word_10, score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Binding the parameters
$stmt->bind_param("iissssssssssi", $lessonID, $testType_ID, $w1, $w2, $w3, $w4, $w5, $w6, $w7, $w8, $w9, $w10, $score);

// Set parameters
$lessonID = $_POST['lessonID'];
$testType_ID = 2; // Set testType_ID to 2
$w1 = $_POST['w1'];
$w2 = $_POST['w2'];
$w3 = $_POST['w3'];
$w4 = $_POST['w4'];
$w5 = $_POST['w5'];
$w6 = $_POST['w6'];
$w7 = $_POST['w7'];
$w8 = $_POST['w8'];
$w9 = $_POST['w9'];
$w10 = $_POST['w10'];
$score = 1; // The score is set to 1 by default

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
