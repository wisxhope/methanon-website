<?php
// db.php
$host     = 'localhost';
$dbname   = 'methanon_db';
$username = 'root';      // ค่าเริ่มต้นของ Laragon
$password = '';          // ค่าเริ่มต้นของ Laragon (รหัสผ่านว่าง)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>