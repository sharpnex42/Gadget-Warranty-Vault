<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

$user_id = $_SESSION['user_id'];

// เพิ่มการ JOIN ตาราง Vendors เพื่อดึงชื่อร้านค้ามาแสดง
$sql = "SELECT 
            a.asset_id,
            a.asset_name, 
            a.purchase_date, 
            a.warranty_expiry_date AS expiry_date,
            v.vendor_name 
        FROM Assets a
        LEFT JOIN Vendors v ON a.vendor_id = v.vendor_id
        WHERE a.user_id = '$user_id'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Gadget & Warranty Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-shield-halved text-info"></i> Gadget & Warranty Vault</a>
            <div class="d-flex align-items-center">
                <!-- เพิ่มปุ่มจัดการร้านค้า -->
                <a href="vendors.php" class="btn btn-warning btn-sm px-3 me-2 text-dark fw-bold"><i class="fa-solid fa-store"></i> จัดการร้านค้า</a>
                <a href="dashboard.php" class="btn btn-info btn-sm px-3 me-3 text-white fw-bold"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                
                <span class="text-light me-3 small"><i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">รายการอุปกรณ์ไอทีของคุณ</h2>
                <p class="text-muted small mb-0">จัดการและติดตามวันหมดอายุประกันฮาร์ดแวร์ได้ในที่เดียว</p>
            </div>
            <a href="add_asset.php" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
                <i class="fa-solid fa-plus me-1"></i> เพิ่มอุปกรณ์ใหม่
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark text-uppercase small">
                            <tr>
                                <th class="py-3 ps-4">ชื่ออุปกรณ์</th>
                                <th class="py-3">ร้านค้า</th>
                                <th class="py-3">วันที่ซื้อ</th>
                                <th class="py-3">วันหมดประกัน</th>
                                <th class="py-3">สถานะประกัน</th>
                                <th class="py-3 text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $expiry_date = new DateTime($row["expiry_date"]);
                                    $today = new DateTime();
                                    $interval = $today->diff($expiry_date);
                                    $days_remaining = $interval->format('%r%a');
                                    
                                    if ($days_remaining < 0) {
                                        $status_html = "<span class='badge bg-danger px-3 py-2'>❌ หมดประกันแล้ว</span>";
                                    } elseif ($days_remaining <= 30) {
                                        $status_html = "<span class='badge bg-warning text-dark px-3 py-2'>⚠️ ใกล้หมด (เหลือ $days_remaining วัน)</span>";
                                    } else {
                                        $status_html = "<span class='badge bg-success px-3 py-2'>✅ ยังอยู่ในประกัน</span>";
                                    }
                                    
                                    // จัดการกรณีไม่ได้ระบุร้านค้า
                                    $vendor_display = $row["vendor_name"] ? htmlspecialchars($row["vendor_name"]) : "<span class='text-muted small'>- ไม่ระบุ -</span>";
                                    
                                    echo "<tr>
                                            <td class='ps-4 fw-semibold text-dark'>" . htmlspecialchars($row["asset_name"]) . "</td>
                                            <td>" . $vendor_display . "</td>
                                            <td class='text-secondary'>" . $row["purchase_date"] . "</td>
                                            <td class='text-secondary'>" . $row["expiry_date"] . "</td>
                                            <td>" . $status_html . "</td>
                                            <td class='text-center'>
                                                <a href='maintenance.php?id=" . $row["asset_id"] . "' class='btn btn-outline-primary btn-sm px-2 me-1' title='ประวัติซ่อม'><i class='fa-solid fa-wrench'></i></a>
                                                <a href='delete_asset.php?id=" . $row["asset_id"] . "' class='btn btn-outline-danger btn-sm px-2' onclick=\"return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบอุปกรณ์นี้?');\" title='ลบ'><i class='fa-solid fa-trash-can'></i></a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-5 text-muted'>ยังไม่มีข้อมูลอุปกรณ์ในระบบ</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>