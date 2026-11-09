<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

// ดึงรายชื่อร้านค้ามาทำ Dropdown
$v_sql = "SELECT * FROM Vendors ORDER BY vendor_name ASC";
$v_res = $conn->query($v_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asset_name = $conn->real_escape_string($_POST['asset_name']);
    $purchase_date = $_POST['purchase_date'];
    $warranty_expiry_date = $_POST['warranty_expiry_date'];
    
    // ตรวจสอบว่าเลือกร้านค้าหรือไม่
    $vendor_id = !empty($_POST['vendor_id']) ? "'" . $conn->real_escape_string($_POST['vendor_id']) . "'" : "NULL";
    $user_id = $_SESSION['user_id']; 

    // เพิ่ม vendor_id เข้าไปในคำสั่ง SQL
    $sql = "INSERT INTO Assets (user_id, asset_name, purchase_date, warranty_expiry_date, vendor_id) 
            VALUES ('$user_id', '$asset_name', '$purchase_date', '$warranty_expiry_date', $vendor_id)";

    try {
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ!'); window.location.href='index.php';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        echo "<div class='alert alert-danger m-3'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มอุปกรณ์ใหม่ - Gadget Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 rounded-4 p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary"><i class="fa-solid fa-laptop-code"></i> เพิ่มอุปกรณ์ไอที</h3>
                        <p class="text-muted small">กรอกรายละเอียดฮาร์ดแวร์และวันหมดประกัน</p>
                    </div>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ชื่ออุปกรณ์</label>
                            <input type="text" name="asset_name" class="form-control" required placeholder="เช่น คีย์บอร์ด Keychron">
                        </div>

                        <!-- เพิ่มช่องเลือกร้านค้าตรงนี้ -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ร้านค้าที่ซื้อ</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">-- ไม่ระบุร้านค้า --</option>
                                <?php 
                                if ($v_res && $v_res->num_rows > 0) {
                                    while($v = $v_res->fetch_assoc()) {
                                        echo "<option value='".$v['vendor_id']."'>".htmlspecialchars($v['vendor_name'])."</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="form-text small">ถ้าร้านที่ต้องการไม่มีในตัวเลือก ให้ไปเพิ่มที่เมนูจัดการร้านค้าก่อน</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">วันที่ซื้อ</label>
                            <input type="date" name="purchase_date" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">วันหมดประกัน</label>
                            <input type="date" name="warranty_expiry_date" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm mb-2">
                            <i class="fa-solid fa-check me-1"></i> บันทึกข้อมูล
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary w-100 py-2 fw-semibold">ยกเลิก</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>