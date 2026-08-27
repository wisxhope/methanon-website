<?php
// บังคับปิด Cache ระดับ API
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Type: application/json; charset=utf-8");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "methanon_db";

$conn = new mysqli($host, $user, $pass, $db);

// กำหนดการส่งข้อมูลเป็น utf8mb4 รองรับภาษาไทยและอักขระพิเศษ
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error], JSON_UNESCAPED_UNICODE);
    exit();
}

$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = array();
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row["price"] = (float)$row["price"];
        $products[] = $row;
    }
}

// ส่งผลลัพธ์เป็น JSON (ใช้ JSON_UNESCAPED_UNICODE อ่านภาษาไทยได้ชัดเจนในระบบ Debug)
echo json_encode($products, JSON_UNESCAPED_UNICODE);

$conn->close();
?>