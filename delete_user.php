<?php
session_start();
include("config.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);

echo "<script>alert('ลบผู้ใช้สำเร็จ'); window.location.href = 'users.php';</script>";
?>