<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบหลังบ้าน - Methanon Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-speedometer2 me-1"></i> Methanon Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">ผู้ใช้งาน: <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold text-secondary">เมนูจัดการระบบ</h3>
            <p class="text-muted">ยินดีต้อนรับเข้าสู่ระบบจัดการหลังบ้าน กรุณาเลือกเมนูที่ต้องการจัดการ</p>
        </div>

        <div class="row g-4">
            <!-- ปุ่มระบบจัดการสินค้า -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-shadow transition">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-box-seam fs-2"></i>
                        </div>
                        <h5 class="fw-bold card-title">จัดการสินค้า</h5>
                        <p class="text-muted small">เพิ่ม แก้ไข ลบ รายการสินค้า และสต็อกสินค้า</p>
                        <a href="products.php" class="btn btn-primary w-100 fw-bold mt-2">
                            เข้าสู่ระบบจัดการสินค้า
                        </a>
                    </div>
                </div>
            </div>

            <!-- ปุ่มระบบจัดการผลงานการให้บริการ -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 hover-shadow transition">
                    <div class="card-body p-4 text-center">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-journal-check fs-2"></i>
                        </div>
                        <h5 class="fw-bold card-title">จัดการผลงานบริการ</h5>
                        <p class="text-muted small">เพิ่ม แก้ไข ลบ ผลงานการให้บริการและอัลบั้มรูปภาพ</p>
                        <a href="services.php" class="btn btn-success w-100 fw-bold mt-2">
                            เข้าสู่ระบบจัดการผลงาน
                        </a>
                    </div>
                </div>
            </div>

            <!-- พื้นที่สำหรับระบบที่จะเพิ่มในอนาคต -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 bg-body-tertiary">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                        <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                            <i class="bi bi-plus-lg fs-2"></i>
                        </div>
                        <h5 class="fw-bold text-muted card-title">ระบบอื่นในอนาคต</h5>
                        <p class="text-muted small mb-0">รองรับการต่อเติมระบบใหม่ๆ ในอนาคต</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>