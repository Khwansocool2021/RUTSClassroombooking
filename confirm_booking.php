<?php
session_start();
include("config.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$room_id = $_GET['room_id'];
$term = $_GET['term'];
$year = $_GET['year'];

// ตรวจสอบว่า room_id, term และ year มีค่าถูกต้องหรือไม่
if (empty($room_id) || empty($term) || empty($year)) {
    die("ข้อมูลไม่ถูกต้อง");
}

// ดึงข้อมูลห้องที่ต้องการจอง
$sql = "SELECT * FROM rooms WHERE id = :room_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['room_id' => $room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("ไม่พบข้อมูลห้อง");
}

// เมื่อคลิกปุ่ม "ยืนยันการจอง"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $user_id = $_SESSION['user']['id'];
    $user_phone = $_POST['user_phone'];
    $class_period = $_POST['class_period'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $use_date = $_POST['use_date'];

    // ตรวจสอบว่าเวลาจองถูกต้องหรือไม่
    if (empty($start_time) || empty($end_time)) {
        die("กรุณากรอกเวลาให้ครบถ้วน");
    }

    // บันทึกข้อมูลการจองห้อง
    $sql = "INSERT INTO bookings (user_id, room_id, booking_date, start_time, end_time, class_period, semester, academic_year, use_date, user_phone) 
            VALUES (:user_id, :room_id, CURRENT_DATE, :start_time, :end_time, :class_period, :semester, :academic_year, :use_date, :user_phone)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'user_id' => $user_id,
        'room_id' => $room_id,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'class_period' => $class_period,
        'semester' => $semester,
        'academic_year' => $academic_year,
        'use_date' => $use_date,
        'user_phone' => $user_phone
    ]);

    echo "<script>alert('การจองห้องเรียนสำเร็จ'); window.location.href = 'booking.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยืนยันการจองห้อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>ยืนยันการจองห้อง: <?= htmlspecialchars($room['room_name']) ?></h2>
    <form method="post">
        <div class="mb-3">
            <label for="start_time" class="form-label">เวลาเริ่มต้น</label>
            <input type="time" class="form-control" id="start_time" name="start_time" required>
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
            <input type="time" class="form-control" id="end_time" name="end_time" required>
        </div>
        <div class="mb-3">
            <label for="class_period" class="form-label">คาบที่จอง</label>
            <input type="text" class="form-control" id="class_period" name="class_period" required>
        </div>
        <div class="mb-3">
            <label for="semester" class="form-label">เทอม</label>
            <input type="text" class="form-control" id="semester" name="semester" value="<?= $term ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="academic_year" class="form-label">ปีการศึกษา</label>
            <input type="text" class="form-control" id="academic_year" name="academic_year" value="<?= $year ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="use_date" class="form-label">วันที่ใช้ห้อง</label>
            <input type="date" class="form-control" id="use_date" name="use_date" required>
        </div>
        <div class="mb-3">
            <label for="user_phone" class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control" id="user_phone" name="user_phone" required>
        </div>
        <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
    </form>
</div>

</body>
</html>