
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



        // ฟังก์ชันสำหรับลบส่วนที่ระบุ
        function removeSection(sectionNumber) {
            const section = document.getElementById('section' + sectionNumber);  // หาส่วนตามหมายเลข
            if (section) {
                section.remove();  // ลบส่วนที่พบ
                updateSectionNumbers();  // เรียกฟังก์ชันเพื่ออัปเดตหมายเลขส่วนหลังจากลบไปแล้ว
            }
        }

        // ฟังก์ชันสำหรับอัปเดตหมายเลขของแต่ละส่วน
        function updateSectionNumbers() {
            const sections = document.querySelectorAll('.content-section');  // เลือกทุกส่วนที่มีคลาส 'content-section'
            sections.forEach((section, index) => {
                const newSectionNumber = index + 1;  // คำนวณหมายเลขส่วนใหม่
                section.id = 'section' + newSectionNumber;  // อัปเดต ID ของส่วน

                // อัปเดตชื่อแสดงผลของส่วน
                section.querySelector('.section-title').textContent = 'Section ' + newSectionNumber;

                // อัปเดต ID สำหรับ input สีพื้นหลัง
                section.querySelector('input[type="color"]').id = 'sectionColor' + newSectionNumber;

                // อัปเดต ID สำหรับ select ชนิดเนื้อหา
                section.querySelector('select').id = 'sectionContent' + newSectionNumber;

                // อัปเดตแอตทริบิวต์ name สำหรับ select ชนิดเนื้อหา
                section.querySelector('select').name = 'contentType[' + newSectionNumber + ']';

                // อัปเดต ID ของ div สำหรับเนื้อหา
                section.querySelector('.section-content').id = 'content' + newSectionNumber;

                // อัปเดต event listener สำหรับการเปลี่ยนสี
                section.querySelector('input[type="color"]').setAttribute('onchange', `updateSectionColor(${newSectionNumber})`);
            });
        }

        // เพิ่ม event listener ให้กับปุ่มเพิ่มส่วนใหม่
        document.getElementById('addSectionBtn').addEventListener('click', () => {
            const sectionNumber = document.querySelectorAll('.content-section').length + 1;  // คำนวณหมายเลขส่วนใหม่
            const newSection = document.createElement('div');  // สร้าง div ใหม่สำหรับส่วน
            newSection.className = 'content-section';  // กำหนดคลาสให้กับ div ใหม่
            newSection.id = 'section' + sectionNumber;  // กำหนด ID ให้กับ div ใหม่
            newSection.innerHTML = `
        <div class="section-header">
            <span class="section-title">Section ${sectionNumber}</span>
            <button type="button" class="delete-button" onclick="removeSection(${sectionNumber})">Delete</button> <!-- ปุ่มลบ -->
        </div>
        <label for="sectionColor${sectionNumber}">Section Background Color:</label>
        <input type="color" id="sectionColor${sectionNumber}" name="sectionColor${sectionNumber}" 
            onchange="updateSectionColor(${sectionNumber})" /> <!-- Input สำหรับเลือกสีพื้นหลัง -->
        <label for="sectionContent${sectionNumber}">Content Type:</label>
        <select id="sectionContent${sectionNumber}" name="contentType[${sectionNumber}]"
            onchange="updateContent(${sectionNumber})"> <!-- Select สำหรับเลือกชนิดเนื้อหา -->
            <option value="">-- Select Content --</option>
            <option value="text">Text</option>
            <option value="image">Image</option>
            <option value="video">Video</option>
        </select>
        <div id="content${sectionNumber}" class="section-content"></div> <!-- Div สำหรับเนื้อหาที่จะเพิ่ม -->
    `;
            document.getElementById('sections-container').appendChild(newSection);  // เพิ่มส่วนใหม่ลงใน container
        });
