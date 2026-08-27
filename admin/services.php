<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
session_start();
// ตรวจสอบ Login (เปิดใช้งานตามความเหมาะสม)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผลงาน/บริการ - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .thumb-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        .preview-box {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #f8f9fa;
        }
        .detail-img-wrapper {
            position: relative;
            display: inline-block;
        }
        .btn-remove-detail {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            padding: 0;
            line-height: 1;
            border-radius: 50%;
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>ระบบจัดการผลงาน / บริการ</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal" onclick="prepareAddModal()">
            + เพิ่มผลงานใหม่
        </button>
    </div>

    <!-- ตารางแสดงรายการผลงาน -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">รูปหลัก</th>
                            <th style="width: 200px;">ชื่อผลงาน</th>
                            <th style="width: 150px;">หมวดหมู่</th>
                            <th>รายละเอียด</th>
                            <th style="width: 160px;">รูปเพิ่มเติม</th>
                            <th style="width: 140px;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="serviceTableBody">
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">กำลังโหลดข้อมูล...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 1. MODAL เพิ่มผลงาน -->
<!-- ========================================== -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addServiceForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มผลงานใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อผลงาน <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required placeholder="เช่น งานติดตั้งระบบไฟฟ้า">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">หมวดหมู่</label>
                                <input type="text" name="category" class="form-control" placeholder="เช่น บริการทั่วไป, งานระบบ">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">รายละเอียด</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="ระบุรายละเอียดผลงาน..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">รูปภาพหลัก (Cover)</label>
                                <img id="add_cover_preview" class="preview-box border mb-2" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='110' viewBox='0 0 200 110'><rect width='100%' height='100%' fill='%23f8f9fa'/><text x='50%' y='50%' fill='%236c757d' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-size='14'>เลือกรูปภาพ</text></svg>">
                                <input type="file" name="cover_image" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'add_cover_preview')">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">รูปภาพเพิ่มเติม (หลายรูป)</label>
                                <input type="file" name="detail_images[]" class="form-control form-control-sm" accept="image/*" multiple>
                                <small class="text-muted fs-7">กด Ctrl เลือกหลายรูปพร้อมกันได้</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" id="addSubmitBtn" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. MODAL แก้ไขผลงาน -->
<!-- ========================================== -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editServiceForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขข้อมูลผลงาน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อผลงาน <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="edit_title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">หมวดหมู่</label>
                                <input type="text" name="category" id="edit_category" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">รายละเอียด</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">รูปภาพหลักปัจจุบัน</label>
                                <img id="edit_cover_preview" class="preview-box border mb-2" src="">
                                <input type="file" name="cover_image" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'edit_cover_preview')">
                                <small class="text-muted">เลือกภาพใหม่หากต้องการเปลี่ยน</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">รูปภาพเพิ่มเติมเดิม</label>
                                <div id="edit_detail_images_container" class="d-flex flex-wrap gap-2 mb-2 p-2 border rounded bg-white" style="min-height: 60px;"></div>
                                <label class="form-label fw-bold">เพิ่มรูปภาพย่อยใหม่</label>
                                <input type="file" name="detail_images[]" class="form-control form-control-sm" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" id="editSubmitBtn" class="btn btn-warning">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// เก็บข้อมูลผลงานทั้งหมดที่โหลดมาจาก API
let globalServices = [];

