<?php
// Connect to the database
include 'connect.php';

// Check if lessonID is passed
if (isset($_GET['lessonID'])) {
    $lesson_id = $_GET['lessonID'];

    // Fetch lesson data from the database
    $query = "SELECT * FROM lessons WHERE lessonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $lesson_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the lesson is found
    if ($result->num_rows == 1) {
        $lesson = $result->fetch_assoc();
    } else {
        echo "Lesson not found.";
        exit;
    }
} else {
    echo "No lesson ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="css/addlesson.css" rel="stylesheet" />
    <link href="css/stylead.css" rel="stylesheet" />
    <title>Edit Lesson</title>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="header">
            <h1>Edit Lesson</h1>
        </div>

        <!-- Form to edit the lesson -->
        <form action='lesson_update.php' method="post" enctype="multipart/form-data">
            <input type="hidden" name="lesson_id"
                value="<?php echo htmlspecialchars($lesson['lessonID'], ENT_QUOTES, 'UTF-8'); ?>" />

            <!-- Page background color selection -->
            <div class="color-controls">
                <label for="page_color">Page Background Color:</label>
                <input type="color" id="page_color" name="page_color"
                    value="<?php echo htmlspecialchars($lesson['page_color'], ENT_QUOTES, 'UTF-8'); ?>"
                    onchange="updatePageColor()" />
            </div>

            <div class="lesson-container" id="lessonContainer">
                <!-- Container background color selection -->
                <div class="color-controls">
                    <label for="container_color">Container Background Color:</label>
                    <input type="color" id="container_color" name="container_color"
                        value="<?php echo htmlspecialchars($lesson['container_color'], ENT_QUOTES, 'UTF-8'); ?>"
                        onchange="updateContainerColor()" />
                </div>

                <!-- Edit lesson name and text color -->
                <div class="lesson-name-container">
                    <label for="lessonName">Lesson Name:</label>
                    <input type="text" id="lessonName" name="lessonName"
                        value="<?php echo htmlspecialchars($lesson['lessonName'], ENT_QUOTES, 'UTF-8'); ?>" required />

                    <label for="text_color">Text Color:</label>
                    <input type="color" id="text_color" name="text_color"
                        value="<?php echo htmlspecialchars($lesson['text_color'], ENT_QUOTES, 'UTF-8'); ?>"
                        onchange="changeLessonTextColor()" />
                </div>

                <!-- Cover image upload -->
                <div class="cover-image-controls">
                    <label for="coverImage">Cover Image:</label>
                    <input type="file" id="coverImage" name="coverImage" accept="image/*"
                        onchange="previewCoverImage(event)" />
                    <div id="coverImagePreview" class="cover-image-preview">
                        <?php if (!empty($lesson['cover_image'])): ?>
                            <img src="<?php echo htmlspecialchars($lesson['cover_image'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="Cover Image Preview" style="max-width: 100%; height: auto;">
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Edit lesson description -->
                <div class="lesson-description-container">
                    <label for="lessonDescription">Lesson Description:</label>
                    <textarea id="lessonDescription"
                        name="lessonDescription"><?php echo htmlspecialchars($lesson['lessonDescription'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

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
                        ?>
                        <div class="content-section" id="section<?php echo $sectionCount; ?>">
                            <div class="section-header">
                                <span class="section-title">Section <?php echo $sectionCount; ?></span>
                                <button type="button" class="delete-button"
                                    onclick="removeSection(<?php echo $sectionCount; ?>)">Delete</button>
                            </div>
                            <!-- Section background color selection -->
                            <label for="sectionColor<?php echo $sectionCount; ?>">Section Background Color:</label>
                            <input type="color" id="sectionColor<?php echo $sectionCount; ?>"
                                name="sectionColor<?php echo $sectionCount; ?>"
                                value="<?php echo htmlspecialchars($section['section_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                onchange="updateSectionColor(<?php echo $sectionCount; ?>)" />

                            <!-- Section content type selection -->
                            <label for="sectionContent<?php echo $sectionCount; ?>">Content Type:</label>
                            <select id="sectionContent<?php echo $sectionCount; ?>"
                                name="contentType[<?php echo $sectionCount; ?>]"
                                onchange="updateContent(<?php echo $sectionCount; ?>)">
                                <option value="">-- Select Content --</option>
                                <option value="text" <?php echo $section['contentType'] == 'text' ? 'selected' : ''; ?>>Text
                                </option>
                                <option value="image" <?php echo $section['contentType'] == 'image' ? 'selected' : ''; ?>>
                                    Image</option>
                                <option value="video" <?php echo $section['contentType'] == 'video' ? 'selected' : ''; ?>>
                                    Video</option>
                            </select>

                            <div id="content<?php echo $sectionCount; ?>" class="section-content">
                                <?php if ($section['contentType'] == 'text'): ?>
                                    <!-- Show text editor -->
                                    <textarea id="editor<?php echo $sectionCount; ?>"
                                        name="content<?php echo $sectionCount; ?>"><?php echo htmlspecialchars(strip_tags($section['text_content']), ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    <label for="text_color<?php echo $sectionCount; ?>">Text Color:</label>
                                    <input type="color" id="text_color<?php echo $sectionCount; ?>"
                                        name="text_color<?php echo $sectionCount; ?>"
                                        value="<?php echo htmlspecialchars($section['text_color'], ENT_QUOTES, 'UTF-8'); ?>"
                                        onchange="updateTextColor(<?php echo $sectionCount; ?>)" />

                                    <script>
                                        ClassicEditor
                                            .create(document.querySelector('#editor<?php echo $sectionCount; ?>'))
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    </script>
                                <?php elseif ($section['contentType'] == 'image'): ?>
                                    <!-- Show image upload option -->
                                    <input type="file" name="contentImage<?php echo $sectionCount; ?>" accept="image/*" />
                                    <div id="preview<?php echo $sectionCount; ?>" class="preview">
                                        <?php if (!empty($section['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($section['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                                alt="Image Preview" style="max-width: 100%; height: auto;">
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($section['contentType'] == 'video'): ?>
                                    <!-- Show video upload option -->
                                    <input type="file" name="contentVideo<?php echo $sectionCount; ?>" accept="video/*" />
                                    <div id="preview<?php echo $sectionCount; ?>" class="preview">
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

</html>