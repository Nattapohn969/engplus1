<?php
include 'connect.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = $_POST['lesson_id'];
    $lessonName = $_POST['lessonName'];
    $pageColor = $_POST['page_color'];
    $containerColor = $_POST['container_color'];
    $textColor = $_POST['text_color']; // รับค่า text_color จากฟอร์ม
    $lessonDescription = $_POST['lessonDescription'];
    $coverImage = $_FILES['coverImage']['name'] ?? '';

    // อัปเดตข้อมูลบทเรียน รวมถึง text_color
    $updateLessonQuery = "UPDATE lessons SET lessonName = ?, page_color = ?, container_color = ?, text_color = ?, lessonDescription = ? WHERE lessonID = ?";
    $stmt = $conn->prepare($updateLessonQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error . " SQL: " . $updateLessonQuery);
    }
    $stmt->bind_param('sssssi', $lessonName, $pageColor, $containerColor, $textColor, $lessonDescription, $lesson_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    // อัปโหลด cover image
    if ($coverImage) {
        $targetDir = "uploads/images/";
        $targetFile = $targetDir . basename($coverImage);

        // ตรวจสอบการอัปโหลดไฟล์
        if (move_uploaded_file($_FILES['coverImage']['tmp_name'], $targetFile)) {
            $currentCoverImageQuery = "SELECT cover_image FROM lessons WHERE lessonID = ?";
            $stmt = $conn->prepare($currentCoverImageQuery);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error . " SQL: " . $currentCoverImageQuery);
            }
            $stmt->bind_param('i', $lesson_id);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
            $currentCoverImageUrl = $stmt->get_result()->fetch_assoc()['cover_image'];

            // อัปเดตเฉพาะเมื่อมีการเปลี่ยนแปลง
            if ($targetFile !== $currentCoverImageUrl) {
                $updateCoverImageQuery = "UPDATE lessons SET cover_image = ? WHERE lessonID = ?";
                $stmt = $conn->prepare($updateCoverImageQuery);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error . " SQL: " . $updateCoverImageQuery);
                }
                $stmt->bind_param('si', $targetFile, $lesson_id);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
            }
        } else {
            echo "Error uploading file.";
            exit();
        }
    }



    

    // ประมวลผล sections
    $contentTypes = $_POST['contentType'] ?? [];
    foreach ($contentTypes as $index => $contentType) {
        $sectionID = $index + 1; // sectionID ควรตรงกับ index ที่ส่งมาจากฟอร์ม
        $sectionColor = $_POST['sectionColor' . ($index + 1)] ?? '';
        $content = $_POST['content' . ($index + 1)] ?? '';

        // อัปเดตข้อมูล sections
        $updateSectionQuery = "UPDATE sections SET section_color = ?, contentType = ? WHERE sectionID = ?";
        $stmt = $conn->prepare($updateSectionQuery);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error . " SQL: " . $updateSectionQuery);
        }
        $stmt->bind_param('ssi', $sectionColor, $contentType, $sectionID);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        // อัปเดตเนื้อหาตามประเภท
        if ($contentType === 'text') {
            // อัปเดตข้อมูลใน text_content
            $updateTextContentQuery = "UPDATE text_content SET content = ? WHERE sectionID = ?";
            $stmt = $conn->prepare($updateTextContentQuery);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error . " SQL: " . $updateTextContentQuery);
            }
            $stmt->bind_param('si', $content, $sectionID);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
        } elseif ($contentType === 'images') {
            // จัดการเนื้อหาภาพ
            $imageFile = $_FILES['contentImage' . ($index + 1)]['name'] ?? '';
            if ($imageFile) {
                $imageTargetDir = "uploads/images/";
                $imageTargetFile = $imageTargetDir . basename($imageFile);
                move_uploaded_file($_FILES['contentImage' . ($index + 1)]['tmp_name'], $imageTargetFile);

                // อัปเดตข้อมูลใน image_content
                $updateImageContentQuery = "UPDATE images SET image_url = ? WHERE sectionID = ?";
                $stmt = $conn->prepare($updateImageContentQuery);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error . " SQL: " . $updateImageContentQuery);
                }
                $stmt->bind_param('si', $imageTargetFile, $sectionID);
                if (!$stmt->execute()) {
                    die("Execute failed: " . $stmt->error);
                }
            }
        } elseif ($contentType === 'video') {
            // จัดการเนื้อหาวิดีโอ
            $videoUrl = $_POST['videoUrl' . ($index + 1)] ?? '';
            $updateVideoContentQuery = "UPDATE videos SET video_url = ? WHERE sectionID = ?";
            $stmt = $conn->prepare($updateVideoContentQuery);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error . " SQL: " . $updateVideoContentQuery);
            }
            $stmt->bind_param('si', $videoUrl, $sectionID);
            if (!$stmt->execute()) {
                die("Execute failed: " . $stmt->error);
            }
        }
    }

    // ส่งผู้ใช้ไปยังหน้าบทเรียน
    header("Location: lesson.php?lessonID=" . $lesson_id);
    exit();
} else {
    echo "Invalid request.";
}
?>