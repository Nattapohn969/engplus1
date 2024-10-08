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

// Fetch new reports with username
$sql = "SELECT ur.*, u.username FROM user_reports ur
        JOIN users u ON ur.user_ID = u.user_ID
        WHERE ur.status='new'";
$result = $conn->query($sql);
$new_reports_count = $result->num_rows;

$sql_read = "SELECT ur.*, u.username FROM user_reports ur
             JOIN users u ON ur.user_ID = u.user_ID
             WHERE ur.status='read' AND ur.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$result_read = $conn->query($sql_read);
$read_reports_count = $result_read->num_rows;
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
        background-color: #fff;
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




    .container1 {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .card1 {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        margin: 15px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card1:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .card1 h2 {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .card1 p {
        font-size: 16px;
        color: #555;
        margin: 8px 0;
    }

    .btn-read {
        display: inline-block;
        padding: 10px 15px;
        background-color: #3f9965;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        font-family: "Mali", cursive;
        font-weight: 500;
    }

    .btn-read:hover {
        background-color: #2d774e;
    }

    hr {
        border: none;
        border-top: 1px solid #e0e0e0;
        margin: 20px 0;
    }

    /* Adjust for small screens */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .card1 {
            padding: 15px;
        }

        .card1 h2 {
            font-size: 18px;
        }

        .card1 p {
            font-size: 14px;
        }

        .btn-read {
            padding: 8px 12px;
        }
    }


    h1,
    .h1 {
        font-size: calc(1.375rem + 1.5vw);
        color: #fff;
    }

    p {
        margin-top: 0;
        margin-bottom: 1rem;
        color: #fff;
        text-align: center;
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





    h4,
    .h4 {
        color: #fff;
        margin-top: 200px;
        text-align: center;
    }

    /* การ์ดสำหรับรายงานที่อ่านแล้ว */
    .card1 {
        background-color: #d6d5d5;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 10px;
        margin: 15px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* เงาเบา */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        /* เอฟเฟกต์เมื่อมีการ hover */
    }

    .card1:hover {
        transform: translateY(-5px);
        /* การ์ดยกขึ้นเล็กน้อยเมื่อ hover */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        /* เงาเพิ่มขึ้นเมื่อ hover */
    }

    .card1 h2 {
        font-size: 20px;
        /* ขนาดหัวข้อใหญ่ */
        font-weight: bold;
        /* เน้นตัวหนา */
        margin-bottom: 10px;
        /* เว้นบรรทัดด้านล่าง */
        color: #333;
        /* สีข้อความ */
    }

    .card1 p {
        font-size: 13px;
        /* ขนาดข้อความปกติ */
        color: #555;
        /* สีข้อความเทาเข้ม */
        margin: 8px 0;
        /* ระยะห่างระหว่างพารากราฟ */
    }

    hr {
        border: none;
        /* ลบขอบเส้น */
        border-top: 1px solid #e0e0e0;
        /* เส้นแบ่งบาง */
        margin: 20px 0;
        /* ระยะห่างระหว่างการ์ด */
    }

    /* Responsive สำหรับหน้าจอขนาดเล็ก */
    @media (max-width: 768px) {
        .card1 {
            padding: 15px;
            /* ลดขนาด padding ในการ์ด */
        }

        .card1 h2 {
            font-size: 18px;
            /* ลดขนาดหัวข้อ */
        }

        .card1 p {
            font-size: 14px;
            /* ลดขนาดข้อความ */
        }
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


    <div class="container1">
        <h1>รายงานใหม่</h1>
        <?php if ($new_reports_count > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card1">
                    <h2>รายงาน ID: <?php echo $row['report_id']; ?></h2>
                    <p>ขื่อผู้ใช้ : <?php echo $row['username']; ?></p>
                    <p>รายละเอียด: <?php echo htmlspecialchars($row['report_text']); ?></p>
                    <p>วันที่ส่ง: <?php echo $row['created_at']; ?></p>
                    <a href="read_report.php?report_id=<?php echo $row['report_id']; ?>" class="btn-read">อ่านแล้ว</a>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>ไม่มีรายงานใหม่</p>
        <?php endif; ?>


        <h4>รายงานที่อ่านแล้ว (7 วันย้อนหลัง)</h4>
        <?php if ($read_reports_count > 0): ?>
            <?php while ($row = $result_read->fetch_assoc()): ?>
                <div class="card1">
                    <h2>รายงาน ID: <?php echo $row['report_id']; ?></h2>
                    <p>ชื่อผู้ใช้ : <?php echo $row['username']; ?></p>
                    <p>รายละเอียด: <?php echo htmlspecialchars($row['report_text']); ?></p>
                    <p>วันที่อ่าน: <?php echo $row['created_at']; ?></p>
                </div>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>ไม่มีรายงานที่อ่านแล้วใน 7 วันย้อนหลัง</p>
        <?php endif; ?>

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