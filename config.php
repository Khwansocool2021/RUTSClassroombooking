<?php
$host = 'localhost';
$db   = 'classroom_booking';
$user = 'root';
$pass = 'root'; // หรือใส่รหัสผ่าน XAMPP ของคุณ
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $conn = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>