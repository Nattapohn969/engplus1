<?php
// Include database connection
include('connect.php');

// Get the lesson ID from the URL or POST
$lessonID = isset($_GET['lessonID']) ? $_GET['lessonID'] : 0;

if ($lessonID > 0) {
    // Fetch the lesson details from the database
    $query = "SELECT * FROM lessons WHERE lessonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lessonID);
    $stmt->execute();
    $result = $stmt->get_result();
    $lesson = $result->fetch_assoc();

    // Fetch section data
    $sectionQuery = "SELECT * FROM sections WHERE lessonID = ?";
    $sectionStmt = $conn->prepare($sectionQuery);
    $sectionStmt->bind_param("i", $lessonID);
    $sectionStmt->execute();
    $sections = $sectionStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/styles.css" rel="stylesheet" />
    <title>Edit Lesson</title>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Edit Lesson</h1>
        </div>

        <!-- Form for editing a lesson -->
        <form action="lesson_update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="lessonID" value="<?php echo $lesson['lessonID']; ?>" />

            <!-- Pre-filled lesson name and text color -->
            <div class="lesson-name-container">
                <label for="lessonName">Lesson Name:</label>
                <input type="text" id="lessonName" name="lessonName"
                    value="<?php echo htmlspecialchars($lesson['lessonName']); ?>" required />
                <label for="text_color">Text Color:</label>
                <input type="color" id="text_color" name="text_color" value="<?php echo $lesson['text_color']; ?>" />
            </div>

            <!-- Pre-filled sections -->
            <div id="sections-container">
                <?php foreach ($sections as $index => $section): ?>
                    <div class="content-section" id="section<?php echo $index + 1; ?>">
                        <input type="hidden" name="sectionID<?php echo $index + 1; ?>"
                            value="<?php echo $section['sectionID']; ?>" />
                        <div class="section-header">
                            <span class="section-title">Section <?php echo $index + 1; ?></span>
                            <button type="button" class="delete-button"
                                onclick="removeSection(<?php echo $index + 1; ?>)">Delete</button>
                        </div>
                        <!-- Background color picker for section -->
                        <label for="sectionColor<?php echo $index + 1; ?>">Section Background Color:</label>
                        <input type="color" id="sectionColor<?php echo $index + 1; ?>"
                            name="sectionColor<?php echo $index + 1; ?>" value="<?php echo $section['section_color']; ?>"
                            onchange="updateSectionColor(<?php echo $index + 1; ?>)" />

                        <!-- Pre-filled content type and content -->
                        <label for="sectionContent<?php echo $index + 1; ?>">Content:</label>
                        <select id="sectionContent<?php echo $index + 1; ?>" name="sectionContent<?php echo $index + 1; ?>"
                            onchange="updateContent(<?php echo $index + 1; ?>)">
                            <option value="text" <?php if ($section['contentType'] == 'text')
                                echo 'selected'; ?>>Text</option>
                            <option value="image" <?php if ($section['contentType'] == 'image')
                                echo 'selected'; ?>>Image</option>
                            <option value="video" <?php if ($section['contentType'] == 'video')
                                echo 'selected'; ?>>Video</option>
                        </select>

                        <div id="content<?php echo $index + 1; ?>" class="section-content">
                            <?php if ($section['contentType'] == 'text'): ?>
                                <textarea
                                    name="contentText<?php echo $index + 1; ?>"><?php echo htmlspecialchars($section['Content']); ?></textarea>
                            <?php elseif ($section['contentType'] == 'image'): ?>
                                <img src="uploads/<?php echo $section['Content']; ?>" alt="Section Image" />
                                <input type="file" name="contentImage<?php echo $index + 1; ?>" accept="image/*" />
                            <?php elseif ($section['contentType'] == 'video'): ?>
                                <video controls>
                                    <source src="uploads/<?php echo $section['Content']; ?>" />
                                </video>
                                <input type="file" name="contentVideo<?php echo $index + 1; ?>" accept="video/*" />
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Button to add new section -->
            <button type="button" class="add-section-btn" onclick="addNewSection()">Add New Section</button>
            <input type="submit" class="save-btn" value="Update Lesson" />
        </form>
    </div>

    <script>
        let sectionCount = document.querySelectorAll('.content-section').length; // Start with existing sections

        // Add a new section
        function addNewSection() {
            sectionCount++;

            const sectionsContainer = document.getElementById("sections-container");
            const newSection = document.createElement("div");
            newSection.className = "content-section";
            newSection.id = "section" + sectionCount;

            newSection.innerHTML = `
        <div class="section-header">
            <span class="section-title">Section ${sectionCount}</span>
            <button type="button" class="delete-button" onclick="removeSection(${sectionCount})">Delete</button>
        </div>
        <label for="sectionColor${sectionCount}">Section Background Color:</label>
        <input type="color" id="sectionColor${sectionCount}" name="sectionColor${sectionCount}" onchange="updateSectionColor(${sectionCount})" />
        <label for="sectionContent${sectionCount}">Content:</label>
        <select id="sectionContent${sectionCount}" name="sectionContent${sectionCount}" onchange="updateContent(${sectionCount})">
            <option value="">-- Select Content --</option>
            <option value="text">Text</option>
            <option value="image">Image</option>
            <option value="video">Video</option>
        </select>
        <div id="content${sectionCount}" class="section-content">
            <!-- Content will be added here based on selection -->
        </div>
    `;

            sectionsContainer.appendChild(newSection);
        }

        // Remove a section
        function removeSection(sectionNumber) {
            const section = document.getElementById("section" + sectionNumber);
            section.remove();
        }

        // Update the background color of a section
        function updateSectionColor(sectionNumber) {
            const sectionColorInput = document.getElementById("sectionColor" + sectionNumber);
            const section = document.getElementById("section" + sectionNumber);
            section.style.backgroundColor = sectionColorInput.value;
        }

        // Update content based on selected type (text, image, video)
        function updateContent(sectionNumber) {
            const selectElement = document.getElementById("sectionContent" + sectionNumber);
            const selectedValue = selectElement.value;
            const contentDiv = document.getElementById("content" + sectionNumber);

            contentDiv.innerHTML = ""; // Clear old content

            if (selectedValue === "text") {
                contentDiv.innerHTML = `
            <textarea id="contentText${sectionNumber}" name="contentText${sectionNumber}" placeholder="Enter your text here..."></textarea>
            <label for="textColor${sectionNumber}">Text Color:</label>
            <input type="color" id="textColor${sectionNumber}" name="textColor${sectionNumber}" onchange="updateTextColor(${sectionNumber})" />
        `;
            } else if (selectedValue === "image") {
                contentDiv.innerHTML = `
            <input type="file" name="contentImage${sectionNumber}" accept="image/*" onchange="previewFile(${sectionNumber}, 'image')">
            <div id="preview${sectionNumber}" class="preview"></div>
        `;
            } else if (selectedValue === "video") {
                contentDiv.innerHTML = `
            <input type="file" name="contentVideo${sectionNumber}" accept="video/*" onchange="previewFile(${sectionNumber}, 'video')">
            <div id="preview${sectionNumber}" class="preview"></div>
        `;
            }
        }

        // Preview selected file (image or video)
        function previewFile(sectionNumber, fileType) {
            const fileInput = document.querySelector(`input[name="content${fileType.charAt(0).toUpperCase() + fileType.slice(1)}${sectionNumber}"]`);
            const previewDiv = document.getElementById("preview" + sectionNumber);

            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    if (fileType === "image") {
                        previewDiv.innerHTML = `<img src="${e.target.result}" alt="Image preview" style="max-width: 200px;">`;
                    } else if (fileType === "video") {
                        previewDiv.innerHTML = `<video controls style="max-width: 200px;"><source src="${e.target.result}"></video>`;
                    }
                };

                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>