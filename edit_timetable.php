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

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ไม่พบ ID ตารางสอน";
    exit();
}

// ดึงข้อมูลเดิม
$stmt = $conn->prepare("SELECT * FROM timetable WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$timetable = $result->fetch_assoc();

if (!$timetable) {
    echo "ไม่พบข้อมูลตารางสอน";
    exit();
}

// ดึงข้อมูลห้อง
$rooms = $conn->query("SELECT id, room_name FROM rooms");

// อัปเดตเมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $day_of_week = $_POST['day_of_week'];
    $period = $_POST['period'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $subject_name = $_POST['subject_name'];
    $teacher_name = $_POST['teacher_name'];

    $stmt = $conn->prepare("UPDATE timetable 
        SET room_id=?, semester=?, academic_year=?, day_of_week=?, period=?, start_time=?, end_time=?, subject_name=?, teacher_name=? 
        WHERE id=?");
    $stmt->bind_param("isssissssi", $room_id, $semester, $academic_year, $day_of_week, $period, $start_time, $end_time, $subject_name, $teacher_name, $id);

    if ($stmt->execute()) {
        header("Location: manage_timetable.php");
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขตารางสอน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>✏️ แก้ไขตารางสอน</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="room_id" class="form-label">ชื่อห้อง</label>
            <select name="room_id" class="form-select" required>
                <?php while ($room = $rooms->fetch_assoc()): ?>
                    <option value="<?= $room['id'] ?>" <?= $room['id'] == $timetable['room_id'] ? 'selected' : '' ?>>
                        <?= $room['room_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">ภาคเรียน</label>
            <input type="text" name="semester" class="form-control" value="<?= $timetable['semester'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ปีการศึกษา</label>
            <input type="text" name="academic_year" class="form-control" value="<?= $timetable['academic_year'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">วัน</label>
            <input type="text" name="day_of_week" class="form-control" value="<?= $timetable['day_of_week'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">คาบ</label>
            <input type="number" name="period" class="form-control" value="<?= $timetable['period'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">เวลาเริ่ม (HH:MM:SS)</label>
            <input type="time" name="start_time" class="form-control" value="<?= $timetable['start_time'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">เวลาสิ้นสุด (HH:MM:SS)</label>
            <input type="time" name="end_time" class="form-control" value="<?= $timetable['end_time'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ชื่อวิชา</label>
            <input type="text" name="subject_name" class="form-control" value="<?= $timetable['subject_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ชื่ออาจารย์</label>
            <input type="text" name="teacher_name" class="form-control" value="<?= $timetable['teacher_name'] ?>" required>
        </div>
        <button type="submit" class="btn btn-success">💾 บันทึกการแก้ไข</button>
        <a href="manage_timetable.php" class="btn btn-secondary">↩️ ย้อนกลับ</a>
    </form>
</body>
</html>

<?php $conn->close(); ?>