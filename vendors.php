<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

$edit_mode = false;
$edit_vendor_id = '';
$edit_vendor_name = '';
$edit_location = '';

// 1. จัดการเพิ่มร้านค้าใหม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $vendor_name = $conn->real_escape_string(trim($_POST['vendor_name']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    
    $sql = "INSERT INTO Vendors (vendor_name, location) VALUES ('$vendor_name', '$location')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('เพิ่มร้านค้าสำเร็จ!'); window.location.href='vendors.php';</script>";
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// 2. จัดการอัปเดตข้อมูลร้านค้า (กรณี Edti)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update') {
    $vendor_id = $_POST['vendor_id'];
    $vendor_name = $conn->real_escape_string(trim($_POST['vendor_name']));
    $location = $conn->real_escape_string(trim($_POST['location']));

    $sql = "UPDATE Vendors SET vendor_name = '$vendor_name', location = '$location' WHERE vendor_id = '$vendor_id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('แก้ไขข้อมูลร้านค้าสำเร็จ!'); window.location.href='vendors.php';</script>";
    } else {
        $error = "เกิดข้อผิดพลาดในการแก้ไข: " . $conn->error;
    }
}

// 3. จัดการลบร้านค้า
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // ตรวจสอบก่อนว่ามีการใช้งานร้านค้านี้อยู่ไหม (ป้องกัน Error Foreign Key)
    $check_use = $conn->query("SELECT * FROM Assets WHERE vendor_id = '$delete_id'");
    if ($check_use->num_rows > 0) {
        echo "<script>alert('ไม่สามารถลบได้ เนื่องจากมีอุปกรณ์ในระบบผูกอยู่กับร้านค้านี้!'); window.location.href='vendors.php';</script>";
    } else {
        $conn->query("DELETE FROM Vendors WHERE vendor_id = '$delete_id'");
        echo "<script>alert('ลบร้านค้าสำเร็จ'); window.location.href='vendors.php';</script>";
    }
}

// 4. ถ้ามีการกดปุ่มแก้ไข ให้ดึงข้อมูลเก่ามาใส่ฟอร์ม
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit_id'];
    $res = $conn->query("SELECT * FROM Vendors WHERE vendor_id = '$edit_id'");
    if ($res->num_rows == 1) {
        $row_edit = $res->fetch_assoc();
        $edit_vendor_id = $row_edit['vendor_id'];
        $edit_vendor_name = $row_edit['vendor_name'];
        $edit_location = $row_edit['location'];
    }
}

// ดึงข้อมูลร้านค้าทั้งหมดมาแสดงในตาราง
$v_sql = "SELECT * FROM Vendors ORDER BY vendor_id DESC";
$v_result = $conn->query($v_sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการร้านค้า - Gadget Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-shield-halved text-info"></i> Gadget & Warranty Vault</a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-outline-light btn-sm px-3"><i class="fa-solid fa-arrow-left"></i> กลับหน้าแรก</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <!-- ฟอร์มเพิ่ม หรือ แก้ไขร้านค้า -->
            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 rounded-4 p-4">
                    <h5 class="fw-bold <?php echo $edit_mode ? 'text-success' : 'text-warning'; ?> mb-3">
                        <i class="fa-solid <?php echo $edit_mode ? 'fa-pen-to-square' : 'fa-store'; ?>"></i> 
                        <?php echo $edit_mode ? 'แก้ไขข้อมูลร้านค้า' : 'เพิ่มร้านค้าใหม่'; ?>
                    </h5>
                    
                    <?php if(isset($error)) { echo "<div class='alert alert-danger small'>$error</div>"; } ?>
                    
                    <form method="post">
                        <!-- สลับ action ระหว่าง add กับ update -->
                        <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
                        <?php if($edit_mode): ?>
                            <input type="hidden" name="vendor_id" value="<?php echo $edit_vendor_id; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">ชื่อร้านค้า</label>
                            <input type="text" name="vendor_name" class="form-control form-control-sm" required placeholder="เช่น JIB, Advice, Shopee" value="<?php echo htmlspecialchars($edit_vendor_name); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">สาขา / รายละเอียด (เช่น A009)</label>
                            <input type="text" name="location" class="form-control form-control-sm" placeholder="เช่น สาขาเซ็นทรัลชลบุรี หรือ A009" value="<?php echo htmlspecialchars($edit_location); ?>">
                        </div>

                        <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-warning'; ?> w-100 py-2 fw-semibold shadow-sm small mb-2">
                            <?php echo $edit_mode ? 'บันทึกการแก้ไข' : 'บันทึกร้านค้า'; ?>
                        </button>

                        <?php if($edit_mode): ?>
                            <a href="vendors.php" class="btn btn-outline-secondary w-100 py-2 fw-semibold small">ยกเลิกการแก้ไข</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- ตารางแสดงร้านค้า พร้อมปุ่มจัดการ -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th class="py-3 ps-4">ชื่อร้านค้า</th>
                                    <th class="py-3">สาขา / รายละเอียด</th>
                                    <th class="py-3 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($v_result && $v_result->num_rows > 0) {
                                    while($row = $v_result->fetch_assoc()) {
                                        echo "<tr>
                                                <td class='ps-4 fw-semibold text-dark'>" . htmlspecialchars($row['vendor_name']) . "</td>
                                                <td class='text-secondary'>" . htmlspecialchars($row['location']) . "</td>
                                                <td class='text-center'>
                                                    <!-- ปุ่มแก้ไข -->
                                                    <a href='vendors.php?edit_id=" . $row['vendor_id'] . "' class='btn btn-outline-warning btn-sm px-2 me-1' title='แก้ไข'>
                                                        <i class='fa-solid fa-pen'></i>
                                                    </a>
                                                    <!-- ปุ่มลบ -->
                                                    <a href='vendors.php?delete_id=" . $row['vendor_id'] . "' class='btn btn-outline-danger btn-sm px-2' onclick=\"return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบร้านค้านี้?');\" title='ลบ'>
                                                        <i class='fa-solid fa-trash-can'></i>
                                                    </a>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center py-4 text-muted'>ยังไม่มีข้อมูลร้านค้า</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>