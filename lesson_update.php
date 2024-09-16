<?php
include 'connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lesson_id = $_POST['lessonID'];
    $lessonName = $_POST['lessonName'];
    $page_color = $_POST['page_color'];
    $container_color = $_POST['container_color'];
    $text_color = $_POST['text_color'];
    $lessonDescription = $_POST['lessonDescription'];

    // Fetch existing data for comparison
    $query = "SELECT * FROM lessons WHERE lessonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingLesson = $result->fetch_assoc();

    // Prepare update fields
    $updateFields = [];
    $params = [];

    // Check for changed values
    if ($lessonName !== $existingLesson['lessonName']) {
        $updateFields[] = "lessonName = ?";
        $params[] = $lessonName;
    }
    if ($page_color !== $existingLesson['page_color']) {
        $updateFields[] = "page_color = ?";
        $params[] = $page_color;
    }
    if ($container_color !== $existingLesson['container_color']) {
        $updateFields[] = "container_color = ?";
        $params[] = $container_color;
    }
    if ($text_color !== $existingLesson['text_color']) {
        $updateFields[] = "text_color = ?";
        $params[] = $text_color;
    }
    if ($lessonDescription !== $existingLesson['lessonDescription']) {
        $updateFields[] = "lessonDescription = ?";
        $params[] = $lessonDescription;
    }

    // Handle file uploads (if applicable)
    if (!empty($_FILES['coverImage']['name'])) {
        // Process the file upload
        $coverImage = 'uploads/' . basename($_FILES['coverImage']['name']);
        move_uploaded_file($_FILES['coverImage']['tmp_name'], $coverImage);

        // Add to update fields
        $updateFields[] = "cover_image = ?";
        $params[] = $coverImage;
    }

    // If there are fields to update
    if (!empty($updateFields)) {
        $updateQuery = "UPDATE lessons SET " . implode(', ', $updateFields) . " WHERE lessonID = ?";
        $stmt = $conn->prepare($updateQuery);
        $params[] = $lesson_id; // Add lessonID for the WHERE clause

        // Bind parameters dynamically
        $types = str_repeat('s', count($params) - 1) . 'i'; // 's' for strings, 'i' for integer
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }

    // Update sections if necessary (similar logic as above)
    // ...

    echo "Lesson updated successfully.";
}
?>
