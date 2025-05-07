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

// รับค่าจากฟอร์ม
$room_id = $_POST['room_id'];
$subject_name = $_POST['subject_name'];
$teacher_name = $_POST['teacher_name'];
$day_of_week = $_POST['day_of_week'];
$period = $_POST['period'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$semester = $_POST['semester'];
$academic_year = $_POST['academic_year'];

// เพิ่มข้อมูลลงตาราง timetable
$sql = "INSERT INTO timetable (room_id, subject_name, teacher_name, day_of_week, period, start_time, end_time, semester, academic_year)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssissss", $room_id, $subject_name, $teacher_name, $day_of_week, $period, $start_time, $end_time, $semester, $academic_year);

if ($stmt->execute()) {
    header("Location: manage_timetable.php");
    exit();
} else {
    echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
}

$stmt->close();
$conn->close();
?>