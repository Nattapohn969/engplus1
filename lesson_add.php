<?php
include 'connect.php';
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
    <link href="css/addlesson.css" rel="stylesheet" />
    <title>Add Lesson</title>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Add Lesson</h1>
        </div>

        <form action="lesson_insert.php" method="post" enctype="multipart/form-data">
            <div class="color-controls">
                <label for="page_color">Page Background Color:</label>
                <input type="color" id="page_color" name="page_color" onchange="updatePageColor()" />
            </div>
            <div class="lesson-container" id="lessonContainer">
                <div class="color-controls">
                    <label for="container_color">Container Color:</label>
                    <input type="color" id="container_color" name="container_color" onchange="updateContainerColor()" />
                </div>

                <div class="lesson-name-container">
                    <label for="lessonName">Lesson Name:</label>
                    <input type="text" id="lessonName" name="lessonName" placeholder="Enter lesson name here..."
                        required />

                    <label for="text_color">Text Color:</label>
                    <input type="color" id="text_color" name="text_color" onchange="changeLessonTextColor()" />
                </div>

                <div id="sections-container">
                    <div class="content-section" id="section1">
                        <div class="section-header">
                            <span class="section-title">Section 1</span>
                            <button type="button" class="delete-button" onclick="removeSection(1)">Delete</button>
                        </div>
                        <label for="sectionColor1">Section Background Color:</label>
                        <input type="color" id="sectionColor1" name="sectionColor1" onchange="updateSectionColor(1)" />

                        <label for="sectionContent1">Content Type:</label>
                        <select id="sectionContent1" name="contentType[1]" onchange="updateContent(1)">
                            <option value="">-- Select Content --</option>
                            <option value="text">Text</option>
                            <option value="image">Image</option>
                            <option value="video">Video</option>
                        </select>
                        <div id="content1" class="section-content"></div>
                    </div>
                </div>

                <button type="button" class="add-section-btn" onclick="addNewSection()">Add New Section</button>
                <input type="submit" class="save-btn" value="Save Lesson" />
            </div>
        </form>
    </div>

    <script>
        let sectionCount = 1;

        function initializeEditor(sectionNumber) {
            ClassicEditor
                .create(document.querySelector(`#contentText${sectionNumber}`), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'link', '|',
                        'fontColor', 'fontBackgroundColor', '|',
                        'bulletedList', 'numberedList', '|',
                        'imageUpload', 'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ],
                    language: 'en',
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });
        }

        function updateContent(sectionNumber) {
            const selectElement = document.getElementById("sectionContent" + sectionNumber);
            const selectedValue = selectElement.value;
            const contentDiv = document.getElementById("content" + sectionNumber);

            contentDiv.innerHTML = "";

            if (selectedValue === "text") {
                contentDiv.innerHTML = `
                    <textarea id="contentText${sectionNumber}" name="contentText${sectionNumber}" placeholder="Enter your text here..."></textarea>
                    <label for="textColor${sectionNumber}">Text Color:</label>
                    <input type="color" id="textColor${sectionNumber}" name="textColor${sectionNumber}" onchange="updateTextColor(${sectionNumber})" />
                `;
                initializeEditor(sectionNumber);
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

        function previewFile(sectionNumber, type) {
            const fileInput = document.querySelector(
                `input[name="content${type.charAt(0).toUpperCase() + type.slice(1)}${sectionNumber}"]`
            );
            const previewDiv = document.getElementById(`preview${sectionNumber}`);

            previewDiv.innerHTML = "";

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
                <label for="sectionContent${sectionCount}">Content Type:</label>
                <select id="sectionContent${sectionCount}" name="contentType[${sectionCount}]" onchange="updateContent(${sectionCount})">
                    <option value="">-- Select Content --</option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
                <div id="content${sectionCount}" class="section-content">
                </div>
            `;

            sectionsContainer.appendChild(newSection);
        }

        function removeSection(sectionNumber) {
            const section = document.getElementById("section" + sectionNumber);
            section.remove();
        }

        function updatePageColor() {
            const pageColor = document.getElementById("page_color").value;
            document.body.style.backgroundColor = pageColor;
        }

        function updateContainerColor() {
            const containerColor = document.getElementById("container_color").value;
            const container = document.getElementById("lessonContainer");
            container.style.backgroundColor = containerColor;
        }

        function updateSectionColor(sectionNumber) {
            const sectionColor = document.getElementById("sectionColor" + sectionNumber).value;
            const section = document.getElementById("section" + sectionNumber);
            section.style.backgroundColor = sectionColor;
        }

        function changeLessonTextColor() {
            const textColor = document.getElementById("text_color").value;
            const lessonNameInput = document.getElementById("lessonName");
            lessonNameInput.style.color = textColor;
        }
    </script>
</body>

</html>