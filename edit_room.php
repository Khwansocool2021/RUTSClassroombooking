<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$host = 'localhost';
$db = 'classroom_booking';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // ดึงข้อมูลห้องที่ต้องการแก้ไข
    $sql = "SELECT * FROM rooms WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $room = $result->fetch_assoc();
    } else {
        echo "ห้องนี้ไม่พบในฐานข้อมูล";
        exit();
    }
}

// ตรวจสอบการส่งข้อมูลเมื่อทำการบันทึกการแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $building_number = $_POST['building_number'];
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];
    $room_size = $_POST['room_size'];
    $status = $_POST['status'];
    $equipment = $_POST['equipment'];

    // คำสั่ง SQL สำหรับการอัพเดทข้อมูลห้อง
    $sql = "UPDATE rooms SET building_number = ?, room_name = ?, room_detail = ?, room_size = ?, status = ?, equipment = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $building_number, $room_name, $room_detail, $room_size, $status, $equipment, $room_id);

    if ($stmt->execute()) {
        header("Location: manage_rooms.php?success=2"); // รีไดเร็กไปที่หน้าจัดการห้อง
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลห้อง | ระบบจองห้องเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>แก้ไขข้อมูลห้อง</h2>
    <form action="edit_room.php?id=<?= $room_id ?>" method="POST">
        <div class="mb-3">
            <label for="building_number" class="form-label">หมายเลขอาคาร</label>
            <input type="text" class="form-control" id="building_number" name="building_number" value="<?= htmlspecialchars($room['building_number']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="room_name" class="form-label">ชื่อ/หมายเลขห้อง</label>
            <input type="text" class="form-control" id="room_name" name="room_name" value="<?= htmlspecialchars($room['room_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="room_detail" class="form-label">รายละเอียดห้อง</label>
            <input type="text" class="form-control" id="room_detail" name="room_detail" value="<?= htmlspecialchars($room['room_detail']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="room_size" class="form-label">ขนาดห้อง (จำนวนที่นั่ง)</label>
            <input type="number" class="form-control" id="room_size" name="room_size" value="<?= htmlspecialchars($room['room_size']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">สถานะ</label>
            <select class="form-select" id="status" name="status" required>
                <option value="ว่าง" <?= ($room['status'] == 'ว่าง') ? 'selected' : '' ?>>ว่าง</option>
                <option value="ไม่ว่าง" <?= ($room['status'] == 'ไม่ว่าง') ? 'selected' : '' ?>>ไม่ว่าง</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="equipment" class="form-label">ครุภัณฑ์ รายละเอียด</label>
            <input type="text" class="form-control" id="equipment" name="equipment" value="<?= htmlspecialchars($room['equipment']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
    </form>
</div>
</body>
</html>