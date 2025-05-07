<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$host = 'localhost'; // เปลี่ยนตามการตั้งค่า
$db = 'classroom_booking'; // เปลี่ยนตามชื่อฐานข้อมูล
$username = 'root'; // เปลี่ยนตามการตั้งค่า
$password = 'root'; // เปลี่ยนตามการตั้งค่า

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $building_number = $_POST['building_number'];
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];
    $room_size = $_POST['room_size'];
    $status = $_POST['status'];
    $equipment = $_POST['equipment']; // ตรวจสอบให้แน่ใจว่าฟิลด์นี้มีในฟอร์ม

    // ตรวจสอบค่าที่รับมา
    if(empty($building_number) || empty($room_name) || empty($room_detail) || empty($room_size) || empty($status) || empty($equipment)) {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // สร้างคำสั่ง SQL สำหรับการเพิ่มห้อง
        $sql = "INSERT INTO rooms (building_number, room_name, room_detail, room_size, status, equipment)
                VALUES (?, ?, ?, ?, ?, ?)";

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $building_number, $room_name, $room_detail, $room_size, $status, $equipment);

        // Execute
        if ($stmt->execute()) {
            header("Location: manage_rooms.php?success=1"); // รีไดเร็กไปที่หน้าจัดการห้อง
        } else {
            echo "เกิดข้อผิดพลาด: " . $stmt->error;
        }
    }
}

$conn->close();
?>