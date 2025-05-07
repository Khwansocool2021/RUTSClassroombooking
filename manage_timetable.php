<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];

$host = 'localhost';
$db = 'classroom_booking';
$username = 'root';
$password = 'root';

$conn = new mysqli($host, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rooms = $conn->query("SELECT * FROM rooms");

// เพิ่มตัวแปรสำหรับการค้นหา
$search = isset($_GET['search']) ? $_GET['search'] : '';

// ดึงข้อมูลตารางสอน
$sql = "SELECT t.*, r.room_name FROM timetable t 
        JOIN rooms r ON t.room_id = r.id 
        WHERE 
            t.subject_name LIKE '%$search%' OR 
            r.room_name LIKE '%$search%' OR 
            t.teacher_name LIKE '%$search%' OR 
            t.day_of_week LIKE '%$search%' OR 
            t.semester LIKE '%$search%' OR 
            t.academic_year LIKE '%$search%'
        ORDER BY day_of_week, period";
$timetables = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการตารางสอน | ระบบจองห้องเรียน</title>
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
    <a href="users.php"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
    <a href="manage_rooms.php"><i class="bi bi-building"></i> จัดการข้อมูลห้อง</a>
    <a href="manage_timetable.php"><i class="bi bi-table"></i> จัดการตารางสอน</a>
    <a href="manage_booking.php"><i class="bi bi-clipboard-data-fill"></i> จัดการการจองห้อง</a>
    
    <a href="user_guide.php"><i class="bi bi-question-circle-fill"></i> คู่มือการใช้งาน</a>
    <hr>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
</div>

<div class="content">
    <h2><i class="bi bi-table"></i> จัดการตารางสอน</h2>
    <p class="mb-4">เพิ่ม แก้ไข หรือลบตารางการเรียนการสอนแต่ละห้อง</p>

    <!-- ฟอร์มเพิ่มตาราง -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>เพิ่มตารางสอนใหม่</h5>
        </div>
        <div class="card-body">
            <form action="add_timetable.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">เลือกห้อง</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- เลือกห้อง --</option>
                            <?php while ($room = $rooms->fetch_assoc()): ?>
                                <option value="<?= $room['id'] ?>"><?= $room['room_name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ชื่อวิชา</label>
                        <input type="text" name="subject_name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ชื่ออาจารย์</label>
                        <input type="text" name="teacher_name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">วัน</label>
                        <select name="day_of_week" class="form-select" required>
                            <option value="จันทร์">จันทร์</option>
                            <option value="อังคาร">อังคาร</option>
                            <option value="พุธ">พุธ</option>
                            <option value="พฤหัสบดี">พฤหัสบดี</option>
                            <option value="ศุกร์">ศุกร์</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">คาบเรียน</label>
                        <input type="number" name="period" class="form-control" placeholder="ตัวเลขคาบเรียน" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">เวลาเริ่มต้น</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">เวลาสิ้นสุด</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ภาคเรียน</label>
                        <input type="text" name="semester" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ปีการศึกษา</label>
                        <input type="text" name="academic_year" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">เพิ่มตารางสอน</button>
            </form>
        </div>
    </div>

    <!-- ช่องค้นหา -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อวิชา / ห้อง..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> ค้นหา</button>
        </div>
    </form>

    <!-- ตารางแสดงตารางสอน -->
    <h4>ตารางสอนทั้งหมด</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ห้อง</th>
                <th>วิชา</th>
                <th>อาจารย์</th>
                <th>วัน</th>
                <th>คาบ</th>
                <th>เวลาเริ่ม</th>
                <th>เวลาสิ้นสุด</th>
                <th>ภาคเรียน</th>
                <th>ปีการศึกษา</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $timetables->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['room_name']) ?></td>
                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                    <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                    <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                    <td><?= htmlspecialchars($row['period']) ?></td>
                    <td><?= htmlspecialchars($row['start_time']) ?></td>
                    <td><?= htmlspecialchars($row['end_time']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                    <td><?= htmlspecialchars($row['academic_year']) ?></td>
                    <td>
                        <a href="edit_timetable.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                        <a href="delete_timetable.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบตารางสอนนี้?')">ลบ</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>