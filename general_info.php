<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการข้อมูลทั่วไป | ระบบจองห้องเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #007bff;
            color: white;
            padding: 1rem;
            position: fixed;
            height: 100%;
        }
        .sidebar a {
            color: white;
            display: block;
            margin: 1rem 0;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            padding-left: 10px;
        }
        .content {
            margin-left: 250px;
            padding: 2rem;
            flex-grow: 1;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

    <!-- เมนูด้านซ้าย -->
    <div class="sidebar">
        <h4 class="text-white">📘 เมนูหลัก</h4>
        <a href="home.php"><i class="bi bi-house-door-fill"></i> หน้าหลัก</a>
        <a href="rooms.php"><i class="bi bi-door-open-fill"></i> ข้อมูลห้อง</a>
        <a href="booking.php"><i class="bi bi-calendar-check-fill"></i> จองห้อง</a>
        <?php if ($user['role'] == 'admin'): ?>
            <a href="users.php"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
            <a href="manage_rooms.php"><i class="bi bi-building"></i> จัดการข้อมูลห้อง</a>
            <a href="timetable.php"><i class="bi bi-table"></i> จัดการตารางสอน</a>
            <a href="manage_bookings.php"><i class="bi bi-clipboard-data-fill"></i> จัดการการจองห้อง</a>
            <a href="general_info.php"><i class="bi bi-gear-fill"></i> จัดการข้อมูลทั่วไป</a>
            <a href="booking_list.php"><i class="bi bi-journal-text"></i> รายการจองห้อง</a>
        <?php endif; ?>
        <a href="guide.php"><i class="bi bi-question-circle-fill"></i> คู่มือการใช้งาน</a>
        <hr>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
    </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <h2><i class="bi bi-gear-fill"></i> จัดการข้อมูลทั่วไป</h2>
        <p class="mb-4">หน้านี้สำหรับจัดการข้อมูลระบบพื้นฐาน เช่น ปีการศึกษา ประเภทห้อง ข้อมูลคงที่ต่าง ๆ</p>

        <!-- พื้นที่สำหรับตารางหรือแบบฟอร์ม -->
        <div class="alert alert-warning">
            🔧 ยังไม่มีเนื้อหาจริง โปรดเพิ่มฟังก์ชันจัดการข้อมูลในภายหลัง
        </div>
    </div>

</body>
</html>