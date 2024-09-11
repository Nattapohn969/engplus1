<?php
include 'connect1.php';
$id=$_GET['user_ID'];
$sql="DELETE FROM users WHERE user_ID='$id'";
$result= mysqli_query($conn,$sql);
if($result){
    echo "<script>alert('ลบข้อมูลเรียบร้อย');</script>";
    echo "<script>window.location='user.php';</script>";
}else{
    echo "<script>alert('ไม่สามารถลบข้อมูลได้');</script>";
}

mysqli_close($conn);

?>