<?php
include 'connect1.php';

// Check if form data is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize it
    $user_id = isset($_POST['user_ID']) ? intval($_POST['user_ID']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    // Validate form data (add more validation as needed)
    if (empty($name) || empty($email) || empty($username)) {
        die('Please fill in all required fields.');
    }

    // Prepare an SQL statement for updating the user information
    $query = $conn->prepare('UPDATE users SET name = ?, email = ?, username = ? WHERE user_ID = ?');
    $query->bind_param('sssi', $name, $email, $username, $user_id);

    // Execute the query and check for success
    if ($query->execute()) {
        // Redirect to a success page or display a success message
        header('Location: user.php?message=update_success');
        exit;
    } else {
        // Handle errors
        die('Update failed: ' . $conn->error);
    }
} else {
    // Handle invalid access
    die('Invalid request method.');
}
