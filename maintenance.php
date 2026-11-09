<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

$user_id = $_SESSION['user_id'];
$asset_id = isset($_GET['id']) ? $_GET['id'] : 0;

// ตรวจสอบว่าเป็นอุปกรณ์ของ User คนนี้จริงๆ
$check_sql = "SELECT asset_name FROM Assets WHERE asset_id = '$asset_id' AND user_id = '$user_id'";
$check_res = $conn->query($check_sql);
if ($check_res->num_rows == 0) {
    echo "<script>alert('ไม่พบข้อมูลอุปกรณ์'); window.location.href='index.php';</script>";
    exit();
}
$asset = $check_res->fetch_assoc();

// บันทึกข้อมูลเมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_date = $_POST['log_date'];
    $description = $conn->real_escape_string($_POST['description']);
    $cost = $_POST['cost'];

    $insert_sql = "INSERT INTO Maintenance_Logs (asset_id, log_date, description, cost) 
                   VALUES ('$asset_id', '$log_date', '$description', '$cost')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "<script>alert('บันทึกประวัติการซ่อมสำเร็จ!'); window.location.href='maintenance.php?id=$asset_id';</script>";
    } else {
        $error = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ดึงประวัติการซ่อมของอุปกรณ์นี้
$logs_sql = "SELECT * FROM Maintenance_Logs WHERE asset_id = '$asset_id' ORDER BY log_date DESC";
$logs_result = $conn->query($logs_sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติซ่อมบำรุง - Gadget Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
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
            <!-- คอลัมน์ซ้าย: ฟอร์มเพิ่มประวัติ -->
            <div class="col-md-4 mb-4">
                <div class="card shadow border-0 rounded-4 p-4">
                    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-wrench"></i> บันทึกการส่งซ่อม/เคลม</h5>
                    <p class="text-muted small">อุปกรณ์: <strong><?php echo htmlspecialchars($asset['asset_name']); ?></strong></p>
                    
                    <?php if(isset($error)) { echo "<div class='alert alert-danger small'>$error</div>"; } ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">วันที่ส่งซ่อม/เคลม</label>
                            <input type="date" name="log_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">รายละเอียด / อาการเสีย</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" required placeholder="เช่น เปลี่ยนแบตเตอรี่, เคลมปุ่มคีย์บอร์ด"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">ค่าใช้จ่าย (บาท)</label>
                            <input type="number" name="cost" class="form-control form-control-sm" min="0" step="0.01" required placeholder="หากเคลมฟรีให้ใส่ 0">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm small">บันทึกประวัติ</button>
                    </form>
                </div>
            </div>

            <!-- คอลัมน์ขวา: ตารางแสดงประวัติ -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left text-secondary"></i> ประวัติการซ่อมบำรุง</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase small">
                                <tr>
                                    <th class="py-3 ps-4">วันที่</th>
                                    <th class="py-3">รายละเอียด</th>
                                    <th class="py-3 text-end pe-4">ค่าใช้จ่าย (฿)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_cost = 0;
                                if ($logs_result && $logs_result->num_rows > 0) {
                                    while($log = $logs_result->fetch_assoc()) {
                                        $total_cost += $log['cost'];
                                        echo "<tr>
                                                <td class='ps-4 text-secondary'>" . $log['log_date'] . "</td>
                                                <td>" . htmlspecialchars($log['description']) . "</td>
                                                <td class='text-end pe-4 fw-semibold text-danger'>" . number_format($log['cost'], 2) . "</td>
                                              </tr>";
                                    }
                                    // แถวสรุปยอดรวม
                                    echo "<tr class='table-dark'>
                                            <td colspan='2' class='text-end fw-bold'>รวมค่าใช้จ่ายอุปกรณ์นี้:</td>
                                            <td class='text-end pe-4 fw-bold text-warning'>" . number_format($total_cost, 2) . " ฿</td>
                                          </tr>";
                                } else {
                                    echo "<tr><td colspan='3' class='text-center py-4 text-muted'>ยังไม่มีประวัติการซ่อมบำรุง</td></tr>";
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