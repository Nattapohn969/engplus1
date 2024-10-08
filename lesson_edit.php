<?php
// Connect to the database
include 'connect.php';  // เรียกไฟล์ connect.php เพื่อเชื่อมต่อกับฐานข้อมูล

// Check if lessonID is passed
if (isset($_GET['lessonID'])) {  // ตรวจสอบว่ามีการส่งค่า lessonID ผ่าน URL หรือไม่
    $lesson_id = $_GET['lessonID'];  // รับค่า lessonID จาก URL และเก็บในตัวแปร $lesson_id

    // Fetch lesson data from the database
    $query = "SELECT * FROM lessons WHERE lessonID = ?";  // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลบทเรียนจากฐานข้อมูลที่มี lessonID ตรงกับที่ได้รับ
    $stmt = $conn->prepare($query);  // เตรียมการทำงานของคำสั่ง SQL แบบ Prepared Statement เพื่อป้องกัน SQL Injection
    $stmt->bind_param('i', $lesson_id);  // กำหนดค่าให้กับพารามิเตอร์ที่ใช้ใน SQL (เลขแบบ integer)
    $stmt->execute();  // ทำการ execute คำสั่ง SQL
    $result = $stmt->get_result();  // ดึงผลลัพธ์จากการ execute มาเก็บในตัวแปร $result

    // Check if the lesson is found
    if ($result->num_rows == 1) {  // ตรวจสอบว่าพบข้อมูลบทเรียนหรือไม่ (หากเจอ จะมีจำนวนแถว = 1)
        $lesson = $result->fetch_assoc();  // เก็บข้อมูลบทเรียนเป็นอาเรย์เชื่อมโยง
    } else {
        echo "Lesson not found.";  // หากไม่พบบทเรียน แสดงข้อความและหยุดการทำงาน
        exit;
    }
} else {
    echo "No lesson ID provided.";  // หากไม่มี lessonID ถูกส่งมา แสดงข้อความและหยุดการทำงาน
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_id = $_POST['lesson_id'];
    $lessonName = $_POST['lessonName'];
    $pageColor = $_POST['page_color'];
    $containerColor = $_POST['container_color'];
    $textColor = $_POST['text_color'];
    $lessonDescription = $_POST['lessonDescription'];
    $coverImage = $_FILES['coverImage']['name'] ?? '';

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
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ส่วนหัว -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <!-- โหลด CKEditor สำหรับแก้ไขข้อความ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- โหลด CSS สำหรับ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- โหลด JS สำหรับ SweetAlert2 -->
    <link href="addlesson.css" rel="stylesheet" /> <!-- ลิงก์ไปยังไฟล์ CSS สำหรับการจัดรูปแบบของฟอร์ม -->
    <link href="stylead.css" rel="stylesheet" /> <!-- ลิงก์ไปยังไฟล์ CSS เพิ่มเติม -->
    <title>Edit Lesson</title> <!-- ชื่อของหน้าเว็บ -->
</head>
<style>
    .lesson-description-container {
        margin-bottom: 20px;
    }

    .lesson-name-container {
        margin-bottom: 20px;
        display: flex;
    }

    .lesson-name-container input[type="text"] {
        width: calc(100% - 20px);
        padding: 10px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        margin-right: 50px;
    }

    .save-lesson {
        background-color: #20B2AA;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1.2rem;
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 20px;
    }
    .add-section{
        background-color: #3CB371;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1.2rem;
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 5px;
    }

    /* .save-section{
          background-color: #3CB371;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1.2rem;
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 5px;
    }
 */


</style>

<body>
    <?php include 'navbar.php'; ?> <!-- เรียกใช้ Navbar จากไฟล์ navbar.php -->

    <!-- ส่วนเนื้อหาหลัก -->
    <div class="container">
        <div class="header">
            <h1>Edit Lesson</h1> <!-- หัวข้อหลักของหน้าเว็บ -->
        </div>

        <!-- ฟอร์มแก้ไขบทเรียน -->
        <form action='' method="post" enctype="multipart/form-data">
            <input type="hidden" name="lesson_id"
                value="<?php echo htmlspecialchars($lesson['lessonID'], ENT_QUOTES, 'UTF-8'); ?>" />
            <!-- เก็บค่า lessonID ไว้ในฟอร์มอย่างปลอดภัย -->

            <!-- การเลือกสีพื้นหลังของหน้า -->
            <div class="color-controls">
                <label for="page_color">Page Background Color:</label> <!-- ป้ายกำกับสำหรับสีพื้นหลังของหน้า -->
                <input type="color" id="page_color" name="page_color"
                    value="<?php echo htmlspecialchars($lesson['page_color'], ENT_QUOTES, 'UTF-8'); ?>"
                    onchange="updatePageColor()" /> <!-- อินพุตสำหรับเลือกสีและอัปเดตสีพื้นหลังเมื่อมีการเปลี่ยนแปลง -->
            </div>

            <div class="lesson-container" id="lessonContainer">
                <!-- การเลือกสีพื้นหลังของคอนเทนเนอร์ -->
                <div class="color-controls">
                    <label for="container_color">Container Background Color:</label>
                    <!-- ป้ายกำกับสำหรับสีพื้นหลังของคอนเทนเนอร์ -->
                    <input type="color" id="container_color" name="container_color"
                        value="<?php echo htmlspecialchars($lesson['container_color'], ENT_QUOTES, 'UTF-8'); ?>"
                        onchange="updateContainerColor()" /> <!-- อินพุตสำหรับเลือกสีของคอนเทนเนอร์ -->
                </div>

                <!-- การแก้ไขชื่อบทเรียนและสีตัวอักษร -->
                <div class="lesson-name-container">
                    <label for="lessonName">Lesson Name:</label> <!-- ป้ายกำกับสำหรับชื่อบทเรียน -->
                    <input type="text" id="lessonName" name="lessonName"
                        value="<?php echo htmlspecialchars($lesson['lessonName'], ENT_QUOTES, 'UTF-8'); ?>" required />

                    <!-- อินพุตสำหรับแก้ไขชื่อบทเรียน -->

                    <label for="text_color">Text Color:</label> <!-- ป้ายกำกับสำหรับสีของข้อความ -->
                    <input type="color" id="text_color" name="text_color"
                        value="<?php echo htmlspecialchars($lesson['text_color'], ENT_QUOTES, 'UTF-8'); ?>"
                        onchange="changeLessonTextColor()" /> <!-- อินพุตสำหรับเลือกสีตัวอักษร -->
                </div>

                <!-- ส่วนอัปโหลดภาพปก -->
                <div class="cover-image-controls">
                    <label for="coverImage">Cover Image:</label> <!-- ป้ายกำกับสำหรับอัปโหลดภาพปก -->
                    <input type="file" id="coverImage" name="coverImage" accept="image/*"
                        onchange="previewCoverImage(event)" /> <!-- อินพุตสำหรับเลือกไฟล์ภาพปก -->
                    <div id="coverImagePreview" class="cover-image-preview">
                        <?php if (!empty($lesson['cover_image'])): ?> <!-- ตรวจสอบว่ามีภาพปกที่เคยอัปโหลดหรือไม่ -->
                            <img src="<?php echo htmlspecialchars($lesson['cover_image'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Cover Image Preview" style="max-width: 100%; height: auto;"> <!-- แสดงตัวอย่างภาพปก -->
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ส่วนแก้ไขรายละเอียดของบทเรียน -->
                <div class="lesson-description-container">
                    <label for="lessonDescription">Lesson Description:</label> <!-- ป้ายกำกับสำหรับรายละเอียดบทเรียน -->
                    <textarea id="lessonDescription"
                        name="lessonDescription"><?php echo htmlspecialchars($lesson['lessonDescription'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    <!-- textarea สำหรับแก้ไขรายละเอียดของบทเรียน -->
                </div>


                <button type="submit" class="save-lesson">Save Lesson </button>
        </form>
        <!-- Manage lesson sections -->
        <div id="sections-container">
            <?php
            // Fetch sections of the lesson from the database with content of each type
            $query = "
                        SELECT sections.*, text_content.content as text_content, 
                               images.image_url, videos.video_url 
                        FROM sections
                        LEFT JOIN text_content ON sections.sectionID = text_content.sectionID
                        LEFT JOIN images ON sections.sectionID = images.sectionID
                        LEFT JOIN videos ON sections.sectionID = videos.sectionID
                        WHERE sections.lessonID = ?
                    ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $lesson_id);
            $stmt->execute();
            $sections_result = $stmt->get_result();
            $sectionCount = 0;

            // Display each section of the lesson
            while ($section = $sections_result->fetch_assoc()) {
                $sectionCount++;
                $sectionID = $section['sectionID']; // Fetch sectionID
                ?>
                <div class="content-section" id="section<?php echo $sectionID; ?>">
                    <form action="section_update.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="section_id"
                            value="<?php echo htmlspecialchars($sectionID, ENT_QUOTES, 'UTF-8'); ?>" />
                        <div class="section-header">
                            <span class="section-title">Section <?php echo $sectionCount; ?> (ID:
                                <?php echo $sectionID; ?>)</span> <!-- Display sectionID -->
                            <button type="button" class="delete-button"
                                onclick="removeSection(<?php echo $sectionID; ?>)">Delete</button>
                        </div>
                        <!-- Section background color selection -->
                        <label for="sectionColor<?php echo $sectionID; ?>">Section Background Color:</label>
                        <input type="color" id="sectionColor<?php echo $sectionID; ?>" name="section_color"
                            value="<?php echo htmlspecialchars($section['section_color'], ENT_QUOTES, 'UTF-8'); ?>"
                            onchange="updateSectionColor(<?php echo $sectionID; ?>)" />

                        <!-- Section content type selection -->
                        <label for="sectionContent<?php echo $sectionID; ?>">Content Type:</label>
                        <select id="sectionContent<?php echo $sectionID; ?>" name="content_type"
                            onchange="updateContent(<?php echo $sectionID; ?>)">
                            <option value="">-- Select Content --</option>
                            <option value="text" <?php echo $section['contentType'] == 'text' ? 'selected' : ''; ?>>
                                Text</option>
                            <option value="image" <?php echo $section['contentType'] == 'image' ? 'selected' : ''; ?>>
                                Image</option>
                            <option value="video" <?php echo $section['contentType'] == 'video' ? 'selected' : ''; ?>>
                                Video</option>
                        </select>

                        <div id="content<?php echo $sectionID; ?>" class="section-content">
                            <?php if ($section['contentType'] == 'text'): ?>
                                <textarea id="editor<?php echo $sectionID; ?>"
                                    name="text_content"><?php echo htmlspecialchars($section['text_content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <script>
                                    ClassicEditor
                                        .create(document.querySelector('#editor<?php echo $sectionID; ?>'))
                                        .then(editor => {
                                            editor.model.document.on('change:data', () => {
                                                document.querySelector('#editor<?php echo $sectionID; ?>').value = editor.getData();
                                            });
                                        })
                                        .catch(error => {
                                            console.error(error);
                                        });
                                </script>
                            <?php elseif ($section['contentType'] == 'image'): ?>
                                <input type="file" name="image_content" accept="image/*"
                                    onchange="previewImage(event, <?php echo $sectionID; ?>)" />
                                <div id="preview<?php echo $sectionID; ?>" class="preview">
                                    <?php if (!empty($section['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($section['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                            alt="Image Preview" style="max-width: 100%; height: auto;">
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($section['contentType'] == 'video'): ?>
                                <input type="file" name="video_content" accept="video/*"
                                    onchange="previewVideo(event, <?php echo $sectionID; ?>)" />
                                <div id="preview<?php echo $sectionID; ?>" class="preview">
                                    <?php if (!empty($section['video_url'])): ?>
                                        <video controls style="max-width: 100%; height: auto;">
                                            <source
                                                src="<?php echo htmlspecialchars($section['video_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                                type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="save-section">Save Section</button>
                    </form>
                </div>
            <?php } ?>
        </div>

        <!-- Add new section -->
        <button type="button" id="addSectionBtn" class="add-section">Add Section +</button>
    </div>


    </div>

    <script src="js/editlesson.js"></script>
</body>

</html>