<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../auth_check.php'; // ตรวจสอบ Session Admin
require_once '../../db.php';     // ไฟล์เชื่อมต่อ Database (ปรับ Path ตามโครงสร้างโฟลเดอร์)

try {
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $products
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}