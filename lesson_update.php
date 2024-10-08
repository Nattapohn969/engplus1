<?php
include 'connect.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = $_POST['lesson_id'];
    $lessonName = $_POST['lessonName'];
    $pageColor = $_POST['page_color'];
    $containerColor = $_POST['container_color'];
    $textColor = $_POST['text_color'];
    $lessonDescription = $_POST['lessonDescription'];
    $coverImage = $_FILES['coverImage']['name'] ?? '';

    // Debug Output
    echo "Debug Info: <br>";
    echo "Lesson ID: $lesson_id<br>";
    echo "Lesson Name: $lessonName<br>";
    echo "Page Color: $pageColor<br>";
    echo "Container Color: $containerColor<br>";
    echo "Text Color: $textColor<br>";
    echo "Lesson Description: $lessonDescription<br>";
    echo "Cover Image: $coverImage<br>";

    // อัปเดตข้อมูลบทเรียน
    $updateLessonQuery = "UPDATE lessons SET lessonName = ?, page_color = ?, container_color = ?, text_color = ?, lessonDescription = ? WHERE lessonID = ?";
    $stmt = $conn->prepare($updateLessonQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('sssssi', $lessonName, $pageColor, $containerColor, $textColor, $lessonDescription, $lesson_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    echo "Lesson updated successfully.<br>";

    // อัปโหลด cover image
    if ($coverImage) {
        $targetDir = "uploads/images/";
        $targetFile = $targetDir . basename($coverImage);

        if (move_uploaded_file($_FILES['coverImage']['tmp_name'], $targetFile)) {
            // อัปเดต URL ของ cover image
            $updateCoverImageQuery = "UPDATE lessons SET cover_image = ? WHERE lessonID = ?";
            $stmt = $conn->prepare($updateCoverImageQuery);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param('si', $targetFile, $lesson_id);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            echo "Cover image uploaded successfully: $targetFile<br>";
        } else {
            echo "Error uploading cover image.<br>";
        }
    }

    // ประมวลผล sections
    $contentTypes = $_POST['contentType'] ?? [];
    $sectionNums = $_POST['section_num'] ?? [];

    foreach ($contentTypes as $index => $contentType) {
        $sectionID = $sectionNums[$index] ?? 0;
        $sectionColor = $_POST['sectionColor' . $sectionID] ?? '';
        $content = $_POST['content' . $sectionID] ?? '';

        // Debug Output for sections
        echo "Debug Info for Section $sectionID: <br>";
        echo "Section Color: $sectionColor<br>";
        echo "Content Type: $contentType<br>";
        echo "Content: " . htmlspecialchars($content) . "<br>";

        // อัปเดตข้อมูล sections
        $updateSectionQuery = "UPDATE sections SET section_color = ?, contentType = ? WHERE sectionID = ?";
        $stmt = $conn->prepare($updateSectionQuery);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('ssi', $sectionColor, $contentType, $sectionID);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        echo "Section $sectionID updated successfully.<br>";

        // อัปเดตเนื้อหาตามประเภท
        if ($contentType === 'text') {
            // อัปเดตข้อมูลใน text_content
            $updateTextContentQuery = "UPDATE text_content SET content = ? WHERE sectionID = ?";
            $stmt = $conn->prepare($updateTextContentQuery);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param('si', $content, $sectionID);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            echo "Text content for Section $sectionID updated successfully.<br>";
        } elseif ($contentType === 'images') {
            // จัดการเนื้อหาภาพ
            $imageFile = $_FILES['contentImage' . $sectionID]['name'] ?? '';
            if ($imageFile) {
                $imageTargetDir = "uploads/images/";
                $imageTargetFile = $imageTargetDir . basename($imageFile);
                if (move_uploaded_file($_FILES['contentImage' . $sectionID]['tmp_name'], $imageTargetFile)) {
                    // อัปเดตข้อมูลใน image_content
                    $updateImageContentQuery = "UPDATE images SET image_url = ? WHERE sectionID = ?";
                    $stmt = $conn->prepare($updateImageContentQuery);
                    if (!$stmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $stmt->bind_param('si', $imageTargetFile, $sectionID);
                    if (!$stmt->execute()) {
                        die("Execute failed: " . $stmt->error);
                    }
                    echo "Image for Section $sectionID uploaded successfully: $imageTargetFile<br>";
                } else {
                    echo "Error uploading image for Section $sectionID.<br>";
                }
            }
        } elseif ($contentType === 'video') {
            // จัดการเนื้อหาวิดีโอ
            $videoUrl = $_POST['videoUrl' . $sectionID] ?? '';
            $updateVideoContentQuery = "UPDATE videos SET video_url = ? WHERE sectionID = ?";
            $stmt = $conn->prepare($updateVideoContentQuery);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param('si', $videoUrl, $sectionID);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            echo "Video content for Section $sectionID updated successfully: $videoUrl<br>";
        }
    }

    // แจ้งผู้ใช้ว่าได้บันทึกข้อมูลเรียบร้อยแล้ว
    echo "ข้อมูลทั้งหมดถูกบันทึกเรียบร้อยแล้ว!";
} else {
    echo "Invalid request.";
}
?>
