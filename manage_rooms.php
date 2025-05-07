<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];

// เชื่อมต่อฐานข้อมูล
$host = 'localhost';
$db = 'classroom_booking';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการค้นหา
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM rooms";
if (!empty($search)) {
    $sql .= " WHERE 
        building_number LIKE '%$search%' OR
        room_name LIKE '%$search%' OR
        room_detail LIKE '%$search%' OR
        equipment LIKE '%$search%'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการข้อมูลห้อง | ระบบจองห้องเรียน</title>
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

<div class="sidebar">
    <h4 class="text-white">📘 เมนูหลัก</h4>
    <a href="home.php"><i class="bi bi-house-door-fill"></i> หน้าหลัก</a>
    <a href="rooms.php"><i class="bi bi-door-open-fill"></i> ข้อมูลห้อง</a>
    <a href="booking.php"><i class="bi bi-calendar-check-fill"></i> จองห้อง</a>
    <a href="booking_list.php"><i class="bi bi-journal-text"></i> รายการจองห้อง</a>
    <?php if ($user['role'] == 'admin'): ?>
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
    <h2><i class="bi bi-building"></i> จัดการข้อมูลห้อง</h2>
    <p class="mb-4">หน้านี้สำหรับเพิ่ม แก้ไข หรือ ลบข้อมูลห้องเรียน</p>

    <!-- ฟอร์มเพิ่มห้อง -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>เพิ่มห้องใหม่</h5>
        </div>
        <div class="card-body">
            <form action="add_room.php" method="POST">
                <div class="mb-3">
                    <label for="building_number" class="form-label">หมายเลขอาคาร</label>
                    <input type="text" class="form-control" id="building_number" name="building_number" required>
                </div>
                <div class="mb-3">
                    <label for="room_name" class="form-label">ชื่อ/หมายเลขห้อง</label>
                    <input type="text" class="form-control" id="room_name" name="room_name" required>
                </div>
                <div class="mb-3">
                    <label for="room_detail" class="form-label">รายละเอียดห้อง</label>
                    <input type="text" class="form-control" id="room_detail" name="room_detail" required>
                </div>
                <div class="mb-3">
                    <label for="room_size" class="form-label">ขนาดห้อง (จำนวนที่นั่ง)</label>
                    <input type="number" class="form-control" id="room_size" name="room_size" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">สถานะ</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="ว่าง">ว่าง</option>
                        <option value="ไม่ว่าง">ไม่ว่าง</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="equipment" class="form-label">ครุภัณฑ์ รายละเอียด</label>
                    <input type="text" class="form-control" id="equipment" name="equipment" required>
                </div>
                <button type="submit" class="btn btn-primary">เพิ่มห้อง</button>
            </form>
        </div>
    </div>

    <!-- ช่องค้นหา -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="ค้นหาห้องตามชื่อ/อาคาร/รายละเอียด/ครุภัณฑ์" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> ค้นหา</button>
        </div>
    </form>

    <!-- ตารางข้อมูลห้อง -->
    <h4>ข้อมูลห้องทั้งหมด</h4>
    <table class="table table-bordered mt-4">
        <thead class="table-primary">
            <tr>
                <th>หมายเลขอาคาร</th>
                <th>ชื่อ/หมายเลขห้อง</th>
                <th>รายละเอียดห้อง</th>
                <th>ขนาด</th>
                <th>สถานะ</th>
                <th>ครุภัณฑ์ รายละเอียด</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['building_number']) ?></td>
                        <td><?= htmlspecialchars($row['room_name']) ?></td>
                        <td><?= htmlspecialchars($row['room_detail']) ?></td>
                        <td><?= htmlspecialchars($row['room_size']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['equipment']) ?></td>
                        <td>
                            <a href="edit_room.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                            <a href="delete_room.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบห้องนี้?')">ลบ</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">ไม่พบข้อมูลห้อง</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>