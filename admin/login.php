<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบแอดมิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 360px;">
        <div class="card-body p-4">
            <h4 class="card-title text-center text-primary fw-bold mb-4">Admin Login</h4>
            <div id="alertBox"></div>
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="admin">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="password">
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mt-2">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('alertBox');
    
    try {
        // เปลี่ยนเป็น api/admin_login.php (เพราะโฟลเดอร์ api อยู่ใน admin แล้ว)
        const response = await fetch('api/admin_login.php', { 
            method: 'POST', 
            body: new FormData(this) 
        });

        const rawText = await response.text();
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${rawText}`);
        }
        
        const result = JSON.parse(rawText);
        
        if (result.success) {
            alertBox.innerHTML = `<div class="alert alert-success">เข้าสู่ระบบสำเร็จ กำลังนำท่านไป...</div>`;
            setTimeout(() => window.location.href = 'index.php', 1000);
        } else {
            alertBox.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        console.error('Login Error:', error);
        alertBox.innerHTML = `<div class="alert alert-danger">เกิดข้อผิดพลาด: ${error.message}</div>`;
    }
});
</script>
</body>
</html>