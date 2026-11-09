<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลสรุปค่าใช้จ่ายแยกตามอุปกรณ์
$sql = "SELECT a.asset_name, COALESCE(SUM(m.cost), 0) AS total_cost 
        FROM Assets a 
        LEFT JOIN Maintenance_Logs m ON a.asset_id = m.asset_id 
        WHERE a.user_id = '$user_id' 
        GROUP BY a.asset_id 
        ORDER BY total_cost DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Gadget Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-shield-halved text-info"></i> Gadget & Warranty Vault</a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-outline-light btn-sm px-3"><i class="fa-solid fa-list"></i> กลับหน้ารายการ</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">📊 Dashboard สรุปค่าซ่อมบำรุง</h2>
                <p class="text-muted small mb-0">รายงานสรุปค่าใช้จ่ายแยกตามอุปกรณ์</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-primary text-uppercase small">
                                <tr>
                                    <th class="py-3 ps-4">ชื่ออุปกรณ์</th>
                                    <th class="py-3 text-end pe-4">ยอดรวมค่าซ่อมบำรุง (บาท)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grand_total = 0;
                                if ($result && $result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $grand_total += $row['total_cost'];
                                        echo "<tr>
                                                <td class='ps-4 fw-semibold text-dark'>" . htmlspecialchars($row['asset_name']) . "</td>
                                                <td class='text-end pe-4 text-danger fw-bold'>" . number_format($row['total_cost'], 2) . " ฿</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2' class='text-center py-4 text-muted'>ยังไม่มีข้อมูลอุปกรณ์</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td class="ps-4 fw-bold text-end">รวมค่าใช้จ่ายทั้งหมดทุกอุปกรณ์:</td>
                                    <td class="text-end pe-4 fw-bold text-warning h5 mb-0"><?php echo number_format($grand_total, 2); ?> ฿</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>