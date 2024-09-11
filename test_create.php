<?php
include 'connect.php';

// Retrieve lessonID, lessonName, and testType from query parameters
$lessonID = isset($_GET['lessonID']) ? htmlspecialchars(trim($_GET['lessonID'])) : '';
$lessonName = isset($_GET['lessonName']) ? htmlspecialchars(trim($_GET['lessonName'])) : '';
$testType = isset($_GET['testType']) ? htmlspecialchars(trim($_GET['testType'])) : '';

if (!$lessonID || !$lessonName || !$testType) {
    // Redirect to an error page or show a user-friendly message
    die('Invalid parameters.');
}

// Check if testType is valid
$validTestTypes = ['test1', 'test2'];
if (!in_array($testType, $validTestTypes)) {
    die('Invalid test type.');
}

// Proceed with creating the test based on the testType
if ($testType === 'test1') {
    header("Location: test1_add.php?lessonID=" . urlencode($lessonID) . "&lessonName=" . urlencode($lessonName));
} elseif ($testType === 'test2') {
    header("Location: test2_add.php?lessonID=" . urlencode($lessonID) . "&lessonName=" . urlencode($lessonName));
}

// Make sure to exit after redirect to prevent further script execution
exit();
?>
