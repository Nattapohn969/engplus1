<?php
include 'connect.php';

// Assuming $conn is your mysqli connection
$sql = "INSERT INTO test1 (question, choice_A, choice_B, choice_C, choice_D, answer, score, lessonID) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // Bind parameters
    $stmt->bind_param('ssssssii', $question, $choiceA, $choiceB, $choiceC, $choiceD, $answer, $score, $lessonID);

    $questions = $_POST['question'] ?? [];
    $choicesA = $_POST['choice_a'] ?? [];
    $choicesB = $_POST['choice_b'] ?? [];
    $choicesC = $_POST['choice_c'] ?? [];
    $choicesD = $_POST['choice_d'] ?? [];
    $answers = $_POST['answer'] ?? [];
    $scores = $_POST['score'] ?? [];

    $lessonID = $_POST['lessonID'] ?? 0; // Default value

    foreach ($questions as $index => $question) {
        $choiceA = $choicesA[$index] ?? '';
        $choiceB = $choicesB[$index] ?? '';
        $choiceC = $choicesC[$index] ?? '';
        $choiceD = $choicesD[$index] ?? '';
        $answer = $answers[$index] ?? '';
        $score = $scores[$index] ?? '1';

        if (!$stmt->execute()) {
            echo '<script>alert("Error executing statement: ' . $stmt->error . '"); history.back();</script>';
            exit();
        }
    }

    $stmt->close();
} else {
    echo '<script>alert("Failed to prepare SQL statement: ' . $conn->error . '"); history.back();</script>';
    exit();
}

$conn->close();

header("Location: lessons_manage.php");
exit();
?>
