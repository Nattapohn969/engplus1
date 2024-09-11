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
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
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

        .content-section {
            margin-bottom: 20px;
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

            <!-- Lesson Background Color -->
            <div class="color-controls">
                <label for="page_color">Page Background Color:</label>
                <input type="color" id="page_color" name="page_color"
                    value="<?php echo htmlspecialchars($lesson['page_color']); ?>" onchange="updatePageColor()" />
            </div>

            <!-- Lesson Container -->
            <div class="lesson-container" id="lessonContainer"
                style="background-color: <?php echo htmlspecialchars($lesson['container_color']); ?>;">
                <div class="lesson-name-container">
                    <label for="lessonName">Lesson Name:</label>
                    <input type="text" id="lessonName" name="lessonName"
                        value="<?php echo htmlspecialchars($lesson['lessonName']); ?>" required
                        style="color: <?php echo htmlspecialchars($lesson['text_color']); ?>;" />
                    <label for="text_color">Text Color:</label>
                    <input type="color" id="text_color" name="text_color"
                        value="<?php echo htmlspecialchars($lesson['text_color']); ?>"
                        onchange="changeLessonTextColor()" />
                </div>

                <!-- Sections Container -->
                <div id="sections-container">
                    <?php foreach ($sections as $section): ?>
                    <div class="content-section" id="section<?php echo htmlspecialchars($section['section_num']); ?>"
                        style="background-color: <?php echo htmlspecialchars($section['section_color']); ?>;">
                        <div class="section-header">
                            <span class="section-title">Section <?php echo htmlspecialchars($section['section_num']); ?></span>
                            <button type="button" class="delete-button"
                                onclick="removeSection(<?php echo htmlspecialchars($section['section_num']); ?>)">Delete</button>
                        </div>

                        <!-- Section Background Color -->
                        <label for="sectionColor<?php echo htmlspecialchars($section['section_num']); ?>">Section
                            Background Color:</label>
                        <input type="color" id="sectionColor<?php echo htmlspecialchars($section['section_num']); ?>"
                            name="sectionColor<?php echo htmlspecialchars($section['section_num']); ?>"
                            value="<?php echo htmlspecialchars($section['section_color']); ?>"
                            onchange="updateSectionColor(<?php echo htmlspecialchars($section['section_num']); ?>)" />

                        <!-- Content Type -->
                        <label for="sectionContent<?php echo htmlspecialchars($section['section_num']); ?>">Content
                            Type:</label>
                        <select id="sectionContent<?php echo htmlspecialchars($section['section_num']); ?>"
                            name="contentType[<?php echo htmlspecialchars($section['section_num']); ?>]"
                            onchange="updateContent(<?php echo htmlspecialchars($section['section_num']); ?>)">
                            <option value="">-- Select Content --</option>
                            <option value="text" <?php if ($section['contentType'] == 'text') echo 'selected'; ?>>Text
                            </option>
                            <option value="image" <?php if ($section['contentType'] == 'image') echo 'selected'; ?>>Image
                            </option>
                            <option value="video" <?php if ($section['contentType'] == 'video') echo 'selected'; ?>>Video
                            </option>
                        </select>

                        <!-- Display Content (Text, Image, Video) -->
                        <div id="content<?php echo htmlspecialchars($section['section_num']); ?>" class="section-content">
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
                            <img src="<?php echo htmlspecialchars($section['image_url']); ?>" alt="Image Preview">
                            <?php elseif ($section['contentType'] == 'video'): ?>
                            <video controls src="<?php echo htmlspecialchars($section['video_url']); ?>"></video>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Add New Section Button -->
                <button type="button" class="add-section-btn" onclick="addNewSection()">Add New Section</button>
                <input type="submit" class="save-btn" value="Save Changes" />
            </div>
        </form>
    </div>

    <!-- JavaScript functions for color changes -->
    <script>
        // Update page background color in the lesson container
        function updatePageColor() {
            const pageColor = document.getElementById("page_color").value;
            const lessonContainer = document.getElementById("lessonContainer");
            lessonContainer.style.backgroundColor = pageColor;
        }

        // Update section background color
        function updateSectionColor(sectionNumber) {
            const sectionColor = document.getElementById("sectionColor" + sectionNumber).value;
            const section = document.getElementById("section" + sectionNumber);
            section.style.backgroundColor = sectionColor;
        }

        // Change lesson text color
        function changeLessonTextColor() {
            const textColor = document.getElementById("text_color").value;
            const lessonNameInput = document.getElementById("lessonName");
            lessonNameInput.style.color = textColor;
        }

        // Additional functions for adding/removing sections and updating content can be added here...
    </script>
</body>

</html>
