<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$isAdmin = $user['role'] === 'admin';

$host = 'localhost';
$db = 'classroom_booking';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// คำสั่ง SQL แสดงรายการจอง พร้อมค้นหาตามชื่อ, ห้อง หรือปีการศึกษา
$sql = "SELECT b.*, 
            CONCAT(u.firstname, ' ', u.lastname) AS booker_name, 
            r.room_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id
        WHERE 
            CONCAT(u.firstname, ' ', u.lastname) LIKE '%$search%' OR 
            r.room_name LIKE '%$search%' OR 
            b.academic_year LIKE '%$search%'
        ORDER BY b.use_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายการจองห้อง | ระบบจองห้องเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { display: flex; min-height: 100vh; margin: 0; }
        .sidebar {
            width: 250px; background-color: #007bff; color: white;
            padding: 1rem; position: fixed; height: 100%;
        }
        .sidebar a { color: white; display: block; margin: 1rem 0; text-decoration: none; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); border-radius: 5px; padding-left: 10px; }
        .content { margin-left: 250px; padding: 2rem; flex-grow: 1; background-color: #f8f9fa; }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-white">📘 เมนูหลัก</h4>
    <a href="home.php"><i class="bi bi-house-door-fill"></i> หน้าหลัก</a>
    <a href="rooms.php"><i class="bi bi-door-open-fill"></i> ข้อมูลห้อง</a>
    <a href="booking.php"><i class="bi bi-calendar-check-fill"></i> จองห้อง</a>
    <a href="booking_list.php"><i class="bi bi-journal-text"></i> รายการจองห้อง</a>
    <?php if ($isAdmin): ?>
        <a href="users.php"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
        <a href="manage_rooms.php"><i class="bi bi-building"></i> จัดการข้อมูลห้อง</a>
        <a href="manage_timetable.php"><i class="bi bi-table"></i> จัดการตารางสอน</a>
        <a href="manage_booking.php"><i class="bi bi-clipboard-data-fill"></i> จัดการการจองห้อง</a>

    <?php endif; ?>
    
    <a href="user_guide.php"><i class="bi bi-question-circle-fill"></i> คู่มือการใช้งาน</a>
    <hr>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
</div>

<div class="content">
    <h2><i class="bi bi-journal-text"></i> รายการจองห้อง</h2>
    <p class="mb-4">ดูรายการจองห้องทั้งหมด พร้อมค้นหาข้อมูล</p>

    <!-- ช่องค้นหา -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อผู้จอง / ห้อง / ปีการศึกษา..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> ค้นหา</button>
        </div>
    </form>

    <!-- ตารางแสดงข้อมูลการจอง -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr class="table-primary">
                <th>ชื่อผู้จอง</th>
                <th>ห้อง</th>
                <th>คาบที่จอง</th>
                <th>ปีการศึกษา</th>
                <th>วันที่ใช้ห้อง</th>
                <th>วันที่จอง</th>
                <th>ผู้ใช้ห้อง</th>
                <th>เบอร์โทร</th>
                <?php if ($isAdmin): ?>
                <th>การจัดการ</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['booker_name']) ?></td>
                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                        <td><?= htmlspecialchars($row['class_period']) ?></td>
                        <td><?= htmlspecialchars($row['academic_year']) ?> / <?= htmlspecialchars($row['semester']) ?></td>
                        <td><?= htmlspecialchars($row['use_date']) ?></td>
                        <td><?= htmlspecialchars($row['booking_created']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) == $user['id'] ? $user['firstname'] . ' ' . $user['lastname'] : 'ไม่ระบุ' ?></td>
                        <td><?= htmlspecialchars($row['user_phone']) ?></td>
                        <?php if ($isAdmin): ?>
                        <td>
                            <a href="edit_booking.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                            <a href="delete_booking.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบการจองนี้?')">ลบ</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="<?= $isAdmin ? '9' : '8' ?>" class="text-center">ไม่พบข้อมูล</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>