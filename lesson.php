<?php
include 'connect.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// รับ lessonID จาก URL และตรวจสอบว่าเป็นค่าตัวเลข
$lessonID = isset($_GET['lessonID']) ? intval($_GET['lessonID']) : 0;
if ($lessonID <= 0) {
    die('Invalid lesson ID');
}

// ดึงข้อมูลบทเรียน
$stmt = $conn->prepare("SELECT lessonName, lessonDescription, page_color, container_color, text_color, cover_image FROM lessons WHERE lessonID = ?");
$stmt->bind_param("i", $lessonID);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();
if (!$lesson) {
    die('Lesson not found');
}

// Retrieve testType_ID from the lesson data
$testType_ID = isset($lesson['testType_ID']) ? intval($lesson['testType_ID']) : 1;

// ดึงข้อมูล sections
$stmt = $conn->prepare("SELECT * FROM sections WHERE lessonID = ? ORDER BY section_num");
$stmt->bind_param("i", $lessonID);
$stmt->execute();
$sections = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400&display=swap" rel="stylesheet">
    <link href="css/style1.css" rel="stylesheet" />
    <title><?php echo htmlspecialchars($lesson['lessonName'], ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        /* Base styles for desktop and larger screens */
        body {
            background-color:
                <?php echo htmlspecialchars($lesson['page_color'], ENT_QUOTES, 'UTF-8'); ?>
            ;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            background-color:
                <?php echo htmlspecialchars($lesson['container_color'], ENT_QUOTES, 'UTF-8'); ?>
            ;
            padding: 20px;
            border-radius: 10px;
            max-width: 1200px;
            margin: 20px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            text-align: center;
            color:
                <?php echo htmlspecialchars($lesson['text_color'], ENT_QUOTES, 'UTF-8'); ?>
            ;
            margin: 20px 0;
            font-size: 2.5rem;
        }




        h3 {
            text-align: center;
            margin: 20px;
            font-size: 15px;
            border: 2px solid black;
        }

        /* สร้างปุ่มย้อนกลับ */
        .back-button {
            position: absolute;
            left: 10px;
            /* ตำแหน่งซ้ายมือ */
            top: 20px;
            /* ระยะห่างจากขอบด้านบน */
            background-color: #4CAF50;
            /* สีพื้นหลัง */
            color: white;
            /* สีตัวอักษร */
            padding: 10px 20px;
            text-align: center;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #45a049;
            /* เปลี่ยนสีเมื่อวางเมาส์ */
        }



        .lesson-description {
            text-align: center;
            color:
                <?php echo htmlspecialchars($lesson['text_color'], ENT_QUOTES, 'UTF-8'); ?>
            ;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .cover-image {
            text-align: center;
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .cover-image img {
            max-width: 70%;
            height: auto;
            border-radius: 10px;
            display: block;
        }

        .section {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .image-content {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .image-content img {
            max-width: 91%;
            height: auto;
            display: block;
        }

        .video-content {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .video-content video {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .cover-image img {
                max-width: 90%;
            }

            .container {
                padding: 10px;
                margin: 10px;
            }

            .section {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            .cover-image img {
                max-width: 100%;
            }

            .container {
                padding: 5px;
                margin: 5px;
            }

            .section {
                padding: 10px;
            }
        }

        /* ปุ่มทำเเบบฝึกหัด */
        .test-button a {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            background-color: gray;
            border-radius: 10px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .test-button a:hover {
            background-color: darkgray;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="javascript:history.back()" class="back-button">ย้อนกลับ</a>

        <h1><?php echo htmlspecialchars($lesson['lessonName'], ENT_QUOTES, 'UTF-8'); ?></h1>

        <!-- Display Lesson Description -->

        <!-- Display Cover Image -->
        <?php if (!empty($lesson['cover_image'])): ?>
            <div class="cover-image">
                <img src="<?php echo htmlspecialchars($lesson['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Cover Image">
            </div>
        <?php endif; ?>

        <?php if (!empty($lesson['lessonDescription'])): ?>
            <p class="lesson-description">
                <?php echo htmlspecialchars_decode($lesson['lessonDescription']); ?>
            </p>
        <?php endif; ?>

        <?php if ($sections->num_rows > 0): ?>
            <?php while ($section = $sections->fetch_assoc()): ?>
                <div class="section"
                    style="background-color: <?php echo htmlspecialchars($section['section_color'], ENT_QUOTES, 'UTF-8'); ?>;">
                    <?php
                    // ดึงข้อมูลเนื้อหาประเภทข้อความ
                    $stmt = $conn->prepare("SELECT * FROM text_content WHERE sectionID = ?");
                    $stmt->bind_param("i", $section['sectionID']);
                    $stmt->execute();
                    $texts = $stmt->get_result();

                    while ($text = $texts->fetch_assoc()): ?>
                        <div class="text-content">
                            <?php echo htmlspecialchars_decode($text['content']); ?>
                        </div>
                    <?php endwhile; ?>

                    <?php
                    // ดึงข้อมูลเนื้อหาประเภทภาพ
                    $stmt = $conn->prepare("SELECT * FROM images WHERE sectionID = ?");
                    $stmt->bind_param("i", $section['sectionID']);
                    $stmt->execute();
                    $images = $stmt->get_result();

                    while ($image = $images->fetch_assoc()): ?>
                        <div class="image-content">
                            <img src="<?php echo htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Image">
                        </div>
                    <?php endwhile; ?>


                    <?php
                    // ดึงข้อมูลเนื้อหาประเภทวิดีโอ
                    $stmt = $conn->prepare("SELECT * FROM videos WHERE sectionID = ?");
                    $stmt->bind_param("i", $section['sectionID']);
                    $stmt->execute();
                    $videos = $stmt->get_result();

                    while ($video = $videos->fetch_assoc()): ?>
                        <div class="video-content">
                            <video controls src="<?php echo htmlspecialchars($video['video_url'], ENT_QUOTES, 'UTF-8'); ?>"></video>
                        </div>
                    <?php endwhile; ?>



                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No sections available for this lesson.</p>
        <?php endif; ?>

        <!-- Test Button Section -->
        <div class="test-button" style="text-align: center; margin-top: 20px;">
            <a href="testPage.php?lessonID=<?php echo $lessonID; ?>" class="btn-test"
                style="pointer-events: none; opacity: 0.5;">
                <h2>ทำแบบทดสอบ</h2>
            </a>
        </div>

    </div>

    <?php $conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล ?>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const videos = document.querySelectorAll("video");
        const testButton = document.querySelector(".btn-test");

        if (videos.length === 0) {
            // If no videos, enable the test button by default
            testButton.style.pointerEvents = "auto";
            testButton.style.opacity = "1";
            return;
        }

        let watchedVideos = 0;

        videos.forEach(video => {
            let lastTime = 0; // เก็บตำแหน่งล่าสุดที่ดู
            let isVideoEnded = false; // ตัวแปรตรวจสอบว่าวิดีโอจบหรือยัง
            let isSeeking = false; // ตรวจสอบว่าผู้ใช้กำลังเลื่อนอยู่หรือไม่

            // ป้องกันการเลื่อนข้ามวิดีโอ
            video.addEventListener("timeupdate", function () {
                if (!isVideoEnded && !isSeeking && video.currentTime > lastTime + 1) {
                    video.currentTime = lastTime; // ย้อนกลับไปที่ตำแหน่งเดิม
                } else {
                    lastTime = video.currentTime; // อัปเดตตำแหน่งปัจจุบันที่ดู
                }
            });

            // เมื่อผู้ใช้พยายามเลื่อนวิดีโอf
            video.addEventListener("seeking", function () {
                if (!isVideoEnded) {
                    isSeeking = true; // เริ่มสถานะเลื่อน
                    video.pause(); // หยุดวิดีโอระหว่างการเลื่อน
                }
            });

            // เมื่อหยุดการเลื่อน
            video.addEventListener("seeked", function () {
                isSeeking = false; // จบสถานะเลื่อน
                if (!isVideoEnded) {
                    video.play(); // เริ่มเล่นเมื่อหยุดเลื่อน
                }
            });

            // เมื่อวิดีโอจบ
            video.addEventListener("ended", function () {
                isVideoEnded = true; // ตั้งค่าว่าวิดีโอจบแล้ว
                watchedVideos++;

                // ถ้าดูวิดีโอทุกตัวจบแล้ว เปิดการใช้งานปุ่มทำแบบทดสอบ
                if (watchedVideos === videos.length) {
                    testButton.style.pointerEvents = "auto";
                    testButton.style.opacity = "1"; // แสดงปุ่มทำแบบทดสอบ
                    testButton.style.backgroundColor = "#4CAF50"; // เปลี่ยนสีปุ่มเป็นเขียว
                    testButton.style.color = "#FFFFFF"; // เปลี่ยนสีตัวอักษรเป็นขาว
                }
            });

            // ป้องกันการคลิกขวาบนวิดีโอ
            video.addEventListener("contextmenu", function (e) {
                e.preventDefault();
            });
        });
    });
</script>

</html>