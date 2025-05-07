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
$result = $conn->query("SELECT * FROM bookings WHERE id = $id");
$booking = $result->fetch_assoc();

// โหลดห้องทั้งหมด
$rooms = $conn->query("SELECT * FROM rooms");

// โหลดผู้ใช้ทั้งหมด
$users = $conn->query("SELECT * FROM users");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $room_id = $_POST['room_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $class_period = $_POST['class_period'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $use_date = $_POST['use_date'];
    $user_phone = $_POST['user_phone'];

    $sql = "UPDATE bookings SET 
        user_id='$user_id', 
        room_id='$room_id',
        booking_date='$booking_date',
        start_time='$start_time',
        end_time='$end_time',
        class_period='$class_period',
        semester='$semester',
        academic_year='$academic_year',
        use_date='$use_date',
        user_phone='$user_phone'
        WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: booking_list.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขการจองห้อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">
    <div class="container">
        <h2>แก้ไขการจองห้อง</h2>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">ผู้จอง</label>
                    <select name="user_id" class="form-select" required>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <option value="<?= $user['id'] ?>" <?= $user['id'] == $booking['user_id'] ? 'selected' : '' ?>>
                                <?= $user['firstname'] . ' ' . $user['lastname'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ห้อง</label>
                    <select name="room_id" class="form-select" required>
                        <?php while ($room = $rooms->fetch_assoc()): ?>
                            <option value="<?= $room['id'] ?>" <?= $room['id'] == $booking['room_id'] ? 'selected' : '' ?>>
                                <?= $room['room_name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันที่จอง</label>
                    <input type="date" name="booking_date" class="form-control" value="<?= $booking['booking_date'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">คาบเรียน</label>
                    <input type="text" name="class_period" class="form-control" value="<?= $booking['class_period'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ภาคเรียน</label>
                    <input type="text" name="semester" class="form-control" value="<?= $booking['semester'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ปีการศึกษา</label>
                    <input type="text" name="academic_year" class="form-control" value="<?= $booking['academic_year'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่ใช้ห้อง</label>
                    <input type="date" name="use_date" class="form-control" value="<?= $booking['use_date'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">เวลาเริ่ม</label>
                    <input type="time" name="start_time" class="form-control" value="<?= $booking['start_time'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">เวลาสิ้นสุด</label>
                    <input type="time" name="end_time" class="form-control" value="<?= $booking['end_time'] ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" name="user_phone" class="form-control" value="<?= $booking['user_phone'] ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success mt-4">บันทึก</button>
            <a href="booking_list.php" class="btn btn-secondary mt-4">ยกเลิก</a>
        </form>
    </div>
</body>
</html>