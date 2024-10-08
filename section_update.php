<?php
// section_update.php

// Include the database connection file
include('connect.php'); // Assuming you have a database connection script

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the section ID and lesson ID
    $section_id = sanitizeInput($_POST['section_id']);
    $lesson_id = sanitizeInput($_POST['lessonID']);
    
    // Get other inputs
    $background_color = sanitizeInput($_POST['section_color']);
    $content_type = sanitizeInput($_POST['content_type']);
    
    $update_query = ""; // This will hold the update query
    $content_value = ""; // This will hold the content based on type
    
    // Initialize an error flag
    $error = false;

    // Update content based on the selected content type
    if ($content_type == "text") {
        // Handle text content
        if (isset($_POST['text_content'])) {
            $content_value = sanitizeInput($_POST['text_content']);
        } else {
            $error = true;
            echo "Text content is missing.";
        }
    } elseif ($content_type == "image") {
        // Handle image content (file upload)
        if (isset($_FILES['image_content']) && $_FILES['image_content']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/images/"; // Directory for image uploads
            $image_name = basename($_FILES["image_content"]["name"]);
            $target_file = $target_dir . time() . "_" . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type (only images)
            $valid_extensions = array("jpg", "jpeg", "png", "gif");
            if (in_array($imageFileType, $valid_extensions)) {
                // Move uploaded file to the target directory
                if (move_uploaded_file($_FILES["image_content"]["tmp_name"], $target_file)) {
                    $content_value = $target_file; // Store the file path in the database
                } else {
                    $error = true;
                    echo "There was an error uploading your image.";
                }
            } else {
                $error = true;
                echo "Invalid image file type.";
            }
        } else {
            $error = true;
            echo "Image content is missing.";
        }
    } elseif ($content_type == "video") {
        // Handle video content (file upload)
        if (isset($_FILES['video_content']) && $_FILES['video_content']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/videos/"; // Directory for video uploads
            $video_name = basename($_FILES["video_content"]["name"]);
            $target_file = $target_dir . time() . "_" . $video_name;
            $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file type (only videos)
            $valid_extensions = array("mp4", "mov", "avi", "wmv");
            if (in_array($videoFileType, $valid_extensions)) {
                // Move uploaded file to the target directory
                if (move_uploaded_file($_FILES["video_content"]["tmp_name"], $target_file)) {
                    $content_value = $target_file; // Store the file path in the database
                } else {
                    $error = true;
                    echo "There was an error uploading your video.";
                }
            } else {
                $error = true;
                echo "Invalid video file type.";
            }
        } else {
            $error = true;
            echo "Video content is missing.";
        }
    }

    // If no errors, proceed to update the database
    if (!$error) {
        // Prepare the update query
        $update_query = "
            UPDATE sections 
            SET background_color = ?, content_type = ?, content = ?
            WHERE id = ? AND lesson_id = ?
        ";
        
        // Prepare and bind the SQL statement
        if ($stmt = $conn->prepare($update_query)) {
            $stmt->bind_param('sssii', $background_color, $content_type, $content_value, $section_id, $lesson_id);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Section updated successfully!";
            } else {
                echo "Error updating section: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Database error: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
