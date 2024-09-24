<style>
    body {
    font-family: "Itim", cursive;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #90cedf;
    padding: 10px 20px;
    color: #fff;
    flex-wrap: wrap; /* รองรับการแสดงผลที่ดีขึ้นในอุปกรณ์ขนาดเล็ก */
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
    flex-wrap: wrap; /* รองรับการแสดงผลที่ดีขึ้นในอุปกรณ์ขนาดเล็ก */
}

.navbar .menu a {
    color: #ffffff;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.3s;
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
    font-family: "Itim", cursive;
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
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
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
    width: 30px; /* Adjust the size */
    height: 30px; /* Adjust the size */
    object-fit: cover;
    margin-right: 10px; /* Space between image and text */
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
    display: none; /* ซ่อนเมนูที่มีคลาส .protected โดยเริ่มต้น */
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

</style>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="assets/img/Logonew.png" alt="Logo" />
        </div>
        <div class="menu" id="menu">
            <a href="index.html">Dashboard</a>
            <!-- <a href="user.php">Manage Account</a> -->
            <div class="dropdown">
                <button class="dropbtn">Manage Lesson</button>
                <div class="dropdown-content">
                    <a href="lesson_add.php">Create Lesson</a>
                    <a href="lessons_manage.php">Manage Lesson</a><!-- เพิ่มลิงค์เพิ่มเติมตามที่ต้องการ -->
                </div>

            </div>
            <div id="accountMenu" class="dropdown">
                <button class="dropbtn" id="accountButton">
                    <?php
                    echo htmlspecialchars($_SESSION['username']);
                    // แสดงบทบาทของผู้ใช้
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
    </div>

</body>