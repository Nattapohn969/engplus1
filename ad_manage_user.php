<?php
session_start();
include 'connect.php';

// Check if the user is logged in and has the Admin role
if (!isset($_SESSION['user_ID']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch user information from the session
$username = htmlspecialchars($_SESSION['username']);

// Fetch new reports
$sql = "SELECT * FROM user_reports WHERE status='new'";
$result = $conn->query($sql);
$new_reports_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <!-- <link rel="stylesheet" href="css/stylead.css" /> -->
</head>
<style>
    body {
        background-image: url("assets/img/Untitled design.png");
        font-family: "Mali", cursive;
        font-weight: 500;
        font-style: normal;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fbde70;
        padding: 10px 20px;
        color: #fff;
        flex-wrap: wrap;
        /* รองรับการแสดงผลที่ดีขึ้นในอุปกรณ์ขนาดเล็ก */
    }

    .navbar .logo {
        font-size: 24px;
        font-weight: bold;
        color: #fff;
        display: flex;
        align-items: center;
    }

    .navbar .logo img {
        height: 40px;
        margin-right: 10px;
        vertical-align: middle;
    }

    .navbar .menu {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        /* รองรับการแสดงผลที่ดีขึ้นในอุปกรณ์ขนาดเล็ก */
    }

    .navbar .menu a {
        color: #121f39;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 5px;
        transition: background-color 0.3s;
        font-family: "Mali", cursive;
        font-weight: 500;
        font-style: normal;
    }

    .navbar .menu a:hover {
        background-color: #ddd;
        color: #333;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background-color: #3f9965;
        color: #fff;
        border: none;
        padding: 10px 15px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s;
        font-family: "Mali", cursive;
        font-weight: 500;
        font-style: normal;
    }

    .dropbtn:hover {
        background-color: #ddd;
        color: #333;
    }

    .dropbtn1:hover {
        background-color: #ddd;
        color: #333;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #333;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border-radius: 5px;
    }


    .dropdown-content a {
        color: #fff;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        border-bottom: 1px solid #ddd;
    }


    .dropdown-content a:hover {
        background-color: #ddd;
        color: #333;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .profile-pic {
        border-radius: 50%;
        width: 30px;
        /* Adjust the size */
        height: 30px;
        /* Adjust the size */
        object-fit: cover;
        margin-right: 10px;
        /* Space between image and text */
        vertical-align: middle;
    }

    .container {
        padding: 20px;
    }

    .card {
        background-color: #ffffffcc;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin: 10px 0;
    }

    .protected {
        display: none;
        /* ซ่อนเมนูที่มีคลาส .protected โดยเริ่มต้น */
    }

    /* Media Queries */
    @media (max-width: 768px) {
        .navbar .menu {
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
        }

        .navbar .menu a {
            display: block;
            width: 100%;
            text-align: center;
            padding: 10px;
        }

        .dropdown-content {
            position: static;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .navbar .logo {
            font-size: 20px;
        }

        .navbar .logo img {
            height: 30px;
        }

        .dropbtn {
            font-size: 14px;
            padding: 8px 12px;
        }

        .profile-pic {
            width: 25px;
            height: 25px;
        }
    }

    .notification-badge {
        background-color: red;
        color: white;
        padding: 5px 10px;
        border-radius: 50%;
        font-size: 12px;
        position: relative;
        top: -5px;
        left: -10px;
    }
</style>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="assets/img/Logonew.png" alt="Logo" />
        </div>
        <div class="menu" id="menu">
            <a href="dashbord_admin.php" class="dropdown">Manage Lesson</a>
            <a href="ad_manage_user.php" class="dropdown">Manage Users</a>
            <a href="get_report.php" class="dropdown">
                Get Report
                <?php if ($new_reports_count > 0): ?>
                    <span class="notification-badge"><?php echo $new_reports_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <div id="accountMenu" class="dropdown">
            <button class="dropbtn" id="accountButton">
                <?php
                echo htmlspecialchars($_SESSION['username']);
                if (isset($_SESSION['role'])) {
                    echo " (" . htmlspecialchars($_SESSION['role']) . ")";
                }
                ?>
            </button>
            <div id="dropdownContent" class="dropdown-content">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <!-- ส่วนของการค้นหา (Search Form) -->
                <div class="row mt-4">
                    <form method="GET" action="" id="searchForm">
                        <div class="input-group">
                            <!-- ช่องสำหรับกรอกคำค้นหา -->
                            <input type="text" class="form-control" name="search" id="searchInput"
                                placeholder="ค้นหาผู้ใช้"
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        </div>
                    </form>
                </div>

                <div class="card mb-4 mt-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i> แสดงข้อมูลผู้ใช้
                    </div>
                    <div class="card-body">
                        <!-- เริ่มต้นการสร้างตาราง -->
                        <table id="datatablesSimple" class="table table-light">
                            <thead>
                                <tr>
                                    <!-- หัวตาราง แสดงลำดับ ชื่อบทเรียน รูปปก ฯลฯ -->
                                    <th>NO</th> <!-- เปลี่ยนจาก รหัสบทเรียน เป็น ลำดับ -->
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
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


                                // Correct SQL query
                                $sql = "SELECT * FROM users WHERE (role = 'Learner' OR role = 'Teacher') AND Name LIKE ? LIMIT ?, ?";
                                $stmt = $conn->prepare($sql);

                                // Check if the statement was prepared successfully
                                if ($stmt === false) {
                                    die("Error preparing statement: " . $conn->error);
                                }

                                // Bind parameters for search keyword, start position, and limit
                                $stmt->bind_param("sii", $search, $start, $limit);
                                $stmt->execute();
                                $result = $stmt->get_result();


                                // กำหนดตัวนับลำดับ (counter) เริ่มต้นตามลำดับของหน้าที่เลือก
                                $counter = $start + 1;

                                // วนลูปแสดงผลข้อมูลแต่ละแถวในตาราง
                                while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <!-- แสดงลำดับโดยเพิ่มค่าจากตัวแปร $counter -->
                                        <td><?= $counter++ ?></td> <!-- เพิ่มลำดับ row number -->
                                        <!-- แสดงชื่อ-->
                                        <td><?= htmlspecialchars($row["name"]) ?></td>
                                        <td><?= htmlspecialchars($row["email"]) ?></td>
                                        <td><?= htmlspecialchars($row["username"]) ?></td>
                                        <td><?= htmlspecialchars($row["role"]) ?></td>
                                        <!-- ลิงก์สำหรับลบบทเรียน -->
                                        <td>
                                            <a href="delete1.php?user_ID=<?= htmlspecialchars($row["user_ID"]) ?>"
                                                onclick="return confirmDelete(this.href);">
                                                <img src="assets/img/delete.png" style="height: 30px; width: 30px;"
                                                    alt="Delete"></img>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                $stmt->close();

                                $countSql = "SELECT COUNT(*) as total FROM users WHERE (role = 'Learner' OR role = 'Teacher') AND Name LIKE ?";
                                $countStmt = $conn->prepare($countSql);

                                $countStmt->bind_param("s", $search);
                                $countStmt->execute();
                                $countResult = $countStmt->get_result();

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