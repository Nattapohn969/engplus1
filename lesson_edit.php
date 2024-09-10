<?php
include 'connect.php';

// Get lessonID from URL
$lessonID = $_GET['lessonID'] ?? 0;

// Fetch lesson data
$stmt = $conn->prepare("SELECT * FROM lessons WHERE lessonID = ?");
$stmt->bind_param("i", $lessonID);
$stmt->execute();
$lessonResult = $stmt->get_result();
$lesson = $lessonResult->fetch_assoc();

// Fetch sections data
$stmt = $conn->prepare("SELECT * FROM sections WHERE lessonID = ?");
$stmt->bind_param("i", $lessonID);
$stmt->execute();
$sectionsResult = $stmt->get_result();
$sections = $sectionsResult->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles1.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/ckeditor.js"></script>
    <title>Edit Lesson</title>
    <style>
        .preview {
            margin-top: 10px;
        }

        .preview img,
        .preview video {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Edit Lesson</h1>
        </div>

        <!-- Form for editing a lesson -->
        <form action="lesson_update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="lessonID" value="<?php echo htmlspecialchars($lessonID); ?>" />

            <div class="color-controls">
                <label for="page_color">Page Background Color:</label>
                <input type="color" id="page_color" name="page_color"
                    value="<?php echo htmlspecialchars($lesson['page_color']); ?>" onchange="updatePageColor()" />
            </div>

            <div class="lesson-container" id="lessonContainer"
                style="background-color: <?php echo htmlspecialchars($lesson['container_color']); ?>;">
                <div class="lesson-name-container">
                    <label for="lessonName">Lesson Name:</label>
                    <input type="text" id="lessonName" name="lessonName"
                        value="<?php echo htmlspecialchars($lesson['lessonName']); ?>" required />
                    <label for="text_color">Text Color:</label>
                    <input type="color" id="text_color" name="text_color"
                        value="<?php echo htmlspecialchars($lesson['text_color']); ?>"
                        onchange="changeLessonTextColor()" />
                </div>

                <!-- Container for lesson sections -->
                <div id="sections-container">
                    <?php foreach ($sections as $section): ?>
                        <div class="content-section" id="section<?php echo htmlspecialchars($section['section_num']); ?>">
                            <div class="section-header">
                                <span class="section-title">Section
                                    <?php echo htmlspecialchars($section['section_num']); ?></span>
                                <button type="button" class="delete-button"
                                    onclick="removeSection(<?php echo htmlspecialchars($section['section_num']); ?>)">Delete</button>
                            </div>
                            <label for="sectionColor<?php echo htmlspecialchars($section['section_num']); ?>">Section
                                Background Color:</label>
                            <input type="color" id="sectionColor<?php echo htmlspecialchars($section['section_num']); ?>"
                                name="sectionColor<?php echo htmlspecialchars($section['section_num']); ?>"
                                value="<?php echo htmlspecialchars($section['section_color']); ?>"
                                onchange="updateSectionColor(<?php echo htmlspecialchars($section['section_num']); ?>)" />
                            <label for="sectionContent<?php echo htmlspecialchars($section['section_num']); ?>">Content
                                Type:</label>
                            <select id="sectionContent<?php echo htmlspecialchars($section['section_num']); ?>"
                                name="contentType[<?php echo htmlspecialchars($section['section_num']); ?>]"
                                onchange="updateContent(<?php echo htmlspecialchars($section['section_num']); ?>)">
                                <option value="">-- Select Content --</option>
                                <option value="text" <?php if ($section['contentType'] == 'text')
                                    echo 'selected'; ?>>Text
                                </option>
                                <option value="image" <?php if ($section['contentType'] == 'image')
                                    echo 'selected'; ?>>Image
                                </option>
                                <option value="video" <?php if ($section['contentType'] == 'video')
                                    echo 'selected'; ?>>Video
                                </option>
                            </select>
                            <div id="content<?php echo htmlspecialchars($section['section_num']); ?>"
                                class="section-content">
                                <!-- Content will be displayed here -->
                                <?php if ($section['contentType'] == 'text'): ?>
                                    <textarea id="contentText<?php echo htmlspecialchars($section['section_num']); ?>"
                                        name="contentText<?php echo htmlspecialchars($section['section_num']); ?>"><?php echo htmlspecialchars($section['content']); ?></textarea>
                                    <script>
                                        ClassicEditor
                                            .create(document.querySelector(`#contentText<?php echo htmlspecialchars($section['section_num']); ?>`))
                                            .catch(error => {
                                                console.error(error);
                                            });
                                    </script>
                                <?php elseif ($section['contentType'] == 'image'): ?>
                                    <input type="file"
                                        name="contentImage<?php echo htmlspecialchars($section['section_num']); ?>"
                                        accept="image/*"
                                        onchange="previewFile(<?php echo htmlspecialchars($section['section_num']); ?>, 'image')">
                                    <div id="preview<?php echo htmlspecialchars($section['section_num']); ?>" class="preview">
                                        <img src="<?php echo htmlspecialchars($section['image_url']); ?>" alt="Image Preview">
                                    </div>
                                <?php elseif ($section['contentType'] == 'video'): ?>
                                    <input type="file"
                                        name="contentVideo<?php echo htmlspecialchars($section['section_num']); ?>"
                                        accept="video/*"
                                        onchange="previewFile(<?php echo htmlspecialchars($section['section_num']); ?>, 'video')">
                                    <div id="preview<?php echo htmlspecialchars($section['section_num']); ?>" class="preview">
                                        <video controls src="<?php echo htmlspecialchars($section['video_url']); ?>"></video>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="add-section-btn" onclick="addNewSection()">Add New Section</button>
                <input type="submit" class="save-btn" value="Save Changes" />
            </div>
        </form>
    </div>

    <script>
        // Include previous JavaScript functions here

        function updateContent(sectionNum) {
            const selectElement = document.getElementById(`sectionContent${sectionNum}`);
            const selectedValue = selectElement.value;
            const contentDiv = document.getElementById(`content${sectionNum}`);

            contentDiv.innerHTML = ""; // Clear old content

            if (selectedValue === "text") {
                contentDiv.innerHTML = `
                    <label for="contentText${sectionNum}">Text Content:</label>
                    <textarea id="contentText${sectionNum}" name="contentText${sectionNum}"></textarea>
                `;
                ClassicEditor
                    .create(document.querySelector(`#contentText${sectionNum}`))
                    .catch(error => {
                        console.error(error);
                    });
            } else if (selectedValue === "image") {
                contentDiv.innerHTML = `
                    <label for="contentImage${sectionNum}">Image:</label>
                    <input type="file" name="contentImage${sectionNum}" accept="image/*" onchange="previewFile(${sectionNum}, 'image')">
                    <div id="preview${sectionNum}" class="preview"></div>
                `;
            } else if (selectedValue === "video") {
                contentDiv.innerHTML = `
                    <label for="contentVideo${sectionNum}">Video:</label>
                    <input type="file" name="contentVideo${sectionNum}" accept="video/*" onchange="previewFile(${sectionNum}, 'video')">
                    <div id="preview${sectionNum}" class="preview"></div>
                `;
            }
        }

        function previewFile(sectionNum, type) {
            const fileInput = document.querySelector(
                `input[name="content${type.charAt(0).toUpperCase() + type.slice(1)}${sectionNum}"]`
            );
            const previewDiv = document.getElementById(`preview${sectionNum}`);

            previewDiv.innerHTML = ""; // Clear old preview

            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    if (type === "image") {
                        previewDiv.innerHTML = `<img src="${e.target.result}" alt="Image Preview">`;
                    } else if (type === "video") {
                        previewDiv.innerHTML = `<video controls src="${e.target.result}"></video>`;
                    }
                };

                reader.readAsDataURL(file);
            }
        }

        function updatePageColor() {
            const pageColor = document.getElementById('page_color').value;
            document.getElementById('lessonContainer').style.backgroundColor = pageColor;
        }

        function updateSectionColor(sectionNum) {
            const sectionColor = document.getElementById(`sectionColor${sectionNum}`).value;
            document.getElementById(`section${sectionNum}`).style.backgroundColor = sectionColor;
        }

        function changeLessonTextColor() {
            const textColor = document.getElementById('text_color').value;
            document.querySelector('#lessonContainer').style.color = textColor;
        }
    </script>
</body>

</html>