<?php
include 'connect.php';

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
                        เเสดงข้อมูลผู้ใช้
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple" class="table-Light">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                          
                                    <th>Role</th>
                                    <th>Date</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <?php
                            $counter = 1; // Initialize a counter variable
                            $sql = "SELECT * FROM users";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_array($result)) {
                                ?>
                                <tr>
                                    <td><?= $counter ?></td>
                                    <td><?= $row["name"] ?></td>
                                    <td><?= $row["email"] ?></td>
                                    <td><?= $row["username"] ?></td>
                                
                                    <td><?= $row["role"] ?></td>
                                    <td><?= $row["created_at"] ?></td>
                                    <td>
                                        <a href="edit1.php?user_ID=<?= $row["user_ID"] ?>"><i class="fas fa-edit "
                                                style="color: blue; font-size: 20px; display: block; margin: 0 auto;"></i></a>
                                    </td>
                                    <td>
                                        <a a href="delete1.php?user_ID=<?= $row["user_ID"] ?>"
                                            onclick="Del(this.href);return false;"><i class="fas fa-trash"
                                                style="color: red; font-size: 20px; display: block; margin: 0 auto;"></i></a>
                                    </td>
                                </tr>

                                <?php
                                $counter++; // Increment the counter after each row
                            }
                            mysqli_close($conn); //ปิดการเชื่อมต่อฐานข้อมูล
                            ?>
                        </table>

                    </div>
                </div>
            </div>
        </main>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>

<script language="JavaScript">
    function Del(mypage) {
        var agree = confirm('คุณต้องการลบข้อมูลหรือไม่ ?');
        if (agree) {
            window.location = mypage;

        }
    }

</script>