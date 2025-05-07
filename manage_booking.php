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
$conn->set_charset("utf8mb4");

$search = $_GET['search'] ?? '';

$sql = "
    SELECT b.*, 
           u.firstname, u.lastname, 
           r.room_name, r.building_number
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    WHERE u.firstname LIKE ? 
       OR u.lastname LIKE ?
       OR r.room_name LIKE ?
       OR r.building_number LIKE ?
    ORDER BY b.use_date DESC
";

$stmt = $conn->prepare($sql);
$param = "%" . $search . "%";
$stmt->bind_param("ssss", $param, $param, $param, $param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการการจองห้อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-wrapper {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-primary"><i class="bi bi-calendar-check"></i> จัดการการจองห้อง</h2>

        <form method="GET" class="mb-3 row g-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยชื่อผู้จอง / ห้อง" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
                
            </div>
        </form>

        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>ผู้จอง</th>
                            <th>ห้อง</th>
                            <th>วันใช้งาน</th>
                            <th>คาบ</th>
                            <th>เวลา</th>
                            <th>ปีการศึกษา</th>
                            <th>โทรศัพท์</th>
                            <th>ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                                    <td><?= "อาคาร " . htmlspecialchars($row['building_number']) . " - " . htmlspecialchars($row['room_name']) ?></td>
                                    <td class="text-center"><?= date("d/m/Y", strtotime($row['use_date'])) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['class_period']) ?></td>
                                    <td class="text-center"><?= substr($row['start_time'], 0, 5) ?> - <?= substr($row['end_time'], 0, 5) ?></td>
                                    <td class="text-center"><?= $row['semester'] ?>/<?= $row['academic_year'] ?></td>
                                    <td class="text-center"><?= htmlspecialchars($row['user_phone']) ?></td>
                                    <td class="text-center">
                                        <a href="edit_booking.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                        <a href="delete_booking.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบรายการจองนี้หรือไม่?');">ลบ</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted">ไม่พบข้อมูลการจอง</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="home.php" class="btn btn-secondary mt-3">⬅️ กลับหน้าหลัก</a>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>