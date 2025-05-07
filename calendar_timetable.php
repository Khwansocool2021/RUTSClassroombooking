<?php
// calendar_timetable.php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db = 'classroom_booking';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $db);
$conn->set_charset("utf8mb4");

$timetable = $conn->query("
    SELECT t.*, r.room_name
    FROM timetable t
    JOIN rooms r ON t.room_id = r.id
");

$events = [];
$day_map = [
    'จันทร์' => 1,
    'อังคาร' => 2,
    'พุธ' => 3,
    'พฤหัสบดี' => 4,
    'ศุกร์' => 5,
    'เสาร์' => 6,
    'อาทิตย์' => 7,
];

// แปลงข้อมูลให้อยู่ในรูปแบบ FullCalendar
while ($row = $timetable->fetch_assoc()) {
    $dow = $day_map[$row['day_of_week']] ?? null;
    if (!$dow) continue;

    $events[] = [
        'title' => $row['subject_name'] . " (" . $row['teacher_name'] . ") - ห้อง " . $row['room_name'],
        'daysOfWeek' => [$dow],
        'startTime' => $row['start_time'],
        'endTime' => $row['end_time'],
        'color' => '#0d6efd'
    ];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ตารางสอน (แบบปฏิทิน)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.min.js"></script>
</head>
<body class="bg-light p-4">
<div class="container">
    <h2 class="mb-4">📅 ตารางสอน (ปฏิทินรายสัปดาห์)</h2>
    <div id="calendar"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        locale: 'th',
        allDaySlot: false,
        slotMinTime: "07:00:00",
        slotMaxTime: "20:00:00",
        height: "auto",
        events: <?= json_encode($events) ?>
    });
    calendar.render();
});
</script>
</body>
</html>