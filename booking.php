<?php
session_start();
include("config.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// กำหนดตัวแปรปีการศึกษาและเทอม
$term = '';
$year = '';
$rooms = [];

// ตรวจสอบและรับค่าจากฟอร์ม
if (isset($_POST['term']) && isset($_POST['year'])) {
    $term = $_POST['term'];
    $year = $_POST['year'];

    // ตรวจสอบว่า term และ year มีค่า
    if (empty($term) || empty($year)) {
        die("กรุณากรอกปีการศึกษาและเทอมให้ครบถ้วน");
    }

    // ตรวจสอบให้แน่ใจว่า term เป็น 1 หรือ 2 และ year เป็นปีการศึกษาที่ถูกต้อง
    if (!in_array($term, [1, 2])) {
        die("เทอมไม่ถูกต้อง");
    }
    if (!preg_match("/^\d{4}$/", $year)) {
        die("ปีการศึกษาต้องเป็นเลข 4 หลัก");
    }

    // สร้าง booking_date ให้ถูกต้อง
    $booking_date = $year . "-" . ($term == 1 ? "06" : "11") . "-01";  // ใช้วันที่ "01" ของเดือนมิถุนายน (เทอม 1) หรือ พฤศจิกายน (เทอม 2)

    // ดึงข้อมูลห้องที่ยังไม่ถูกจอง
    $sql = "SELECT * FROM rooms WHERE id NOT IN (SELECT room_id FROM bookings WHERE booking_date = :booking_date)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['booking_date' => $booking_date]);  // ส่งวันที่ที่เราสร้างให้กับ SQL query
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จองห้อง | ระบบจองห้องเรียน</title>
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
        <a href="booking_list.php"><i class="bi bi-journal-text"></i> รายการจองห้อง</a>
        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="users.php"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
            <a href="manage_rooms.php"><i class="bi bi-building"></i> จัดการข้อมูลห้อง</a>
            <a href="timetable.php"><i class="bi bi-table"></i> จัดการตารางสอน</a>
            <a href="manage_booking.php"><i class="bi bi-clipboard-data-fill"></i> จัดการการจองห้อง</a>
            
        <?php endif; ?>
        <a href="user_guide.php"><i class="bi bi-question-circle-fill"></i> คู่มือการใช้งาน</a>
        <hr>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
    </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
        <h2>📅 จองห้องเรียน</h2>

        <!-- ฟอร์มเลือกปีการศึกษาและเทอม -->
        <form method="post" class="mb-4">
            <div class="row">
                <div class="col">
                    <input type="number" class="form-control" name="year" placeholder="ปีการศึกษา" required>
                </div>
                <div class="col">
                    <select class="form-control" name="term" required>
                        <option value="1">เทอม 1</option>
                        <option value="2">เทอม 2</option>
                    </select>
                </div>
                <div class="col">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> ค้นหาห้อง</button>
                </div>
            </div>
        </form>

        <!-- ตารางแสดงห้องที่ว่าง -->
        <h4>ห้องที่ว่างในเทอม <?= $term ?> ปีการศึกษา <?= $year ?></h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>หมายเลขอาคาร</th>
                    <th>ชื่อห้อง</th>
                    <th>รายละเอียดห้อง</th>
                    <th>ขนาดห้อง</th>
                    <th>สถานะ</th>
                    <th>ครุภัณฑ์</th>
                    <th>จอง</th>
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
                            <td>
                                <a href="confirm_booking.php?room_id=<?= $room['id'] ?>&term=<?= $term ?>&year=<?= $year ?>" class="btn btn-success">จอง</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">ไม่พบห้องว่างในวันที่เลือก 🧐</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>