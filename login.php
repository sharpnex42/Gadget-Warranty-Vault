<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้นี้!";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Login - Gadget & Warranty Vault</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content: center;">
            <div class="col-md-4 mx-auto">
                <div class="card shadow border-0 rounded-4 p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">🛡️ Gadget Vault</h3>
                        <p class="text-muted small">ระบบจัดการข้อมูลฮาร์ดแวร์และประกัน</p>
                    </div>
                    <?php if(isset($error)) { echo "<div class='alert alert-danger py-2 small text-center'>$error</div>"; } ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" required placeholder="กรอกชื่อผู้ใช้งาน">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="กรอกรหัสผ่าน">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">เข้าสู่ระบบ</button>
                    </form>
                    <div class="text-center mt-3">
                        <span class="text-muted small">ยังไม่มีบัญชี?</span> <a href="register.php" class="text-decoration-none fw-semibold">สมัครสมาชิก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>