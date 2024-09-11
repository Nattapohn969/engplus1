<?php
session_start();

require_once "connect.php";

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if the username already exists
    $user_check = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($user_check);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo "<script>alert('Username already exists');</script>";
    } else {
        // Encrypt the password before saving it
        $passwordenc = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $query = "INSERT INTO users (username, password, name, email, role, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $username, $passwordenc, $name, $email, $role);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'User registered successfully';
            header("Location: Teacher_dash.php");
            exit();
        } else {
            $_SESSION['error'] = "Something went wrong: " . $stmt->error;
            header("Location: ");
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/Register.css">
    <title>ENG PLUS</title>
</head>

<body>
<div class='navbar'>
    <img src='assets/img/LogoEngPlusNew.png' width='160px' height='auto'></img>
    <div class='innavbar'>
      <ul><a href='HomePage.html' class='blacktext'>Home</a></ul>
      <ul><a href='CoursesPage.php' class='blacktext'>Courses</a></ul>
      <ul><a href='MycoursesPage.html' class='blacktext'>My Courses</a></ul>
      <ul><a href='#' class='blacktext'>Transform</a></ul>
      <ul><a href='register.php' class='blacktext'>Register</a></ul>
      <ul><a href='login.php' class='createacc'>Login</a></ul>
    </div>
</div>

<div class="intro-register">
    <div class="register-01">
        <div class='register-logo'>
            <img src='assets/img/LogoEngPlusNew.png' width='250px' height='auto'></img>
        </div>

        <div class="register-create">
            <p>Create Account</p>
        </div>

        <div class="register-form">
            <!-- Ensure the button is inside the form -->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class='form-group'>
                <div class="form-group-1">
                    <label for="name">Name</label>
                    <input class='field-1' type='text' id="name" name="name" required></input>
                </div>

                <div class="form-group-2">
                    <label for="email">Email</label>
                    <input class='field-1' type='email' id="email" name="email" required></input>
                </div>

                <div class="form-group-1">
                    <label for="username">Username</label>
                    <input class='field-2' type='text' id="username" name="username" required></input>
                </div>

                <div class="form-group-2">
                    <label for="password">Password</label>
                    <input class='field-2' type='password' id="password" name="password" required></input>
                </div>

                <div class="select-bar">
                    <p>Select Your Role</p>
                    <select class="ddlist" id="role" name="role">
                        <option value="Learner">Learner</option>
                        <option value="Teacher">Teacher</option>
                    </select>
                </div>

                <div class="create-yes">
                    <div class='yes-register'>
                        <button type="submit" name="submit">ลงทะเบียน</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="register-icon">
        <img src='assets/img/Signin-icon.png' width='550px' height='auto'></img>
    </div>

</div>
</body>

</html>
