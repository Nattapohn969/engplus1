<?php
include 'connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Receive data from the form
    $lessonName = $_POST['lessonName'] ?? '';
    $pageColor = $_POST['page_color'] ?? '';
    $textColor = $_POST['text_color'] ?? '';
    $containerColor = $_POST['container_color'] ?? '';
    $userID = 50; // Example userID, adjust as needed

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert lesson information
        $stmt = $conn->prepare("INSERT INTO lessons (lessonName, page_color, text_color, container_color, user_ID) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("ssssi", $lessonName, $pageColor, $textColor, $containerColor, $userID);
        $stmt->execute();
        $lessonID = $stmt->insert_id;

        // Insert section information
        $sectionNum = 1;
        while (isset($_POST["sectionColor$sectionNum"])) {
            $sectionColor = $_POST["sectionColor$sectionNum"];
            $contentType = $_POST["contentType"][$sectionNum] ?? '';

            $stmt = $conn->prepare("INSERT INTO sections (lessonID, section_num, section_color, contentType) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            $stmt->bind_param("iiss", $lessonID, $sectionNum, $sectionColor, $contentType);
            $stmt->execute();
            $sectionID = $stmt->insert_id;

            // Handle different content types
            if ($contentType === "text" && !empty($_POST["contentText$sectionNum"])) {
                $content = $_POST["contentText$sectionNum"];
                $textColorContent = $_POST["textColor$sectionNum"] ?? '';

                $stmt = $conn->prepare("INSERT INTO text_content (sectionID, content, text_color) VALUES (?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param("iss", $sectionID, $content, $textColorContent);
                $stmt->execute();

            } elseif ($contentType === "image" && isset($_FILES["contentImage$sectionNum"])) {
                // Handle image upload
                $imageFile = $_FILES["contentImage$sectionNum"];
                if ($imageFile['error'] === UPLOAD_ERR_OK) {
                    $imageFileName = basename($imageFile["name"]);
                    $imageFileTmp = $imageFile["tmp_name"];
                    $imageFilePath = "uploads/" . $imageFileName;

                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($imageFile['type'], $allowedTypes) && move_uploaded_file($imageFileTmp, $imageFilePath)) {
                        $stmt = $conn->prepare("INSERT INTO images (sectionID, image_url) VALUES (?, ?)");
                        if (!$stmt) {
                            throw new Exception("Prepare statement failed: " . $conn->error);
                        }
                        $stmt->bind_param("is", $sectionID, $imageFilePath);
                        $stmt->execute();
                    } else {
                        throw new Exception("Invalid file type or failed to upload image.");
                    }
                } else {
                    throw new Exception("Error uploading image file: " . $imageFile['error']);
                }

            } elseif ($contentType === "video" && isset($_FILES["contentVideo$sectionNum"])) {
                // Handle video upload
                $videoFile = $_FILES["contentVideo$sectionNum"];
                if ($videoFile['error'] === UPLOAD_ERR_OK) {
                    $videoFileName = basename($videoFile["name"]);
                    $videoFileTmp = $videoFile["tmp_name"];
                    $videoFilePath = "uploads/" . $videoFileName;

                    // Validate file type
                    $allowedTypes = ['video/mp4', 'video/avi', 'video/mkv'];
                    if (in_array($videoFile['type'], $allowedTypes) && move_uploaded_file($videoFileTmp, $videoFilePath)) {
                        $stmt = $conn->prepare("INSERT INTO videos (sectionID, video_url) VALUES (?, ?)");
                        if (!$stmt) {
                            throw new Exception("Prepare statement failed: " . $conn->error);
                        }
                        $stmt->bind_param("is", $sectionID, $videoFilePath);
                        $stmt->execute();
                    } else {
                        throw new Exception("Invalid file type or failed to upload video.");
                    }
                } else {
                    throw new Exception("Error uploading video file: " . $videoFile['error']);
                }
            }

            $sectionNum++;
        }

        // Commit transaction
        $conn->commit();

        // Redirect to add_test1.php with lessonID and lessonName
        header("Location: test1_add.php?lessonID=$lessonID&lessonName=" . urlencode($lessonName));
        exit();
    } catch (Exception $e) {
        // Rollback transaction if an error occurs
        $conn->rollback();
        echo "Failed to save lesson: " . $e->getMessage();
    }

    // Close connection
    $conn->close();
}
?>