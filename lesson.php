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
$stmt = $conn->prepare("SELECT * FROM lessons WHERE lessonID = ?");
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
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
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
            /* Use rem for responsive font size */
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

        /* Media queries for smaller screens */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
                /* Adjust font size for tablets */
            }

            .cover-image img {
                max-width: 90%;
                /* Adjust image size for tablets */
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
                /* Adjust font size for mobile devices */
            }

            .cover-image img {
                max-width: 100%;
                /* Adjust image size for mobile devices */
            }

            .container {
                padding: 5px;
                margin: 5px;
            }

            .section {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($lesson['lessonName'], ENT_QUOTES, 'UTF-8'); ?></h1>

        <!-- Display Cover Image -->
        <?php if (!empty($lesson['cover_image'])): ?>
            <div class="cover-image">
                <img src="<?php echo htmlspecialchars($lesson['cover_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Cover Image">
            </div>
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
                        <div class="text-content"
                            style="color: <?php echo htmlspecialchars($text['text_color'], ENT_QUOTES, 'UTF-8'); ?>;">
                            <!-- แสดงเนื้อหาที่มีการจัดรูปแบบ HTML -->
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

        <!-- ปุ่มลิงก์ไปยังหน้าแบบทดสอบ -->
        <div class="test-button" style="text-align: center; margin-top: 20px;">
            <a href="testPage.php?lessonID=<?php echo $lessonID; ?>" class="btn-test"><h2>ทำแบบทดสอบ</h2></a>
        </div>
    </div>

    <?php $conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล ?>
</body>

</html>