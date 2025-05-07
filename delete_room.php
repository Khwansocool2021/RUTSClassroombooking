<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // เชื่อมต่อฐานข้อมูล
    $host = 'localhost';
    $db = 'classroom_booking';
    $username = 'root';
    $password = 'root';

    $conn = new mysqli($host, $username, $password, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // คำสั่ง SQL สำหรับการลบข้อมูลห้อง
    $sql = "DELETE FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);

    if ($stmt->execute()) {
        header("Location: manage_rooms.php?success=1"); // รีไดเร็กไปที่หน้าจัดการห้อง
    } else {
        echo "Error: " . $stmt->error;
    }

    $conn->close();
} else {
    echo "ไม่มีข้อมูลที่ต้องการลบ";
}
?>