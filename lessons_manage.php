<?php
include 'connect.php';
// Ensure session is started
// session_start();

// Check if user is logged in
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
    <!-- ส่วนของ Navbar ที่นำเข้าจากไฟล์ navbar.php -->
    <?php include 'navbar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <!-- ส่วนของการค้นหา (Search Form) -->
                <div class="row mt-4">
                    <form method="GET" action="" id="searchForm">
                        <div class="input-group">
                            <!-- ช่องสำหรับกรอกคำค้นหา -->
                            <input type="text" class="form-control" name="search" id="searchInput"
                                placeholder="ค้นหาบทเรียน"
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </div>
                    </form>
                </div>

                <div class="card mb-4 mt-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i> แสดงข้อมูลบทเรียน
                    </div>
                    <div class="card-body">
                        <!-- เริ่มต้นการสร้างตาราง -->
                        <table id="datatablesSimple" class="table table-light">
                            <thead>
                                <tr>
                                    <!-- หัวตาราง แสดงลำดับ ชื่อบทเรียน รูปปก ฯลฯ -->
                                    <th>ลำดับ</th> <!-- เปลี่ยนจาก รหัสบทเรียน เป็น ลำดับ -->
                                    <th>ชื่อบทเรียน</th>
                                    <th>รูปปก</th>
                                    <th>รายละเอียด</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'connect.php';

                                // กำหนดค่าจำนวนบทเรียนที่จะแสดงต่อหน้า (Pagination)
                                $limit = 10; // จำนวนบทเรียนต่อหน้า
                                // ตรวจสอบว่ามีการกำหนดหมายเลขหน้า (page) หรือไม่ ถ้าไม่มีกำหนดให้ใช้หน้า 1
                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                                // คำนวณจุดเริ่มต้นของบทเรียนในแต่ละหน้า
                                $start = ($page - 1) * $limit;

                                // คำค้นหาจากฟอร์ม
                                $search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";

                                // SQL สำหรับดึงข้อมูลบทเรียน โดยแสดงเฉพาะบทเรียนของผู้ใช้ที่เข้าสู่ระบบ และค้นหาจากชื่อบทเรียน
                                $sql = "SELECT * FROM lessons WHERE user_ID = ? AND lessonName LIKE ? LIMIT ?, ?";
                                $stmt = $conn->prepare($sql);
                                // กำหนดค่าให้กับ SQL (userID, search keyword, start position, limit)
                                $stmt->bind_param("isii", $userID, $search, $start, $limit);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                // กำหนดตัวนับลำดับ (counter) เริ่มต้นตามลำดับของหน้าที่เลือก
                                $counter = $start + 1;

                                // วนลูปแสดงผลข้อมูลแต่ละแถวในตาราง
                                while ($row = $result->fetch_assoc()) {
                                    $templatePage = "lesson.php";
                                    $editPage = "lesson_edit.php";
                                    ?>
                                    <tr>
                                        <!-- แสดงลำดับโดยเพิ่มค่าจากตัวแปร $counter -->
                                        <td><?= $counter++ ?></td> <!-- เพิ่มลำดับ row number -->
                                        <!-- แสดงชื่อบทเรียน -->
                                        <td><?= htmlspecialchars($row["lessonName"]) ?></td>
                                        <!-- แสดงรูปปกของบทเรียน -->
                                        <td>
                                            <img src="<?= htmlspecialchars($row['cover_image']) ?>" alt="Cover Image"
                                                style="height: 100px; width: auto;">
                                        </td>
                                        <!-- ลิงก์สำหรับแก้ไขและดูรายละเอียดบทเรียน -->
                                        <td>
                                            <a href="<?= $editPage ?>?lessonID=<?= htmlspecialchars($row["lessonID"]) ?>">
                                                <img src="assets/img/edit.png"
                                                    style="height: 30px; width: 30px; margin-right: 10px;" alt="Edit"></img>
                                            </a>
                                            <a
                                                href="<?= $templatePage ?>?lessonID=<?= htmlspecialchars($row["lessonID"]) ?>">
                                                <img src="assets/img/search.png" style="height: 30px; width: 30px;"
                                                    alt="Search"></img>
                                            </a>
                                        </td>
                                        <!-- ลิงก์สำหรับลบบทเรียน -->
                                        <td>
                                            <a href="lesson_delete.php?lessonID=<?= htmlspecialchars($row["lessonID"]) ?>"
                                                onclick="return confirmDelete(this.href);">
                                                <img src="assets/img/delete.png" style="height: 30px; width: 30px;"
                                                    alt="Delete"></img>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                $stmt->close();

                                // SQL สำหรับนับจำนวนบทเรียนทั้งหมด เพื่อใช้ในการทำ Pagination
                                $countSql = "SELECT COUNT(*) as total FROM lessons WHERE user_ID = ? AND lessonName LIKE ?";
                                $countStmt = $conn->prepare($countSql);
                                // กำหนดค่าให้กับ SQL (userID, search keyword)
                                $countStmt->bind_param("is", $userID, $search);
                                $countStmt->execute();
                                $countResult = $countStmt->get_result();
                                // ดึงค่าจำนวนบทเรียนทั้งหมด
                                $total = $countResult->fetch_assoc()['total'];
                                $countStmt->close();

                                // ปิดการเชื่อมต่อฐานข้อมูล
                                $conn->close();
                                ?>
                            </tbody>
                        </table>

                        <!-- ส่วนของ Pagination -->
                        <nav aria-label="Pagination">
                            <ul class="pagination">
                                <?php
                                // คำนวณจำนวนหน้าทั้งหมดจากจำนวนบทเรียนทั้งหมด
                                $totalPages = ceil($total / $limit);
                                // วนลูปสร้างลิงก์ของแต่ละหน้า
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $active = $i == $page ? 'active' : '';
                                    echo "<li class='page-item $active'><a class='page-link' href='?page=$i&search=" . urlencode($_GET['search'] ?? '') . "'>$i</a></li>";
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </main>
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
    <script language="JavaScript">
        function confirmDelete(url) {
            return confirm('คุณต้องการลบข้อมูลหรือไม่ ?') ? (window.location.href = url, true) : false;
        }
    </script>
</body>

</html>
