<?php
include 'connect.php';

// Fetch the user data. Ensure you have user ID and proper database connection
$user_id = isset($_GET['user_ID']) ? intval($_GET['user_ID']) : 0;
$query = $conn->prepare('SELECT * FROM users WHERE user_ID = ?');
$query->bind_param('i', $user_id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    // Handle user not found
    die('User not found.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Dashboard - SB Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link href="css/stylead.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        .centered-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container centered-container">
                <div class="form-container">
                    <div class="text-center alert alert-success mb-4 mt-4" role="alert">
                        <h4>Edit Information</h4>
                    </div>

                    <form method="POST" action="update1.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['user_ID']) ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <textarea id="name" name="name" class="form-control" rows="3"><?= htmlspecialchars($row['name']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <textarea id="email" name="email" class="form-control" rows="3"><?= htmlspecialchars($row['email']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <textarea id="username" name="username" class="form-control" rows="3"><?= htmlspecialchars($row['username']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input id="role" name="role" class="form-control" readonly value="<?= htmlspecialchars($row['role']) ?>">
                        </div>

                        <div class="d-flex justify-content-between">
                            <input type="submit" value="Update" class="btn btn-success">
                            <a href="user.php" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>
