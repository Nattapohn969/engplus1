<?php
include 'connect.php';  // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $lessonID = isset($_POST['lessonID']) ? $conn->real_escape_string($_POST['lessonID']) : '';
    $words = isset($_POST['words']) ? $conn->real_escape_string($_POST['words']) : '';
    $correct_order = isset($_POST['correct_order']) ? $conn->real_escape_string($_POST['correct_order']) : '';

    // Set testType_ID to a fixed value of 2
    $testType_ID = 2;

    // Debugging output
    echo "lessonID: '$lessonID'<br>";
    echo "words: '$words'<br>";
    echo "correct_order: '$correct_order'<br>";
    echo "testType_ID: '$testType_ID'<br>";

    // Validate inputs
    if (empty($lessonID) || empty($words) || empty($correct_order)) {
        die('Error: Missing required fields.');
    }

    // Prepare SQL statement
    $sql = "INSERT INTO test2 (lessonID, words, correct_order, testType_ID) VALUES ('$lessonID', '$words', '$correct_order', '$testType_ID')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        // Improved error handling
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>