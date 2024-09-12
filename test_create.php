<?php
include 'connect.php';

// Retrieve values from the form
$lessonID = isset($_GET['lessonID']) ? $_GET['lessonID'] : '';
$lessonName = isset($_GET['lessonName']) ? $_GET['lessonName'] : '';
$testType_ID = isset($_GET['testType_ID']) ? $_GET['testType_ID'] : '';

if ($lessonID && $testType_ID) {
    // Sanitize lessonName
    $lessonName = htmlspecialchars($lessonName);

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE lessons SET testType_ID = ? WHERE LessonID = ?");
    $stmt->bind_param("ii", $testType_ID, $lessonID);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect based on the value of testType_ID
        if ($testType_ID == 1) {
            header("Location: test1_add.php?lessonID=" . urlencode($lessonID) . "&lessonName=" . urlencode($lessonName));
        } elseif ($testType_ID == 2) {
            header("Location: Add-test2.php?lessonID=" . urlencode($lessonID) . "&lessonName=" . urlencode($lessonName));
        } else {
            // Handle unexpected testType_ID values
            echo "Invalid testType_ID.";
        }
        exit();
    } else {
        echo "Error updating test type: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Missing lessonID or testType_ID.";
}

$conn->close();
?>
