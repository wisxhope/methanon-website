<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการสินค้าหลังบ้าน - Methanon</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <style>
        .ts-control {
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-color: #dee2e6;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Methanon Admin</a>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="btn btn-outline-light btn-sm">กลับหน้าหลัก Dashboard</a>
                <span class="text-white ms-2">ผู้ใช้งาน: <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></strong></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-secondary mb-0">รายการสินค้าทั้งหมด</h4>
            <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#addProductModal">
                + เพิ่มสินค้าใหม่
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">รูปภาพ</th>
                                <th>ชื่อสินค้า</th>
                                <th>หมวดหมู่</th>
                                <th>รายละเอียด</th>
                                <th style="width: 130px;">ราคา (บาท)</th>
                                <th style="width: 140px;" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">กำลังโหลดข้อมูล...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal เพิ่มสินค้า -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">เพิ่มสินค้าใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อสินค้า (Title)</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">หมวดหมู่หลัก (Category)</label>
                                <select name="category" id="add_category" placeholder="เลือกหรือพิมพ์เพิ่ม..." autocomplete="off">
                                    <option value="">เลือกหมวดหมู่...</option>
                                    <option value="เครื่องถ่ายเอกสาร">เครื่องถ่ายเอกสาร</option>
                                    <option value="เครื่องทำลายเอกสาร">เครื่องทำลายเอกสาร</option>
                                    <option value="Printer INKJET">Printer INKJET</option>
                                    <option value="Printer Laser">Printer Laser</option>
                                    <option value="Printer Dot Matrix">Printer Dot Matrix</option>
                                    <option value="CopyPrint">CopyPrint</option>
                                    <option value="TONER">TONER</option>
                                    <option value="INK">INK</option>
                                    <option value="Drum">Drum</option>
                                    <option value="กระดาษ">กระดาษ</option>
                                    <option value="Ribbon">Ribbon</option>
                                    <option value="เครื่องตอกบัตร">เครื่องตอกบัตร</option>
                                    <option value="เครื่องสแกนนิ้ว">เครื่องสแกนนิ้ว</option>
                                    <option value="เครื่องคิดเลข">เครื่องคิดเลข</option>
                                    <option value="จอภาพ">จอภาพ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">แบรนด์ (Subcategory)</label>
                                <select name="subcategory" id="add_subcategory" placeholder="เลือกหรือพิมพ์เพิ่ม..." autocomplete="off">
                                    <option value="">เลือกแบรนด์...</option>
                                    <option value="KYOCERA">KYOCERA</option>
                                    <option value="EPSON">EPSON</option>
                                    <option value="BROTHER">BROTHER</option>
                                    <option value="OKI">OKI</option>
                                    <option value="NEOCAL">NEOCAL</option>
                                    <option value="RISO">RISO</option>
                                    <option value="HIP">HIP</option>
                                    <option value="ACER">ACER</option>
                                    <option value="ASUS">ASUS</option>
                                    <option value="IQ BRAND">IQ BRAND</option>
                                    <option value="DOUBLE A">DOUBLE A</option>
                                    <option value="ZIRCON">ZIRCON</option>
                                    <option value="CASIO">CASIO</option>
                                    <option value="CANON">CANON</option>
                                    <option value="RICOH">RICOH</option>
                                    <option value="AURORA">AURORA</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">รายละเอียด</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ราคา</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">รูปภาพสินค้า</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal แก้ไขสินค้า -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">แก้ไขข้อมูลสินค้า</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อสินค้า (Title)</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">หมวดหมู่หลัก</label>
                                <select name="category" id="edit_category" placeholder="เลือกหรือพิมพ์เพิ่ม..." autocomplete="off">
                                    <option value="">เลือกหมวดหมู่...</option>
                                    <option value="เครื่องถ่ายเอกสาร">เครื่องถ่ายเอกสาร</option>
                                    <option value="เครื่องทำลายเอกสาร">เครื่องทำลายเอกสาร</option>
                                    <option value="Printer INKJET">Printer INKJET</option>
                                    <option value="Printer Laser">Printer Laser</option>
                                    <option value="Printer Dot Matrix">Printer Dot Matrix</option>
                                    <option value="CopyPrint">CopyPrint</option>
                                    <option value="TONER">TONER</option>
                                    <option value="INK">INK</option>
                                    <option value="Drum">Drum</option>
                                    <option value="กระดาษ">กระดาษ</option>
                                    <option value="Ribbon">Ribbon</option>
                                    <option value="เครื่องตอกบัตร">เครื่องตอกบัตร</option>
                                    <option value="เครื่องสแกนนิ้ว">เครื่องสแกนนิ้ว</option>
                                    <option value="เครื่องคิดเลข">เครื่องคิดเลข</option>
                                    <option value="จอภาพ">จอภาพ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">แบรนด์</label>
                                <select name="subcategory" id="edit_subcategory" placeholder="เลือกหรือพิมพ์เพิ่ม..." autocomplete="off">
                                    <option value="">เลือกแบรนด์...</option>
                                    <option value="KYOCERA">KYOCERA</option>
                                    <option value="EPSON">EPSON</option>
                                    <option value="BROTHER">BROTHER</option>
                                    <option value="OKI">OKI</option>
                                    <option value="NEOCAL">NEOCAL</option>
                                    <option value="RISO">RISO</option>
                                    <option value="HIP">HIP</option>
                                    <option value="ACER">ACER</option>
                                    <option value="ASUS">ASUS</option>
                                    <option value="IQ BRAND">IQ BRAND</option>
                                    <option value="DOUBLE A">DOUBLE A</option>
                                    <option value="ZIRCON">ZIRCON</option>
                                    <option value="CASIO">CASIO</option>
                                    <option value="CANON">CANON</option>
                                    <option value="RICOH">RICOH</option>
                                    <option value="AURORA">AURORA</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">รายละเอียด</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ราคา</label>
                            <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">เปลี่ยนรูปภาพสินค้า (ปล่อยว่างหากใช้รูปเดิม)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div id="currentImagePreview" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-warning fw-bold">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        let globalProducts = [];
        let tsAddCat, tsAddSub, tsEditCat, tsEditSub;

        const selectConfig = {
            create: true,
            sortField: { field: "text", order: "asc" }
        };

        document.addEventListener("DOMContentLoaded", function () {
            tsAddCat = new TomSelect('#add_category', selectConfig);
            tsAddSub = new TomSelect('#add_subcategory', selectConfig);
            tsEditCat = new TomSelect('#edit_category', selectConfig);
            tsEditSub = new TomSelect('#edit_subcategory', selectConfig);

            loadProducts();
        });

        // 1. ดึงข้อมูลสินค้า (ใช้อยู่ในไฟล์ get_products.php)
        async function loadProducts() {
            try {
                const res = await fetch('api/get_products.php');
                const result = await res.json();
                const tbody = document.getElementById('productTableBody');

                if (result.success && result.data.length > 0) {
                    globalProducts = result.data;
                    tbody.innerHTML = result.data.map(p => `
                        <tr>
                            <td>
                                <img src="../${p.image || 'assets/images/no-image.png'}" 
                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                     class="rounded border" 
                                     onerror="this.src='https://via.placeholder.com/50?text=No+Image'">
                            </td>
                            <td class="fw-bold">${p.title || '-'}</td>
                            <td>
                                <span class="badge bg-info text-dark">${p.category || '-'}</span>
                                <small class="text-muted d-block">${p.subcategory || ''}</small>
                            </td>
                            <td class="text-muted small">${p.description || '-'}</td>
                            <td class="text-primary fw-bold">${parseFloat(p.price || 0).toLocaleString()}</td>
                            <td class="text-center">
                                <button onclick="openEditModal(${p.id})" class="btn btn-outline-warning btn-sm me-1">แก้ไข</button>
                                <button onclick="deleteProduct(${p.id})" class="btn btn-outline-danger btn-sm">ลบ</button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">ยังไม่มีข้อมูลสินค้า</td></tr>`;
                }
            } catch (err) {
                console.error('Error:', err);
                document.getElementById('productTableBody').innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
            }
        }

        // 2. เพิ่มสินค้า
        document.getElementById('addProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const res = await fetch('api/get_products.php?action=add', { method: 'POST', body: formData });
                const result = await res.json();

                if (result.success) {
                    alert(result.message);
                    this.reset();
                    tsAddCat.clear();
                    tsAddSub.clear();
                    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                    loadProducts();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('เกิดข้อผิดพลาดทางเทคนิค');
            }
        });

        // 3. เปิด Modal แก้ไขสินค้า
        function openEditModal(id) {
            const product = globalProducts.find(p => p.id == id);
            if (!product) return;

            document.getElementById('edit_id').value = product.id;
            document.getElementById('edit_title').value = product.title || '';
            document.getElementById('edit_description').value = product.description || '';
            document.getElementById('edit_price').value = product.price || 0;

            tsEditCat.setValue(product.category || '');
            tsEditSub.setValue(product.subcategory || '');

            const preview = document.getElementById('currentImagePreview');
            if (product.image) {
                preview.innerHTML = `<small class="text-muted d-block mb-1">รูปปัจจุบัน:</small><img src="../${product.image}" style="height: 60px;" class="rounded border">`;
            } else {
                preview.innerHTML = '';
            }

            const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
            editModal.show();
        }

        // 4. บันทึกการแก้ไข
        document.getElementById('editProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const res = await fetch('api/get_products.php?action=edit', { method: 'POST', body: formData });
                const result = await res.json();

                if (result.success) {
                    alert(result.message);
                    bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                    loadProducts();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('เกิดข้อผิดพลาดทางเทคนิค');
            }
        });

        // 5. ลบสินค้า
        async function deleteProduct(id) {
            if (confirm('คุณต้องการลบสินค้ารายการนี้ใช่หรือไม่?')) {
                const formData = new FormData();
                formData.append('id', id);

                try {
                    const res = await fetch('api/get_products.php?action=delete', { method: 'POST', body: formData });
                    const result = await res.json();

                    if (result.success) {
                        loadProducts();
                    } else {
                        alert(result.message);
                    }
                } catch (err) {
                    alert('เกิดข้อผิดพลาดทางเทคนิค');
                }
            }
        }
    </script>
</body>
</html>