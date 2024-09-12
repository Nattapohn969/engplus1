<?php
session_start(); // Start the session

include 'connect.php';

// Check if the user is logged in and has a user_ID in the session
if (!isset($_SESSION['user_ID'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user_ID from the session
$userID = $_SESSION['user_ID'];

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get data from the form
    $lessonName = $_POST['lessonName'] ?? ''; // Lesson name
    $pageColor = $_POST['page_color'] ?? ''; // Background color
    $textColor = $_POST['text_color'] ?? ''; // Text color
    $containerColor = $_POST['container_color'] ?? ''; // Container color
    $testType = $_POST['testType'] ?? ''; // Test type

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert lesson data
        $stmt = $conn->prepare("INSERT INTO lessons (lessonName, page_color, text_color, container_color, testType, user_ID) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("sssssi", $lessonName, $pageColor, $textColor, $containerColor, $testType, $userID);
        $stmt->execute();
        $lessonID = $stmt->insert_id; // Get the newly created lessonID

        // Insert section data
        $sectionNum = 1;
        while (isset($_POST["sectionColor$sectionNum"])) {
            $sectionColor = $_POST["sectionColor$sectionNum"]; // Section color
            $contentType = $_POST["contentType"][$sectionNum] ?? ''; // Content type

            // Insert into sections table
            $stmt = $conn->prepare("INSERT INTO sections (lessonID, section_num, section_color, contentType) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            $stmt->bind_param("iiss", $lessonID, $sectionNum, $sectionColor, $contentType);
            $stmt->execute();
            $sectionID = $stmt->insert_id; // Get the newly created sectionID

            // Handle content based on content type
            if ($contentType === "text" && !empty($_POST["contentText$sectionNum"])) {
                $content = $_POST["contentText$sectionNum"]; // Text content
                $textColorContent = $_POST["textColor$sectionNum"] ?? ''; // Text color

                // Insert into text_content table
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

                    // Check file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($imageFile['type'], $allowedTypes) && move_uploaded_file($imageFileTmp, $imageFilePath)) {
                        // Insert into images table
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

                    // Check file type
                    $allowedTypes = ['video/mp4', 'video/avi', 'video/mkv'];
                    if (in_array($videoFile['type'], $allowedTypes) && move_uploaded_file($videoFileTmp, $videoFilePath)) {
                        // Insert into videos table
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

        // Commit the transaction
        $conn->commit();
        // Redirect to test_choose.php
        header("Location: test_choose.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Failed: " . addslashes($e->getMessage()) . "',
                icon: 'error'
            });
        </script>";
    }

    // Close the database connection
    $conn->close();
}
?>
