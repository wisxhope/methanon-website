<?php
// admin/api/get_services.php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. ตรวจสอบการเปิดเชื่อมต่อ DB
$dbPath = __DIR__ . '/../../db.php';
if (!file_exists($dbPath)) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์ db.php ในตำแหน่ง: ' . $dbPath]);
    exit();
}
require_once $dbPath;

$action = $_REQUEST['action'] ?? 'get';
$method = $_SERVER['REQUEST_METHOD'];

// 2. ตรวจสอบ Session เฉพาะ Action ที่ต้องจัดการข้อมูล (add, edit, delete)
// สำหรับ 'get' อนุญาตให้หน้าเว็บ (Frontend) ดึงข้อมูลไปแสดงได้โดยไม่ต้อง Login
if ($action !== 'get' && !isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized Access']);
    exit();
}

try {
    // ------------------------------------------------------------------
    // 1. ดึงข้อมูล (GET) - สำหรับทั้ง Admin และ หน้าเว็บทั่วไป
    // ------------------------------------------------------------------
    if ($action === 'get') {
        try {
            // ดึงรายการผลงาน
            $stmt = $pdo->query("SELECT * FROM services ORDER BY id DESC");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ดึงรูปภาพย่อยของแต่ละผลงาน
            foreach ($services as &$service) {
                $stmtImg = $pdo->prepare("SELECT id, image_path FROM service_images WHERE service_id = ?");
                $stmtImg->execute([$service['id']]);
                $service['detail_images'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
            }

            // คืนค่า JSON สำเร็จ
            echo json_encode([
                'success' => true,
                'data' => $services
            ]);
            exit;
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    // ------------------------------------------------------------------
    // 2. เพิ่มข้อมูล (ADD)
    // ------------------------------------------------------------------
    if ($action === 'add' && $method === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อผลงาน']);
            exit();
        }

        // อัปโหลด Cover Image
        $coverImagePath = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $coverImagePath = uploadFile($_FILES['cover_image']);
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO services (title, category, description, cover_image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $category, $description, $coverImagePath]);
        $serviceId = $pdo->lastInsertId();

        // อัปโหลด Detail Images
        if (isset($_FILES['detail_images']) && is_array($_FILES['detail_images']['name'])) {
            $imgStmt = $pdo->prepare("INSERT INTO service_images (service_id, image_path) VALUES (?, ?)");
            foreach ($_FILES['detail_images']['name'] as $key => $name) {
                if ($_FILES['detail_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileArray = [
                        'name'     => $_FILES['detail_images']['name'][$key],
                        'type'     => $_FILES['detail_images']['type'][$key],
                        'tmp_name' => $_FILES['detail_images']['tmp_name'][$key],
                        'error'    => $_FILES['detail_images']['error'][$key],
                        'size'     => $_FILES['detail_images']['size'][$key]
                    ];
                    $detailPath = uploadFile($fileArray);
                    if ($detailPath) {
                        $imgStmt->execute([$serviceId, $detailPath]);
                    }
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'เพิ่มผลงานเรียบร้อยแล้ว']);
        exit();
    }

    // ------------------------------------------------------------------
    // 3. แก้ไขข้อมูล (EDIT)
    // ------------------------------------------------------------------
    if ($action === 'edit' && $method === 'POST') {
        $id = $_POST['id'] ?? null;
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$id || empty($title)) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit();
        }

        $pdo->beginTransaction();

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $oldImg = $pdo->prepare("SELECT cover_image FROM services WHERE id = ?");
            $oldImg->execute([$id]);
            $old = $oldImg->fetch();
            if ($old && !empty($old['cover_image'])) {
                deleteFile(__DIR__ . '/../../' . $old['cover_image']);
            }

            $coverImagePath = uploadFile($_FILES['cover_image']);
            $stmt = $pdo->prepare("UPDATE services SET title = ?, category = ?, description = ?, cover_image = ? WHERE id = ?");
            $stmt->execute([$title, $category, $description, $coverImagePath, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE services SET title = ?, category = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $category, $description, $id]);
        }

        if (isset($_FILES['detail_images']) && is_array($_FILES['detail_images']['name'])) {
            $imgStmt = $pdo->prepare("INSERT INTO service_images (service_id, image_path) VALUES (?, ?)");
            foreach ($_FILES['detail_images']['name'] as $key => $name) {
                if ($_FILES['detail_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileArray = [
                        'name'     => $_FILES['detail_images']['name'][$key],
                        'type'     => $_FILES['detail_images']['type'][$key],
                        'tmp_name' => $_FILES['detail_images']['tmp_name'][$key],
                        'error'    => $_FILES['detail_images']['error'][$key],
                        'size'     => $_FILES['detail_images']['size'][$key]
                    ];
                    $detailPath = uploadFile($fileArray);
                    if ($detailPath) {
                        $imgStmt->execute([$id, $detailPath]);
                    }
                }
            }
        }

        if (isset($_POST['delete_detail_image_ids']) && is_array($_POST['delete_detail_image_ids'])) {
            $deleteStmt = $pdo->prepare("SELECT image_path FROM service_images WHERE id = ?");
            $delDbStmt = $pdo->prepare("DELETE FROM service_images WHERE id = ?");
            foreach ($_POST['delete_detail_image_ids'] as $imgId) {
                $deleteStmt->execute([$imgId]);
                $imgData = $deleteStmt->fetch();
                if ($imgData && !empty($imgData['image_path'])) {
                    deleteFile(__DIR__ . '/../../' . $imgData['image_path']);
                }
                $delDbStmt->execute([$imgId]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'แก้ไขผลงานเรียบร้อยแล้ว']);
        exit();
    }

    // ------------------------------------------------------------------
    // 4. ลบข้อมูล (DELETE)
    // ------------------------------------------------------------------
    if ($action === 'delete' && $method === 'POST') {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบ ID']);
            exit();
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT cover_image FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch();
        if ($service && !empty($service['cover_image'])) {
            deleteFile(__DIR__ . '/../../' . $service['cover_image']);
        }

        $imgStmt = $pdo->prepare("SELECT image_path FROM service_images WHERE service_id = ?");
        $imgStmt->execute([$id]);
        $detailImages = $imgStmt->fetchAll();
        foreach ($detailImages as $img) {
            if (!empty($img['image_path'])) {
                deleteFile(__DIR__ . '/../../' . $img['image_path']);
            }
        }

        $pdo->prepare("DELETE FROM service_images WHERE service_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'ลบผลงานเรียบร้อยแล้ว']);
        exit();
    }

    echo json_encode(['success' => false, 'message' => 'Invalid Action']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

function uploadFile($file) {
    $uploadDir = __DIR__ . '/../../uploads/services/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (in_array(strtolower($ext), $allowedTypes)) {
        $newFileName = uniqid('service_', true) . '.' . strtolower($ext);
        $targetFilePath = $uploadDir . $newFileName;
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return 'uploads/services/' . $newFileName;
        }
    }
    return null;
}

function deleteFile($filePath) {
    if (file_exists($filePath) && is_file($filePath)) {
        @unlink($filePath);
    }
}