<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_ID = $_POST['user_ID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind parameters
    $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE user_ID = ?";
    $stmt = $conn->prepare($sql);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sssi', $username, $email, $hashed_password, $user_ID);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username; // Update session with new username
        header('Location: ProfilePage.php?update=success');
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
