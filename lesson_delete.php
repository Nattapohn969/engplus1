<?php

include 'connect.php';

// Check if the ID is provided
if (isset($_GET['lessonID'])) {
    $lessonID = intval($_GET['lessonID']); // Ensure it's an integer

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete related images
        $stmt = $conn->prepare("
            DELETE FROM images 
            WHERE sectionID IN (SELECT sectionID FROM sections WHERE lessonID = ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Delete related videos
        $stmt = $conn->prepare("
            DELETE FROM videos 
            WHERE sectionID IN (SELECT sectionID FROM sections WHERE lessonID = ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Delete related text content
        $stmt = $conn->prepare("
            DELETE FROM text_content 
            WHERE sectionID IN (SELECT sectionID FROM sections WHERE lessonID = ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Delete related test entries
        $stmt = $conn->prepare("DELETE FROM test1 WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Delete sections
        $stmt = $conn->prepare("DELETE FROM sections WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Delete lesson
        $stmt = $conn->prepare("DELETE FROM lessons WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();
        
        // Redirect to the previous page
        header("Location: lessons_manage.php"); // Change 'previous_page.php' to the actual page you want to redirect to
        exit();
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $conn->rollback();
        echo "Failed to delete lesson: " . $e->getMessage();
    }

    // Close connection
    $conn->close();
} else {
    echo "No lesson ID provided.";
}
?>