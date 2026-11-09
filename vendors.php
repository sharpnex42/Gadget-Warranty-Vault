<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

// เมื่อมีการกดบันทึกร้านค้าใหม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vendor_name'])) {
    $vendor_name = $conn->real_escape_string(trim($_POST['vendor_name']));
    $location = $conn->real_escape_string(trim($_POST['location']));
    
    $sql = "INSERT INTO Vendors (vendor_name, location) VALUES ('$vendor_name', '$location')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('เพิ่มร้านค้าสำเร็จ!'); window.location.href='vendors.php';</script>";
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ดึงข้อมูลร้านค้าทั้งหมด
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
            <!-- ฟอร์มเพิ่มร้านค้า -->
            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 rounded-4 p-4">
                    <h5 class="fw-bold text-warning mb-3"><i class="fa-solid fa-store"></i> เพิ่มร้านค้าใหม่</h5>
                    <?php if(isset($error)) { echo "<div class='alert alert-danger small'>$error</div>"; } ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">ชื่อร้านค้า</label>
                            <input type="text" name="vendor_name" class="form-control form-control-sm" required placeholder="เช่น JIB, Advice, Shopee">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">สาขา / ลิงก์ร้านค้า (ทางเลือก)</label>
                            <input type="text" name="location" class="form-control form-control-sm" placeholder="เช่น สาขาเซ็นทรัลชลบุรี">
                        </div>
                        <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold shadow-sm small">บันทึกร้านค้า</button>
                    </form>
                </div>
            </div>

            <!-- ตารางแสดงร้านค้า -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th class="py-3 ps-4">ชื่อร้านค้า</th>
                                    <th class="py-3">สาขา / รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($v_result && $v_result->num_rows > 0) {
                                    while($row = $v_result->fetch_assoc()) {
                                        echo "<tr>
                                                <td class='ps-4 fw-semibold text-dark'>" . htmlspecialchars($row['vendor_name']) . "</td>
                                                <td class='text-secondary'>" . htmlspecialchars($row['location']) . "</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center py-4 text-muted'>ยังไม่มีข้อมูลร้านค้า</td></tr>";
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