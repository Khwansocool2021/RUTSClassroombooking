<?php
session_start();
include("config.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: user_management.php");
    exit();
}

// โหลดข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit();
}

// อัปเดตข้อมูลผู้ใช้
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, position = :position,
        department = :department, address = :address, phone = :phone, role = :role WHERE id = :id");
    $stmt->execute([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'position' => $position,
        'department' => $department,
        'address' => $address,
        'phone' => $phone,
        'role' => $role,
        'id' => $id
    ]);

    echo "<script>alert('อัปเดตข้อมูลผู้ใช้สำเร็จ'); window.location.href = 'users.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>แก้ไขข้อมูลผู้ใช้</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">ชื่อ</label>
            <input type="text" class="form-control" name="firstname" value="<?= $user['firstname'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">นามสกุล</label>
            <input type="text" class="form-control" name="lastname" value="<?= $user['lastname'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ตำแหน่ง</label>
            <input type="text" class="form-control" name="position" value="<?= $user['position'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">สาขา</label>
            <input type="text" class="form-control" name="department" value="<?= $user['department'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ที่อยู่</label>
            <textarea class="form-control" name="address" required><?= $user['address'] ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control" name="phone" value="<?= $user['phone'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">บทบาท</label>
            <select class="form-select" name="role" required>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="teacher" <?= $user['role'] == 'teacher' ? 'selected' : '' ?>>Teacher</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
        <a href="users.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
</body>
</html>