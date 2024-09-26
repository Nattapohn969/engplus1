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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ส่วนหัว -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- โหลดฟอนต์จาก Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <!-- โหลด CKEditor สำหรับแก้ไขข้อความ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- โหลด CSS สำหรับ SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- โหลด JS สำหรับ SweetAlert2 -->
    <link href="css/addlesson.css" rel="stylesheet" /> <!-- ลิงก์ไปยังไฟล์ CSS สำหรับการจัดรูปแบบของฟอร์ม -->
    <link href="css/stylead.css" rel="stylesheet" /> <!-- ลิงก์ไปยังไฟล์ CSS เพิ่มเติม -->
    <title>Edit Lesson</title> <!-- ชื่อของหน้าเว็บ -->
</head>

<body>
    <?php include 'navbar.php'; ?> <!-- เรียกใช้ Navbar จากไฟล์ navbar.php -->

    <!-- ส่วนเนื้อหาหลัก -->
    <div class="container">
        <div class="header">
            <h1>Edit Lesson</h1> <!-- หัวข้อหลักของหน้าเว็บ -->
        </div>

        <!-- ฟอร์มแก้ไขบทเรียน -->
        <form action='lesson_update.php' method="post" enctype="multipart/form-data">
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

                <script>
                    ClassicEditor
                        .create(document.querySelector('#lessonDescription'))  // สร้าง CKEditor สำหรับ textarea
                        .catch(error => {
                            console.error(error);  // แสดง error ถ้าหากเกิดข้อผิดพลาดในการสร้าง CKEditor
                        });
                </script>


                <!-- Manage lesson sections -->
                <div id="sections-container">
                    <?php
                    // Fetch sections of the lesson from the database with content of each type
                    $query = "
        SELECT sections.*, text_content.content as text_content, text_content.text_color as text_color, 
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
                            <div class="section-header">
                                <span class="section-title">Section <?php echo $sectionCount; ?> (ID:
                                    <?php echo $sectionID; ?>)</span> <!-- Display sectionID -->
                                <button type="button" class="delete-button"
                                    onclick="removeSection(<?php echo $sectionID; ?>)">Delete</button>
                            </div>
                            <!-- Section background color selection -->
                            <label for="sectionColor<?php echo $sectionID; ?>">Section Background Color:</label>
                            <input type="color" id="sectionColor<?php echo $sectionID; ?>"
                                name="sectionColor<?php echo $sectionID; ?>"
                                value="<?php echo htmlspecialchars($section['section_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                onchange="updateSectionColor(<?php echo $sectionID; ?>)" />

                            <!-- Section content type selection -->
                            <label for="sectionContent<?php echo $sectionID; ?>">Content Type:</label>
                            <select id="sectionContent<?php echo $sectionID; ?>"
                                name="contentType[<?php echo $sectionID; ?>]"
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
                                        name="content<?php echo $sectionID; ?>"><?php echo htmlspecialchars($section['text_content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                                    <input type="file" name="contentImage<?php echo $sectionID; ?>" accept="image/*" />
                                    <div id="preview<?php echo $sectionID; ?>" class="preview">
                                        <?php if (!empty($section['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($section['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                                alt="Image Preview" style="max-width: 100%; height: auto;">
                                        <?php endif; ?>
                                    </div>

                                <?php elseif ($section['contentType'] == 'video'): ?>
                                    <input type="file" name="contentVideo<?php echo $sectionID; ?>" accept="video/*" />
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
                        </div>
                    <?php } ?>
                </div>

                <!-- Add new section -->
                <button type="button" id="addSectionBtn">Add Section</button>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>

    <script>


        // ตั้งค่าสีพื้นหลังของหน้าและคอนเทนเนอร์เมื่อโหลดหน้า
        window.onload = function () {
            updatePageColor(); // ตั้งค่าสีพื้นหลังของหน้า
            updateContainerColor(); // ตั้งค่าสีพื้นหลังของคอนเทนเนอร์

            // วนลูปผ่านทุก section และตั้งค่าสีพื้นหลังของแต่ละ section
            const sectionCount = document.querySelectorAll('.content-section').length;
            for (let i = 1; i <= sectionCount; i++) {
                updateSectionColor(i); // ตั้งค่าสีพื้นหลังของแต่ละ section
            }
        };

        // ฟังก์ชันในการตั้งค่าสีพื้นหลังของหน้า
        function updatePageColor() {
            document.body.style.backgroundColor = document.getElementById('page_color').value;
        }

        // ฟังก์ชันในการตั้งค่าสีพื้นหลังของคอนเทนเนอร์
        function updateContainerColor() {
            document.getElementById('lessonContainer').style.backgroundColor = document.getElementById('container_color').value;
        }

        // ฟังก์ชันในการตั้งค่าสีพื้นหลังของแต่ละ section
        function updateSectionColor(sectionNumber) {
            const section = document.getElementById('section' + sectionNumber);
            const sectionColor = document.getElementById('sectionColor' + sectionNumber).value;
            section.style.backgroundColor = sectionColor;
        }




        // เเสดงสีของส่วนต่างๆหลังจากที่กดเปลี่ยนเเล้ว

        function updatePageColor() {
            // เปลี่ยนเฉพาะสีพื้นหลังของหน้า
            document.body.style.backgroundColor = document.getElementById('page_color').value;
        }

        function updateContainerColor() {
            // เปลี่ยนเฉพาะสีพื้นหลังของ container ที่มี ID lessonContainer
            document.getElementById('lessonContainer').style.backgroundColor = document.getElementById('container_color').value;
        }

        function changeLessonTextColor() {
            // เปลี่ยนเฉพาะสีตัวอักษรของ lessonName และ lessonDescription
            const textColor = document.getElementById('text_color').value;
            document.getElementById('lessonName').style.color = textColor;
            document.getElementById('lessonDescription').style.color = textColor;
        }

        function updateSectionColor(sectionNumber) {
            // เปลี่ยนเฉพาะสีพื้นหลังของ section ที่ถูกระบุ
            const section = document.getElementById('section' + sectionNumber);
            const sectionColor = document.getElementById('sectionColor' + sectionNumber).value;
            section.style.backgroundColor = sectionColor;
        }




        function updateContent(sectionNumber) {
            const contentType = document.getElementById('sectionContent' + sectionNumber).value;
            document.getElementById('content' + sectionNumber).innerHTML = '';

            if (contentType === 'text') {
                const editorContainer = document.createElement('textarea');
                editorContainer.id = 'editor' + sectionNumber;
                document.getElementById('content' + sectionNumber).appendChild(editorContainer);

                ClassicEditor
                    .create(editorContainer)
                    .catch(error => {
                        console.error(error);
                    });

            } else if (contentType === 'image') {
                const imageInput = document.createElement('input');
                imageInput.type = 'file';
                imageInput.name = 'contentImage' + sectionNumber;
                imageInput.accept = 'image/*';
                imageInput.onchange = function (event) {
                    previewImage(event, sectionNumber);
                };
                document.getElementById('content' + sectionNumber).appendChild(imageInput);

                const preview = document.createElement('div');
                preview.id = 'preview' + sectionNumber;
                preview.className = 'preview';
                document.getElementById('content' + sectionNumber).appendChild(preview);

            } else if (contentType === 'video') {
                const videoInput = document.createElement('input');
                videoInput.type = 'file';
                videoInput.name = 'contentVideo' + sectionNumber;
                videoInput.accept = 'video/*';
                videoInput.onchange = function (event) {
                    previewVideo(event, sectionNumber);  // Call the preview function for video
                };
                document.getElementById('content' + sectionNumber).appendChild(videoInput);

                const preview = document.createElement('div');
                preview.id = 'preview' + sectionNumber;
                preview.className = 'preview';
                document.getElementById('content' + sectionNumber).appendChild(preview);
            }
        }

        function previewImage(event, sectionNumber) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('preview' + sectionNumber);
                preview.innerHTML = '<img src="' + reader.result + '" alt="Image Preview" style="max-width: 100%; height: auto;">';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function previewCoverImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('coverImagePreview');
                preview.innerHTML = '<img src="' + reader.result + '" alt="Cover Image Preview" style="max-width: 100%; height: auto;">';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function previewVideo(event, sectionNumber) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function () {
                    const preview = document.getElementById('preview' + sectionNumber);
                    preview.innerHTML = `
                <video controls style="max-width: 100%; height: auto;">
                    <source src="${reader.result}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>`;
                };
                reader.readAsDataURL(file);  // Read the video file as a data URL for preview
            }
        }




        function removeSection(sectionNumber) {
            const section = document.getElementById('section' + sectionNumber);
            if (section) {
                section.remove();
            }
        }

        document.getElementById('addSectionBtn').addEventListener('click', () => {
            const sectionNumber = document.querySelectorAll('.content-section').length + 1;
            const newSection = document.createElement('div');
            newSection.className = 'content-section';
            newSection.id = 'section' + sectionNumber;
            newSection.innerHTML = `
        <div class="section-header">
            <span class="section-title">Section ${sectionNumber}</span>
            <button type="button" class="delete-button" onclick="removeSection(${sectionNumber})">Delete</button>
        </div>
        <label for="sectionColor${sectionNumber}">Section Background Color:</label>
        <input type="color" id="sectionColor${sectionNumber}" name="sectionColor${sectionNumber}" 
            onchange="updateSectionColor(${sectionNumber})" />
        <label for="sectionContent${sectionNumber}">Content Type:</label>
        <select id="sectionContent${sectionNumber}" name="contentType[${sectionNumber}]"
            onchange="updateContent(${sectionNumber})">
            <option value="">-- Select Content --</option>
            <option value="text">Text</option>
            <option value="image">Image</option>
            <option value="video">Video</option>
        </select>
        <div id="content${sectionNumber}" class="section-content"></div>
    `;
            document.getElementById('sections-container').appendChild(newSection);
        });

    </script>
</body>

</html>เเสดง sectionID