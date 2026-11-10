<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; 

    // 1. ตรวจสอบก่อนว่าอุปกรณ์นี้เป็นของ User คนที่ล็อกอินอยู่จริงๆ
    $check_sql = "SELECT asset_id FROM Assets WHERE asset_id = '$asset_id' AND user_id = '$user_id'";
    $check_res = $conn->query($check_sql);
    
    if ($check_res->num_rows > 0) {
        
        // 2. ลบประวัติการซ่อมบำรุงที่ผูกกับอุปกรณ์นี้ออกก่อน (แก้ปัญหา Foreign Key Constraint)
        $delete_logs_sql = "DELETE FROM Maintenance_Logs WHERE asset_id = '$asset_id'";
        $conn->query($delete_logs_sql);
        
        // 3. ลบอุปกรณ์ออกจากระบบ
        $delete_asset_sql = "DELETE FROM Assets WHERE asset_id = '$asset_id'";
        
        if ($conn->query($delete_asset_sql) === TRUE) {
            echo "<script>alert('ลบข้อมูลอุปกรณ์และประวัติที่เกี่ยวข้องสำเร็จ'); window.location.href='index.php';</script>";
        } else {
            // ถ้าลบไม่สำเร็จ ให้แสดง Error ออกมาให้เห็นชัดเจน จะได้รู้ว่าเกิดจากอะไร
            echo "<script>alert('เกิดข้อผิดพลาดในการลบอุปกรณ์: " . $conn->error . "'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('คุณไม่มีสิทธิ์ลบอุปกรณ์นี้ หรือไม่พบข้อมูล'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>