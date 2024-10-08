
        // รายละเอียดของบทเรียน

        ClassicEditor
            .create(document.querySelector('#lessonDescription'))  // สร้าง CKEditor สำหรับ textarea
            .catch(error => {
                console.error(error);  // แสดง error ถ้าหากเกิดข้อผิดพลาดในการสร้าง CKEditor
            });




        // ตั้งค่าสีพื้นหลังของหน้าและคอนเทนเนอร์เมื่อโหลดหน้า
        window.onload = function () {
            updatePageColor(); // ตั้งค่าสีพื้นหลังของหน้า
            updateContainerColor(); // ตั้งค่าสีพื้นหลังของคอนเทนเนอร์

            // วนลูปผ่านทุก section และตั้งค่าสีพื้นหลังของแต่ละ section
            const sectionElements = document.querySelectorAll('.content-section');
            sectionElements.forEach(section => {
                const sectionID = section.id.replace('section', '');
                updateSectionColor(sectionID);
            });
        };

        // ฟังก์ชันในการตั้งค่าสีพื้นหลังของหน้า
        function updatePageColor() {
            document.body.style.backgroundColor = document.getElementById('page_color').value;
        }

        // ฟังก์ชันในการตั้งค่าสีพื้นหลังของคอนเทนเนอร์
        function updateContainerColor() {
            document.getElementById('lessonContainer').style.backgroundColor = document.getElementById('container_color').value;
        }

        // ฟังก์ชันในการตั้งค่าสีตัวอักษรของ lessonName และ lessonDescription
        function changeLessonTextColor() {
            const textColor = document.getElementById('text_color').value;
            document.getElementById('lessonName').style.color = textColor;
            document.getElementById('lessonDescription').style.color = textColor;
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

        // ฟังก์ชันสำหรับอัปเดตเนื้อหาของ section ตามประเภทที่เลือก
        function updateContent(sectionNumber) {
            const contentType = document.getElementById('sectionContent' + sectionNumber).value;
            const contentDiv = document.getElementById('content' + sectionNumber);
            contentDiv.innerHTML = '';

            if (contentType === 'text') {
                const textarea = document.createElement('textarea');
                textarea.id = 'editor' + sectionNumber;
                textarea.name = 'text_content';
                contentDiv.appendChild(textarea);

                ClassicEditor
                    .create(textarea)
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            document.querySelector('#editor' + sectionNumber).value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });

            } else if (contentType === 'image') {
                const imageInput = document.createElement('input');
                imageInput.type = 'file';
                imageInput.name = 'image_content';
                imageInput.accept = 'image/*';
                imageInput.onchange = function (event) {
                    previewImage(event, sectionNumber);
                };
                contentDiv.appendChild(imageInput);

                const preview = document.createElement('div');
                preview.id = 'preview' + sectionNumber;
                preview.className = 'preview';
                contentDiv.appendChild(preview);

            } else if (contentType === 'video') {
                const videoInput = document.createElement('input');
                videoInput.type = 'file';
                videoInput.name = 'video_content';
                videoInput.accept = 'video/*';
                videoInput.onchange = function (event) {
                    previewVideo(event, sectionNumber);  // Call the preview function for video
                };
                contentDiv.appendChild(videoInput);

                const preview = document.createElement('div');
                preview.id = 'preview' + sectionNumber;
                preview.className = 'preview';
                contentDiv.appendChild(preview);
            }
        }

        // ฟังก์ชันสำหรับแสดงตัวอย่างภาพ
        function previewImage(event, sectionNumber) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('preview' + sectionNumber);
                preview.innerHTML = '<img src="' + reader.result + '" alt="Image Preview" style="max-width: 100%; height: auto;">';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // ฟังก์ชันสำหรับแสดงตัวอย่างภาพปก
        function previewCoverImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const preview = document.getElementById('coverImagePreview');
                preview.innerHTML = '<img src="' + reader.result + '" alt="Cover Image Preview" style="max-width: 100%; height: auto;">';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // ฟังก์ชันสำหรับแสดงตัวอย่างวิดีโอ
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

        // ฟังก์ชันสำหรับลบ section
        function removeSection(sectionNumber) {
            const section = document.getElementById('section' + sectionNumber);
            if (section) {
                // ยืนยันการลบ
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ส่งคำขอลบไปยัง server
                        fetch('section_delete.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'section_id=' + sectionNumber
                        })
                            .then(response => response.text())
                            .then(data => {
                                Swal.fire(
                                    'Deleted!',
                                    'Your section has been deleted.',
                                    'success'
                                );
                                section.remove();
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the section.',
                                    'error'
                                );
                            });
                    }
                });
            }
        }

        // ฟังก์ชันสำหรับเพิ่ม section ใหม่
        document.getElementById('addSectionBtn').addEventListener('click', () => {
            const sectionCount = document.querySelectorAll('.content-section').length + 1;
            const newSection = document.createElement('div');
            newSection.className = 'content-section';
            newSection.id = 'new_section' + sectionCount; // ใช้ ID ชั่วคราวสำหรับ section ใหม่

            newSection.innerHTML = `
                <form action="section_create.php" method="post" enctype="multipart/form-data">
                    <div class="section-header">
                        <span class="section-title">Section ${sectionCount}</span>
                        <button type="button" class="delete-button" onclick="removeNewSection('new_section${sectionCount}')">Delete</button>
                    </div>
                    <!-- Section background color selection -->
                    <label for="sectionColor_new_${sectionCount}">Section Background Color:</label>
                    <input type="color" id="sectionColor_new_${sectionCount}" name="section_color"
                        onchange="updateSectionColorNew('new_section${sectionCount}')" />

                    <!-- Section content type selection -->
                    <label for="sectionContent_new_${sectionCount}">Content Type:</label>
                    <select id="sectionContent_new_${sectionCount}" name="content_type"
                        onchange="updateContentNew('new_section${sectionCount}')">
                        <option value="">-- Select Content --</option>
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>

                    <div id="content_new_${sectionCount}" class="section-content"></div>

                    <button type="submit" class="save-button">Save Section</button>
                </form>
            `;
            document.getElementById('sections-container').appendChild(newSection);
        });

        // ฟังก์ชันสำหรับลบ section ใหม่ที่ยังไม่ได้บันทึก
        function removeNewSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.remove();
            }
        }

        // ฟังก์ชันสำหรับอัปเดตสีของ section ใหม่
        function updateSectionColorNew(sectionId) {
            const section = document.getElementById(sectionId);
            const colorInput = section.querySelector('input[type="color"]');
            section.style.backgroundColor = colorInput.value;
        }

        // ฟังก์ชันสำหรับอัปเดตเนื้อหาของ section ใหม่ตามประเภทที่เลือก
        function updateContentNew(sectionId) {
            const section = document.getElementById(sectionId);
            const select = section.querySelector('select[name="content_type"]');
            const contentType = select.value;
            const contentDiv = section.querySelector('.section-content');
            contentDiv.innerHTML = '';

            if (contentType === 'text') {
                const textarea = document.createElement('textarea');
                textarea.name = 'text_content';
                contentDiv.appendChild(textarea);

                ClassicEditor
                    .create(textarea)
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            textarea.value = editor.getData();
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });

            } else if (contentType === 'image') {
                const imageInput = document.createElement('input');
                imageInput.type = 'file';
                imageInput.name = 'image_content';
                imageInput.accept = 'image/*';
                imageInput.onchange = function (event) {
                    previewImage(event, sectionId.replace('new_section', ''));
                };
                contentDiv.appendChild(imageInput);

                const preview = document.createElement('div');
                preview.className = 'preview';
                contentDiv.appendChild(preview);

            } else if (contentType === 'video') {
                const videoInput = document.createElement('input');
                videoInput.type = 'file';
                videoInput.name = 'video_content';
                videoInput.accept = 'video/*';
                videoInput.onchange = function (event) {
                    previewVideo(event, sectionId.replace('new_section', ''));
                };
                contentDiv.appendChild(videoInput);

                const preview = document.createElement('div');
                preview.className = 'preview';
                contentDiv.appendChild(preview);
            }
        }