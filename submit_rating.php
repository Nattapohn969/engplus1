<?php
session_start();
include 'connect.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST request
    $lessonID = $_POST['lessonID'] ?? null;  // Using null coalescing operator to avoid undefined index
    $userID = $_POST['user_ID'] ?? null;
    $rating = $_POST['rating'] ?? null;

    // Validate the data
    if ($lessonID !== null && $userID !== null && $rating !== null) {
        try {
            // Check if the user has already rated this lesson
            $stmt = $pdo->prepare("SELECT * FROM ratings WHERE lessonID = ? AND user_ID = ?");
            $stmt->execute([$lessonID, $userID]);
            $existingRating = $stmt->fetch();

            if ($existingRating) {
                // Update the existing rating
                $stmt = $pdo->prepare("UPDATE ratings SET rating = ?, timestamp = NOW() WHERE lessonID = ? AND user_ID = ?");
                $stmt->execute([$rating, $lessonID, $userID]);
            } else {
                // Insert a new rating
                $stmt = $pdo->prepare("INSERT INTO ratings (lessonID, user_ID, rating, timestamp) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$lessonID, $userID, $rating]);
            }

            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
