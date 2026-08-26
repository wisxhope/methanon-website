<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../auth_check.php';
require_once '../../db.php'; // ปรับ Path ตามไฟล์เชื่อมต่อ DB ของคุณ

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    // ---------------------------------------------------------
    // 1. ดึงข้อมูลสินค้าทั้งหมด (GET)
    // ---------------------------------------------------------
    if ($method === 'GET') {
        $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $products
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ---------------------------------------------------------
    // 2. จัดการข้อมูลแบบ POST (เพิ่ม / แก้ไข / ลบ)
    // ---------------------------------------------------------
    if ($method === 'POST') {

        // --- เพิ่มสินค้าใหม่ ---
        if ($action === 'add') {
            $title       = $_POST['title'] ?? '';
            $category    = $_POST['category'] ?? '';
            $subcategory = $_POST['subcategory'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? 0;
            $image_path  = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_name = time() . '_' . uniqid() . '.' . $ext;
                $upload_dir = '../../uploads/';

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                    $image_path = 'uploads/' . $new_name;
                }
            }

            $sql = "INSERT INTO products (title, category, subcategory, description, price, image) 
                    VALUES (:title, :category, :subcategory, :description, :price, :image)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'       => $title,
                ':category'    => $category,
                ':subcategory' => $subcategory,
                ':description' => $description,
                ':price'       => $price,
                ':image'       => $image_path
            ]);

            echo json_encode(['success' => true, 'message' => 'เพิ่มสินค้าเรียบร้อยแล้ว']);
            exit;
        }

        // --- แก้ไขสินค้า ---
        if ($action === 'edit') {
            $id          = $_POST['id'] ?? 0;
            $title       = $_POST['title'] ?? '';
            $category    = $_POST['category'] ?? '';
            $subcategory = $_POST['subcategory'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? 0;

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบ ID สินค้า']);
                exit;
            }

            // ถ้ามีการอัปโหลดรูปใหม่
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $new_name = time() . '_' . uniqid() . '.' . $ext;
                $upload_dir = '../../uploads/';

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_name)) {
                    $image_path = 'uploads/' . $new_name;
                    
                    // อัปเดตแบบเปลี่ยนรูปภาพด้วย
                    $sql = "UPDATE products SET title=:title, category=:category, subcategory=:subcategory, 
                            description=:description, price=:price, image=:image WHERE id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':title'       => $title,
                        ':category'    => $category,
                        ':subcategory' => $subcategory,
                        ':description' => $description,
                        ':price'       => $price,
                        ':image'       => $image_path,
                        ':id'          => $id
                    ]);
                    echo json_encode(['success' => true, 'message' => 'แก้ไขสินค้าเรียบร้อยแล้ว']);
                    exit;
                }
            }

            // ถ้าไม่อัปโหลดรูปใหม่ (ใช้รูปเดิม)
            $sql = "UPDATE products SET title=:title, category=:category, subcategory=:subcategory, 
                    description=:description, price=:price WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'       => $title,
                ':category'    => $category,
                ':subcategory' => $subcategory,
                ':description' => $description,
                ':price'       => $price,
                ':id'          => $id
            ]);

            echo json_encode(['success' => true, 'message' => 'แก้ไขสินค้าเรียบร้อยแล้ว']);
            exit;
        }

        // --- ลบสินค้า ---
        if ($action === 'delete') {
            $id = $_POST['id'] ?? 0;

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ไม่พบ ID สินค้า']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'ลบสินค้าเรียบร้อยแล้ว']);
            exit;
        }
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}