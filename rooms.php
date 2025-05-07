<?php
session_start();
include("config.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// ค้นหาห้อง
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// ดึงข้อมูลห้อง
$sql = "SELECT * FROM rooms WHERE room_name LIKE :search OR building_number LIKE :search";
$stmt = $conn->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลห้อง | ระบบจองห้องเรียน</title>
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
        <a href="manage_booking.php"><i class="bi bi-journal-text"></i> รายการจองห้อง</a>
        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="users.php"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
            <a href="manage_rooms.php"><i class="bi bi-building"></i> จัดการข้อมูลห้อง</a>
            <a href="timetable.php"><i class="bi bi-table"></i> จัดการตารางสอน</a>
            <a href="manage_booking.php"><i class="bi bi-clipboard-data-fill"></i> จัดการการจองห้อง</a>
            
        <?php endif; ?>
        <a href="user_guide.php"><i class="bi bi-question-circle-fill"></i> คู่มือการใช้งาน</a>
        <a href="menu_update.php"><i class="bi bi-cloud-upload-fill"></i> อัปเดตข่าวสาร</a>
        <hr>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
    </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <h2>🏢 ข้อมูลห้องเรียน</h2>
        
        <!-- ฟอร์มค้นหาห้อง -->
        <form method="get" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="ค้นหาห้อง..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> ค้นหา</button>
            </div>
        </form>

        <!-- ตารางแสดงข้อมูลห้อง -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>หมายเลขอาคาร</th>
                    <th>ชื่อห้อง</th>
                    <th>รายละเอียดห้อง</th>
                    <th>ขนาดห้อง</th>
                    <th>สถานะ</th>
                    <th>ครุภัณฑ์</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rooms): ?>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['building_number']) ?></td>
                            <td><?= htmlspecialchars($room['room_name']) ?></td>
                            <td><?= htmlspecialchars($room['room_detail']) ?></td>
                            <td><?= htmlspecialchars($room['room_size']) ?></td>
                            <td><?= htmlspecialchars($room['status']) ?></td>
                            <td><?= htmlspecialchars($room['equipment']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">ไม่พบข้อมูลห้องที่ค้นหา 🧐</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>