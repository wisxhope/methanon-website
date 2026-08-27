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
            $pdf_path    = ''; // [จุดที่เพิ่ม 1] ประกาศตัวแปรเก็บ Path ของ PDF

            // อัปโหลดรูปภาพ
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

            // [จุดที่เพิ่ม 2] จัดการอัปโหลดไฟล์ PDF
            if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') { // ตรวจสอบว่าเป็นนามสกุล pdf
                    $new_name = 'pdf_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir = '../../uploads/pdfs/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $upload_dir . $new_name)) {
                        $pdf_path = 'uploads/pdfs/' . $new_name;
                    }
                }
            }

            // [จุดที่เพิ่ม 3] ปรับ Query เพิ่มคอลัมน์ pdf
            $sql = "INSERT INTO products (title, category, subcategory, description, price, image, pdf) 
                    VALUES (:title, :category, :subcategory, :description, :price, :image, :pdf)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'       => $title,
                ':category'    => $category,
                ':subcategory' => $subcategory,
                ':description' => $description,
                ':price'       => $price,
                ':image'       => $image_path,
                ':pdf'         => $pdf_path
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

            // ดึงข้อมูลเดิมเพื่อเช็กรูปภาพและไฟล์ PDF เดิม
            $stmt_old = $pdo->prepare("SELECT image, pdf FROM products WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch(PDO::FETCH_ASSOC);

            $image_path = $old_data['image'] ?? '';
            $pdf_path   = $old_data['pdf'] ?? '';

            // [จุดที่เพิ่ม 4] ตรวจสอบหากมีการอัปโหลดรูปภาพใหม่
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

            // [จุดที่เพิ่ม 5] ตรวจสอบหากมีการอัปโหลด PDF ใหม่
            if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') {
                    $new_name = 'pdf_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_dir = '../../uploads/pdfs/';

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $upload_dir . $new_name)) {
                        $pdf_path = 'uploads/pdfs/' . $new_name;
                    }
                }
            }

            // [จุดที่เพิ่ม 6] อัปเดตข้อมูลทุกฟิลด์รวมถึง image และ pdf
            $sql = "UPDATE products SET title=:title, category=:category, subcategory=:subcategory, 
                    description=:description, price=:price, image=:image, pdf=:pdf WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'       => $title,
                ':category'    => $category,
                ':subcategory' => $subcategory,
                ':description' => $description,
                ':price'       => $price,
                ':image'       => $image_path,
                ':pdf'         => $pdf_path,
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