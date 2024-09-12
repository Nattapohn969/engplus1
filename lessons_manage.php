<?php
include 'connect.php';
// session_start(); // Ensure session is started

// // Assuming $userID is stored in the session
// if (!isset($_SESSION['userID'])) {
//     header('Location: login.php'); // Redirect to login if not logged in
//     exit();
// }
// $userID = $_SESSION['userID'];
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
</head>

<body class="sb-nav-fixed">
    <?php
    include 'navbar.php';
    ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="card mb-4 mt-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        แสดงข้อมูลบทเรียน
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple" class="table table-light">
                            <thead>
                                <tr>
                                    <th>รหัสบทเรียน</th>
                                    <th>ชื่อบทเรียน</th>
                                    <th>รายละเอียด</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM lessons WHERE user_ID = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $userID);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($row = $result->fetch_assoc()) {
                                    $templatePage = "lesson.php";
                                    $editPage = "lesson_edit.php";
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row["lessonID"]) ?></td>
                                        <td><?= htmlspecialchars($row["lessonName"]) ?></td>
                                        <td>
                                            <a href="<?= $editPage ?>?lessonID=<?= htmlspecialchars($row["lessonID"]) ?>">
                                                <img src="assets/img/edit.png" style="height: 30px; width: 30px; margin-right: 10px;"></img>
                                            </a>
                                            <a href="<?= $templatePage ?>?lessonID=<?= htmlspecialchars($row["lessonID"]) ?>">
                                                <img src="assets/img/search.png" style="height: 30px; width: 30px;"></img>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="lesson_delete.php?lessonID=<?= htmlspecialchars($row["lessonID"]) ?>"
                                               onclick="return confirmDelete(this.href);">
                                                <img src="assets/img/delete.png" style="height: 30px; width: 30px;"></img>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $stmt->close();
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
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

<script language="JavaScript">
    function confirmDelete(url) {
        if (confirm('คุณต้องการลบข้อมูลหรือไม่ ?')) {
            window.location.href = url;
            return true;
        }
        return false;
    }
</script>
