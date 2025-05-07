<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db = 'classroom_booking';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "DELETE FROM bookings WHERE id = $id";

if ($conn->query($sql)) {
    header("Location: booking_list.php");
    exit();
} else {
    echo "เกิดข้อผิดพลาดในการลบ: " . $conn->error;
}
?>