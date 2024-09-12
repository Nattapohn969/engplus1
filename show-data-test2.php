<?php
include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Test2</title>
    <style>
        /* Add your styles here (same as in your initial example) */
    </style>
</head>

<body>
    <h1>Manage Test2</h1>
    <a href="add_test2.php" class="button">Add New Test2</a> <!-- Link to Add New Entry -->
    <table>
        <thead>
            <tr>
                <th>test2_ID</th>
                <th>testType_ID</th>
                <th>lessonID</th>
                <th>word_1</th>
                <th>word_2</th>
                <th>word_3</th>
                <th>word_4</th>
                <th>word_5</th>
                <th>word_6</th>
                <th>word_7</th>
                <th>word_8</th>
                <th>word_9</th>
                <th>word_10</th>
                <th>score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM test2";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }

            while ($row = mysqli_fetch_array($result)) {
                $id = htmlspecialchars($row["test2_ID"]);
            ?>
                <tr>
                    <td><?= $id ?></td>
                    <td><?= htmlspecialchars($row["testType_ID"]) ?></td>
                    <td><?= htmlspecialchars($row["lessonID"]) ?></td>
                    <td><?= htmlspecialchars($row["word_1"]) ?></td>
                    <td><?= htmlspecialchars($row["word_2"]) ?></td>
                    <td><?= htmlspecialchars($row["word_3"]) ?></td>
                    <td><?= htmlspecialchars($row["word_4"]) ?></td>
                    <td><?= htmlspecialchars($row["word_5"]) ?></td>
                    <td><?= htmlspecialchars($row["word_6"]) ?></td>
                    <td><?= htmlspecialchars($row["word_7"]) ?></td>
                    <td><?= htmlspecialchars($row["word_8"]) ?></td>
                    <td><?= htmlspecialchars($row["word_9"]) ?></td>
                    <td><?= htmlspecialchars($row["word_10"]) ?></td>
                    <td><?= htmlspecialchars($row["score"]) ?></td>
                    <td>
                        <a href="Test2.php?id=<?= $id ?>" class="button">view</a>
                        <a href="delete_test2.php?id=<?= $id ?>" class="button" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php
            }
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</body>

</html>