// รูป SVG Placeholder สำรองกรณีไม่มีรูป
const DEFAULT_SVG = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="110" viewBox="0 0 200 110"><rect width="100%" height="100%" fill="%23f8f9fa"/><text x="50%" y="50%" fill="%236c757d" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="14">เลือกรูปภาพ</text></svg>`;
// ใช้ SVG Data URI ในรูปแบบ Base64 เพื่อป้องกันปัญหา Syntax หลุดใน HTML Attribute
const THUMB_SVG = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDYwIDYwIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZTllY2VmIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZpbGw9IiM2Yzc1N2QiIGRvbWluYW50LWJhc2VsaW5lPSJtaWRkbGUiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZvbnQtZmFtaWx5PSJzYW5zLXNlcmlmIiBmb250LXNpemU9IjEwIj5ObyBJbWc8L3RleHQ+PC9zdmc+";

// เมื่อโหลดหน้าเว็บเสร็จ
document.addEventListener('DOMContentLoaded', () => {
    loadServices();
});

// Helper Escape HTML เพื่อความปลอดภัย
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// ------------------------------------------------------------------
// 1. ดึงรายการผลงานทั้งหมดมาแสดง (GET)
// ------------------------------------------------------------------
async function loadServices() {
    const tbody = document.getElementById('serviceTableBody');
    try {
        const res = await fetch('api/get_services.php?action=get');
        const result = await res.json();

        if (result.success && Array.isArray(result.data)) {
            globalServices = result.data;
            if (result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">ยังไม่มีข้อมูลผลงาน</td></tr>`;
                return;
            }

            tbody.innerHTML = result.data.map(s => {
                const coverPath = s.cover_image || s.image_path;
                const coverUrl = coverPath ? `../${coverPath}` : THUMB_SVG;
                
                return `
                    <tr>
                        <td>
                            <img src="${escapeHtml(coverUrl)}" class="thumb-img border" onerror="this.src='${THUMB_SVG}'">
                        </td>
                        <td class="fw-bold">${escapeHtml(s.title)}</td>
                        <td><span class="badge bg-secondary">${escapeHtml(s.category) || '-'}</span></td>
                        <td class="text-muted small">${escapeHtml(s.description) || '-'}</td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                ${(s.detail_images || []).map(img => `
                                    <img src="../${escapeHtml(img.image_path)}" style="width:30px; height:30px; object-fit:cover;" class="rounded border" onerror="this.src='${THUMB_SVG}'">
                                `).join('')}
                            </div>
                        </td>
                        <td class="text-center">
                            <button onclick="openEditModal(${s.id})" class="btn btn-outline-warning btn-sm me-1">แก้ไข</button>
                            <button onclick="deleteService(${s.id})" class="btn btn-outline-danger btn-sm">ลบ</button>
                        </td>
                    </tr>
                `;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">เกิดข้อผิดพลาด: ${result.message || 'ไม่สามารถโหลดข้อมูลได้'}</td></tr>`;
        }
    } catch (err) {
        console.error('Fetch Error:', err);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้</td></tr>`;
    }
}

// ------------------------------------------------------------------
// 2. ฟังก์ชั่นเพิ่มข้อมูลใหม่ (ADD)
// ------------------------------------------------------------------
function prepareAddModal() {
    document.getElementById('addServiceForm').reset();
    document.getElementById('add_cover_preview').src = DEFAULT_SVG;
}

document.getElementById('addServiceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('addSubmitBtn');
    submitBtn.disabled = true;

    try {
        const formData = new FormData(this);
        formData.append('action', 'add'); // ระบุ action

        const res = await fetch('api/get_services.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();

        if (result.success) {
            alert(result.message || 'บันทึกสำเร็จ');
            bootstrap.Modal.getInstance(document.getElementById('addServiceModal')).hide();
            loadServices();
        } else {
            alert('เกิดข้อผิดพลาด: ' + result.message);
        }
    } catch (err) {
        console.error(err);
        alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
    } finally {
        submitBtn.disabled = false;
    }
});

// ------------------------------------------------------------------
// 3. ฟังก์ชั่นเปิดและแก้ไขข้อมูล (EDIT)
// ------------------------------------------------------------------
function openEditModal(id) {
    const item = globalServices.find(s => s.id == id);
    if (!item) return;

    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_title').value = item.title || '';
    document.getElementById('edit_category').value = item.category || '';
    document.getElementById('edit_description').value = item.description || '';

    // ตั้งค่าพรีวิวรูปหลัก
    const coverPath = item.cover_image || item.image_path;
    document.getElementById('edit_cover_preview').src = coverPath ? `../${coverPath}` : DEFAULT_SVG;

    // แสดงรายการรูปย่อยเดิมพร้อมปุ่มลบ
    const detailContainer = document.getElementById('edit_detail_images_container');
    if (item.detail_images && item.detail_images.length > 0) {
        detailContainer.innerHTML = item.detail_images.map(img => `
            <div class="detail-img-wrapper" id="detail-img-box-${img.id}">
                <img src="../${escapeHtml(img.image_path)}" style="width:45px; height:45px; object-fit:cover;" class="rounded border">
                <button type="button" class="btn btn-danger btn-remove-detail" onclick="markDeleteDetailImage(${img.id})">&times;</button>
            </div>
        `).join('');
    } else {
        detailContainer.innerHTML = '<small class="text-muted">ไม่มีรูปภาพย่อย</small>';
    }

    const editModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
    editModal.show();
}

// ทำเครื่องหมายลบรูปภาพย่อยบางรูปในฟอร์มแก้ไข
function markDeleteDetailImage(imgId) {
    if (confirm('คุณต้องการลบรูปย่อยนี้ใช่หรือไม่?')) {
        const box = document.getElementById(`detail-img-box-${imgId}`);
        if (box) box.remove();

        // สร้าง hidden input ส่ง id รูปที่จะลบไปให้ PHP
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_detail_image_ids[]';
        input.value = imgId;
        document.getElementById('editServiceForm').appendChild(input);
    }
}

document.getElementById('editServiceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = document.getElementById('editSubmitBtn');
    submitBtn.disabled = true;

    try {
        const formData = new FormData(this);
        formData.append('action', 'edit'); // ระบุ action

        const res = await fetch('api/get_services.php', {
            method: 'POST',
            body: formData
        });
        const result = await res.json();

        if (result.success) {
            alert(result.message || 'แก้ไขสำเร็จ');
            bootstrap.Modal.getInstance(document.getElementById('editServiceModal')).hide();
            loadServices();
        } else {
            alert('เกิดข้อผิดพลาด: ' + result.message);
        }
    } catch (err) {
        console.error(err);
        alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
    } finally {
        submitBtn.disabled = false;
    }
});

// ------------------------------------------------------------------
// 4. ฟังก์ชั่นลบผลงาน (DELETE)
// ------------------------------------------------------------------
async function deleteService(id) {
    if (confirm('คุณต้องการลบผลงานนี้ใช่หรือไม่? (รูปภาพทั้งหมดจะถูกลบออกจากระบบ)')) {
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            const res = await fetch('api/get_services.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();

            if (result.success) {
                alert(result.message || 'ลบข้อมูลสำเร็จ');
                loadServices();
            } else {
                alert('เกิดข้อผิดพลาด: ' + result.message);
            }
        } catch (err) {
            console.error(err);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        }
    }
}

// ------------------------------------------------------------------
// Helper: พรีวิวรูปภาพขณะเลือกไฟล์
// ------------------------------------------------------------------
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>