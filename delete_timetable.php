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
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // ตรวจสอบว่า id นี้มีอยู่หรือไม่
    $check = $conn->prepare("SELECT id FROM timetable WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM timetable WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: manage_timetable.php");
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการลบข้อมูล";
        }
    } else {
        echo "ไม่พบตารางสอนที่ต้องการลบ";
    }
} else {
    echo "ไม่มีข้อมูล ID ส่งมา";
}

$conn->close();
?>