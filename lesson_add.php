<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet"> -->
    <link
        href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="addlesson.css" rel="stylesheet" />
    <!-- <link href="stylead.css" rel="stylesheet" /> -->
    <title>Add Lesson</title>
</head>
<style>
    .lesson-description-container {
        margin: 20px 0;
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

    .lesson-description-container label {
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .lesson-description-container textarea {
        width: 100%;
        height: 200px;
        padding: 10px;
        font-size: 1rem;
        border-radius: 5px;
        border: 1px solid #ccc;
    }


    .lesson-description-container {
        margin-bottom: 20px;
    }
</style>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="header">
            <h1>Add Lesson</h1>
        </div>

        <form action='lesson_insert.php' method="post" enctype="multipart/form-data">

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
                    <label for="lessonName">🪄Lesson Name:</label>
                    <input type="text" id="lessonName" name="lessonName" placeholder="Enter lesson name here..."
                        required />

                    <label for="text_color">Text Color:</label>
                    <input type="color" id="text_color" name="text_color" onchange="changeLessonTextColor()" />
                </div>

                <div class="cover-image-controls">
                    <label for="coverImage">Cover Image:</label>
                    <input type="file" id="coverImage" name="coverImage" accept="image/*" />
                    <div id="coverImagePreview" class="cover-image-preview"></div>
                </div>

                <div class="lesson-description-container">
                    <label for="lessonDescription">Lesson Description:</label>
                    <textarea id="lessonDescription" name="lessonDescription"
                        placeholder="Enter lesson description here..."></textarea>
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
        ClassicEditor
            .create(document.querySelector('#lessonDescription'),
                {
                    ckfinder:
                    {
                        uploadUrl: "{{route('ckeditor.upload',['_token'=>csrf_token()])}}",
                    }
                })
            .catch(error => {
                console.error(error);

            });



        //ck editor ของ รายละเอียดของบทเรียน
        // document.addEventListener("DOMContentLoaded", function () {
        //     ClassicEditor
        //         .create(document.querySelector('#lessonDescription'), {
        //             toolbar: [
        //                 'heading', '|',
        //                 'bold', 'italic', 'link', '|',
        //                 'fontColor', 'fontBackgroundColor', '|',
        //                 'bulletedList', 'numberedList', '|',
        //                 'imageUpload', 'blockQuote', 'insertTable', '|',
        //                 'undo', 'redo'
        //             ],
        //             language: 'en',
        //             ckfinder: {
        //                 uploadUrl: 'uploads/' // Update this to the correct path of your file upload handler
        //             }
        //         })
        //         .catch(error => {
        //             console.error('CKEditor initialization error for lesson description:', error);
        //         });
        // });


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
                        previewDiv.innerHTML = `<img src="${e.target.result}" alt="Image Preview" style="max-width: 100%; height: auto;">`;
                    } else if (type === "video") {
                        previewDiv.innerHTML = `<video controls src="${e.target.result}" style="max-width: 100%; height: auto;"></video>`;
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
                <div id="content${sectionCount}" class="section-content"></div>
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

        function previewCoverImage() {
            const fileInput = document.getElementById('coverImage');
            const previewDiv = document.getElementById('coverImagePreview');
            previewDiv.innerHTML = ""; // Clear previous preview

            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewDiv.innerHTML = `<img src="${e.target.result}" alt="Cover Image Preview" style="max-width: 100%; height: auto;">`;
                };

                reader.readAsDataURL(file);
            }
        }

        document.getElementById('coverImage').addEventListener('change', previewCoverImage);
    </script>
</body>

</html>