<?php
header("Content-Type: application/json; charset=utf-8");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "methanon_db";

$conn = new mysqli($host, $user, $pass, $db);

// แก้ไขจาก utg8mb4 เป็น utf8mb4
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
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

echo json_encode($products);
$conn->close();
?>