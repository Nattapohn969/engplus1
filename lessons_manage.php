<?php
include 'connect.php';

// ดึงข้อมูลบทเรียนทั้งหมด
$query = "SELECT * FROM lessons";
$result = $conn->query($query);

if ($result === false) {
    die('Error: ' . $conn->error);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/style2.css" rel="stylesheet" />
    <title>Manage Lessons</title>
</head>

<body>
    <h1>Manage Lessons</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Lesson Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($lesson = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo ($lesson['lessonID']); ?></td>
                    <td><?php echo ($lesson['lessonName']); ?></td>
                    <td>
                        <a href="lesson.php?lessonID=<?php echo ($lesson['lessonID']); ?>">lesson</a>
                        <a href="test1_display.php?lessonID=<?php echo ($lesson['lessonID']); ?>">test</a> |
                        <a href="lesson_edit.php?lessonID=<?php echo ($lesson['lessonID']); ?>">Edit lesson</a>
                        <a
                            href="test1_edit.php?lessonID=<?php echo ($lesson['lessonID']); ?>&lessonName=<?php echo urlencode($lesson['lessonName']); ?>">Edit
                            test</a>
                        |
                        <a href="lesson_delete.php?lessonID=<?php echo ($lesson['lessonID']); ?>"
                            onclick="return confirm('Are you sure you want to delete this lesson?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="add_lesson.php">Add New Lesson</a>
</body>

</html>