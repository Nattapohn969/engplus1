<?php
include 'connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการส่งข้อมูลจากแบบฟอร์ม
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // รับข้อมูลจากแบบฟอร์ม
    $lessonName = $_POST['lessonName'] ?? ''; // ชื่อบทเรียน
    $pageColor = $_POST['page_color'] ?? ''; // สีพื้นหลังหน้า
    $textColor = $_POST['text_color'] ?? ''; // สีข้อความ
    $containerColor = $_POST['container_color'] ?? ''; // สีของกล่องเนื้อหา
    $testType = $_POST['testType'] ?? ''; // ประเภทของการทดสอบ
    $userID = 50; // ตัวอย่าง userID (ควรปรับให้เป็น ID ของผู้ใช้จริง)

    // เริ่มต้นการทำธุรกรรม
    $conn->begin_transaction();

    try {
        // แทรกข้อมูลบทเรียน
        $stmt = $conn->prepare("INSERT INTO lessons (lessonName, page_color, text_color, container_color, testType, user_ID) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("sssssi", $lessonName, $pageColor, $textColor, $containerColor, $testType, $userID);
        $stmt->execute();
        $lessonID = $stmt->insert_id; // รับค่า lessonID ที่ถูกสร้างขึ้นใหม่

        // แทรกข้อมูลหมวดหมู่ (section)
        $sectionNum = 1;
        while (isset($_POST["sectionColor$sectionNum"])) {
            $sectionColor = $_POST["sectionColor$sectionNum"]; // สีของหมวดหมู่
            $contentType = $_POST["contentType"][$sectionNum] ?? ''; // ประเภทเนื้อหา

            // แทรกข้อมูลในตาราง sections
            $stmt = $conn->prepare("INSERT INTO sections (lessonID, section_num, section_color, contentType) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            $stmt->bind_param("iiss", $lessonID, $sectionNum, $sectionColor, $contentType);
            $stmt->execute();
            $sectionID = $stmt->insert_id; // รับค่า sectionID ที่ถูกสร้างขึ้นใหม่

            // จัดการเนื้อหาตามประเภท
            if ($contentType === "text" && !empty($_POST["contentText$sectionNum"])) {
                $content = $_POST["contentText$sectionNum"]; // เนื้อหาข้อความ
                $textColorContent = $_POST["textColor$sectionNum"] ?? ''; // สีข้อความ

                // แทรกข้อมูลในตาราง text_content
                $stmt = $conn->prepare("INSERT INTO text_content (sectionID, content, text_color) VALUES (?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Prepare statement failed: " . $conn->error);
                }
                $stmt->bind_param("iss", $sectionID, $content, $textColorContent);
                $stmt->execute();

            } elseif ($contentType === "image" && isset($_FILES["contentImage$sectionNum"])) {
                // จัดการการอัปโหลดภาพ
                $imageFile = $_FILES["contentImage$sectionNum"];
                if ($imageFile['error'] === UPLOAD_ERR_OK) {
                    $imageFileName = basename($imageFile["name"]);
                    $imageFileTmp = $imageFile["tmp_name"];
                    $imageFilePath = "uploads/" . $imageFileName;

                    // ตรวจสอบประเภทไฟล์
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (in_array($imageFile['type'], $allowedTypes) && move_uploaded_file($imageFileTmp, $imageFilePath)) {
                        // แทรกข้อมูลในตาราง images
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
                // จัดการการอัปโหลดวิดีโอ
                $videoFile = $_FILES["contentVideo$sectionNum"];
                if ($videoFile['error'] === UPLOAD_ERR_OK) {
                    $videoFileName = basename($videoFile["name"]);
                    $videoFileTmp = $videoFile["tmp_name"];
                    $videoFilePath = "uploads/" . $videoFileName;

                    // ตรวจสอบประเภทไฟล์
                    $allowedTypes = ['video/mp4', 'video/avi', 'video/mkv'];
                    if (in_array($videoFile['type'], $allowedTypes) && move_uploaded_file($videoFileTmp, $videoFilePath)) {
                        // แทรกข้อมูลในตาราง videos
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

        // ยืนยันการทำธุรกรรม
        $conn->commit();

        // เปลี่ยนเส้นทางไปยังหน้า test_choose.php พร้อมส่งค่า lessonID, lessonName และ testType
        header("Location: test_choose.php?lessonID=$lessonID&lessonName=" . urlencode($lessonName) . "&testType=" . urlencode($testType));
        exit();
    } catch (Exception $e) {
        // ยกเลิกการทำธุรกรรมหากเกิดข้อผิดพลาด
        $conn->rollback();
        echo "Failed to save lesson: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
}
?>
