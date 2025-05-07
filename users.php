<?php
session_start();
include("config.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// เพิ่มผู้ใช้ใหม่
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (firstname, lastname, position, department, address, phone, username, password, role) 
            VALUES (:firstname, :lastname, :position, :department, :address, :phone, :username, :password, :role)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'position' => $position,
        'department' => $department,
        'address' => $address,
        'phone' => $phone,
        'username' => $username,
        'password' => $password,
        'role' => $role
    ]);

    echo "<script>alert('เพิ่มผู้ใช้ใหม่สำเร็จ'); window.location.href = 'users.php';</script>";
}

// ค้นหาผู้ใช้
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM users WHERE firstname LIKE :search OR lastname LIKE :search";
$stmt = $conn->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: url('img/bg-user.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Prompt', sans-serif;
        }
        .main-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.3);
            margin-top: 40px;
        }
        h2 {
            color: #007bff;
            font-weight: bold;
        }
        .btn-back {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .modal-content {
            background-color: #f0f8ff;
            border-radius: 15px;
        }
        .modal-title {
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="main-box">
        <!-- ปุ่มย้อนกลับ -->
        <a href="home.php" class="btn btn-primary btn-back">
            <i class="bi bi-arrow-left-circle"></i> กลับสู่หน้าหลัก
        </a>

        <h2><i class="bi bi-people-fill"></i> จัดการผู้ใช้</h2>

        <!-- ฟอร์มค้นหา -->
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-10">
                <input type="text" class="form-control" name="search" placeholder="🔍 ค้นหาผู้ใช้..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> ค้นหา</button>
            </div>
        </form>

        <!-- ตาราง -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>#</th>
                        <th>ชื่อ</th>
                        <th>ตำแหน่ง</th>
                        <th>สาขา</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>บทบาท</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="text-center"><?= $user['id'] ?></td>
                        <td><?= $user['firstname'] . ' ' . $user['lastname'] ?></td>
                        <td><?= $user['position'] ?></td>
                        <td><?= $user['department'] ?></td>
                        <td><?= $user['phone'] ?></td>
                        <td class="text-center"><?= $user['role'] === 'admin' ? '👑 Admin' : '👨‍🏫 Teacher' ?></td>
                        <td class="text-center">
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบผู้ใช้นี้?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ปุ่มเพิ่มผู้ใช้ -->
        <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้ใหม่
        </button>

        <!-- Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel"><i class="bi bi-person-plus-fill"></i> เพิ่มผู้ใช้ใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อ</label>
                                    <input type="text" name="firstname" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุล</label>
                                    <input type="text" name="lastname" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ตำแหน่ง</label>
                                    <input type="text" name="position" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">สาขา</label>
                                    <input type="text" name="department" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">ที่อยู่</label>
                                    <textarea name="address" class="form-control" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อผู้ใช้</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">รหัสผ่าน</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">บทบาท</label>
                                    <select name="role" class="form-select" required>
                                        <option value="admin">Admin</option>
                                        <option value="teacher">Teacher</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary" name="add_user">
                                    <i class="bi bi-check-circle-fill"></i> บันทึก
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>