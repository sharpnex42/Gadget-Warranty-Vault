<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Users (username, password_hash) VALUES ('$username', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Register - Gadget & Warranty Vault</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center py-4" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 mx-auto">
                <div class="card shadow border-0 rounded-4 p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-success">📝 สมัครสมาชิก</h3>
                        <p class="text-muted small">สร้างบัญชีเพื่อเริ่มจัดเก็บข้อมูลอุปกรณ์ของคุณ</p>
                    </div>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" required placeholder="กำหนด Username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="กำหนด Password">
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm">ยืนยันสมัครสมาชิก</button>
                    </form>
                    <div class="text-center mt-3">
                        <span class="text-muted small">มีบัญชีอยู่แล้ว?</span> <a href="login.php" class="text-decoration-none fw-semibold">เข้าสู่ระบบ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>