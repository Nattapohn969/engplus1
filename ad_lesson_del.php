<?php

include 'connect.php';

// ตรวจสอบว่ามีการระบุ ID หรือไม่
if (isset($_GET['lessonID'])) {
    $lessonID = intval($_GET['lessonID']); // แปลงให้เป็นตัวเลข integer เพื่อความปลอดภัย

    // เริ่มต้นการทำธุรกรรม
    $conn->begin_transaction();

    try {

        // ลบข้อมูลรูปภาพที่เกี่ยวข้อง
        $stmt = $conn->prepare("
            DELETE FROM images 
            WHERE sectionID IN (SELECT sectionID FROM sections WHERE lessonID = ?)
        ");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();

        // ลบข้อมูลวิดีโอที่เกี่ยวข้อง
        $stmt = $conn->prepare("
            DELETE FROM videos 
            WHERE sectionID IN (SELECT sectionID FROM sections WHERE lessonID = ?)
        ");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();

        // ลบข้อมูลเนื้อหาข้อความที่เกี่ยวข้อง
        $stmt = $conn->prepare("
            DELETE FROM text_content 
            WHERE sectionID IN (SELECT sectionID FROM sections WHERE lessonID = ?)
        ");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();

        // ลบข้อมูล test1 ที่เกี่ยวข้อง
        $stmt = $conn->prepare("DELETE FROM test1 WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();

        // ลบข้อมูล test2 ที่เกี่ยวข้อง
        $stmt = $conn->prepare("DELETE FROM test2 WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();

        // ลบข้อมูล section ที่เกี่ยวข้อง
        $stmt = $conn->prepare("DELETE FROM sections WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();
        
        // ลบข้อมูล  ratings
        $stmt = $conn->prepare("DELETE FROM ratings WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();


        // ลบข้อมูล favorite_lesson
        $stmt = $conn->prepare("DELETE FROM favorite_lesson WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();


        // ลบข้อมูลบทเรียน
        $stmt = $conn->prepare("DELETE FROM lessons WHERE lessonID = ?");
        if (!$stmt) {
            throw new Exception("ไม่สามารถเตรียม statement ได้: " . $conn->error);
        }
        $stmt->bind_param("i", $lessonID);
        if (!$stmt->execute()) {
            throw new Exception("การประมวลผล statement ล้มเหลว: " . $stmt->error);
        }
        $stmt->close();

        // ยืนยันการทำธุรกรรม
        $conn->commit();

        // เปลี่ยนหน้าไปยังหน้าการจัดการบทเรียน
        header("Location: dashbord_admin.php");
        exit();
    } catch (Exception $e) {
        // ยกเลิกการทำธุรกรรมหากมีข้อผิดพลาด
        $conn->rollback();
        echo "การลบบทเรียนล้มเหลว: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อ
    $conn->close();
} else {
    echo "เกิดข้อผิดพลาด: ไม่ได้ระบุรหัสบทเรียน (lessonID) กรุณากลับไปและลองใหม่อีกครั้ง.";
}
