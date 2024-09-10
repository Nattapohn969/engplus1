document.addEventListener("DOMContentLoaded", function() {
    // สมมติว่าคุณดึงข้อมูล JSON ของบทเรียนและส่วนต่าง ๆ จากฐานข้อมูล
    const lessonData = lessons;
    const sectionsData = sections;

    // เติมข้อมูลบทเรียนในฟอร์ม
    document.getElementById('lessonName').value = lessonData.lessonName;
    document.getElementById('page_color').value = lessonData.page_color;
    document.getElementById('text_color').value = lessonData.text_color;
    document.getElementById('container_color').value = lessonData.container_color;
    changeLessonTextColor();

    // เติมข้อมูล sections ในฟอร์ม
    sectionsData.forEach((section, index) => {
        if (index > 0) addNewSection();  // เพิ่ม section ถ้าไม่ใช่ section แรก
        const sectionNumber = index + 1;

        document.getElementById(`sectionColor${sectionNumber}`).value = section.section_color;
        updateSectionColor(sectionNumber);

        // เติมเนื้อหาใน section
        document.getElementById(`sectionContent${sectionNumber}`).value = section.content_type;
        updateContent(sectionNumber);

        if (section.content_type === 'text') {
            document.getElementById(`contentText${sectionNumber}`).value = section.content;
            document.getElementById(`textColor${sectionNumber}`).value = section.text_color;
            updateTextColor(sectionNumber);
        } else if (section.content_type === 'image') {
            // แสดง preview ของรูปภาพ
            const previewDiv = document.getElementById(`preview${sectionNumber}`);
            previewDiv.innerHTML = `<img src="${section.image_url}" alt="Image Preview">`;
        } else if (section.content_type === 'video') {
            // แสดง preview ของวิดีโอ
            const previewDiv = document.getElementById(`preview${sectionNumber}`);
            previewDiv.innerHTML = `<video controls src="${section.video_url}"></video>`;
        } else if (section.content_type === 'topic') {
            section.topics.forEach((topic, topicIndex) => {
                topicCount++;
                const topicId = `topic${topicCount}`;
                const topicInputDiv = document.getElementById(`content${sectionNumber}`);

                topicInputDiv.innerHTML += `
                    <label for="${topicId}">Topic Title:</label>
                    <input type="text" id="${topicId}" name="topic${sectionNumber}[]" value="${topic.title}" />
                    <label for="${topicId}_color">Topic Text Color:</label>
                    <input type="color" id="${topicId}_color" name="topic${sectionNumber}_color[]" value="${topic.text_color}" />
                `;
                updateTopicTextColor(topicId);
            });
        }
    });
});

// ฟังก์ชันที่เหลือจาก JavaScript เดิม
function updateContent(sectionNumber) {
    const selectElement = document.getElementById("sectionContent" + sectionNumber); 
    const selectedValue = selectElement.value; 
    const contentDiv = document.getElementById("content" + sectionNumber);

    contentDiv.innerHTML = ""; // ล้างข้อมูลก่อนหน้า

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
    } else if (selectedValue === "topic") {
        topicCount++;
        const topicId = "topic" + topicCount;
        contentDiv.innerHTML = `
            <label for="${topicId}">Topic Title:</label>
            <input type="text" id="${topicId}" name="topic${sectionNumber}[]" placeholder="Enter topic title here..." />
            <label for="${topicId}_color">Topic Text Color:</label>
            <input type="color" id="${topicId}_color" name="topic${sectionNumber}_color" onchange="updateTopicTextColor('${topicId}')" />
        `;
    }
}

function previewFile(sectionNumber, type) {
    const fileInput = document.querySelector(
        `input[name="content${type.charAt(0).toUpperCase() + type.slice(1)}${sectionNumber}"]`
    );
    const previewDiv = document.getElementById(`preview${sectionNumber}`);

    previewDiv.innerHTML = ""; // ล้าง preview ก่อนหน้า

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
        <span class="section-title">Section ${sectionCount}</span>

        <label for="sectionColor${sectionCount}">Section Background Color:</label>
        <input type="color" id="sectionColor${sectionCount}" name="sectionColor${sectionCount}" onchange="updateSectionColor(${sectionCount})" />

        <label for="sectionContent${sectionCount}">Content:</label>
        <select id="sectionContent${sectionCount}" name="sectionContent${sectionCount}" onchange="updateContent(${sectionCount})">
            <option value="">-- Select Content --</option>
            <option value="text">Text</option>
            <option value="image">Image</option>
            <option value="video">Video</option>
            <option value="topic">Add Topic</option>
        </select>

        <div id="content${sectionCount}" class="section-content">
            <!-- Content -->
        </div>
        <button type="button" class="delete-button" onclick="removeSection(${sectionCount})">Delete</button>
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


function updateSectionColor(sectionNumber) {
    const sectionColorInput = document.getElementById("sectionColor" + sectionNumber);
    const section = document.getElementById("section" + sectionNumber);
    section.style.backgroundColor = sectionColorInput.value;
}

function updateTextColor(sectionNumber) {
    const colorInput = document.getElementById("textColor" + sectionNumber);
    const textArea = document.getElementById("contentText" + sectionNumber);
    textArea.style.color = colorInput.value;
}

function updateTopicTextColor(topicId) {
    const colorInput = document.getElementById(topicId + '_color');
    const topicInput = document.getElementById(topicId);
    topicInput.style.color = colorInput.value;
}

function updateContainerColor() {
    const containerColor = document.getElementById("container_color").value;
    document.getElementById("lessonContainer").style.backgroundColor = containerColor;
}

function changeLessonTextColor() {
    // Get the color from the input
    const color = document.getElementById('text_color').value;
    
    // Set the color for the lesson name
    document.getElementById('lessonName').style.color = color;
}
