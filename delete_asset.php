<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

// ตรวจสอบว่ามีการส่ง id ของอุปกรณ์ที่ต้องการลบมาหรือไม่
if (isset($_GET['id'])) {
    $asset_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // ป้องกันการลบข้อมูลของคนอื่น

    // ลบข้อมูลที่ตรงกับ asset_id และต้องเป็นของ user_id ที่ล็อกอินอยู่เท่านั้น
    $sql = "DELETE FROM Assets WHERE asset_id = '$asset_id' AND user_id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล: " . $conn->error . "'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
}
?>