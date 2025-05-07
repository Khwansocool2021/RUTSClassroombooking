<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หน้าหลัก | ระบบจองห้องเรียน</title>
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
            overflow-y: auto;
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
        .logo-top {
            max-width: 100%;
            margin-bottom: 1rem;
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
        <?php if ($user['role'] == 'admin'): ?>
            <a href="users.php"><i class="bi bi-people-fill"></i> จัดการผู้ใช้</a>
            <a href="manage_rooms.php"><i class="bi bi-building"></i> จัดการข้อมูลห้อง</a>
            <a href="manage_timetable.php"><i class="bi bi-table"></i> จัดการตารางสอน</a>
            <a href="manage_booking.php"><i class="bi bi-clipboard-data-fill"></i> จัดการการจองห้อง</a>
        <?php endif; ?>
        <a href="user_guide.php"><i class="bi bi-question-circle-fill"></i> คู่มือการใช้งาน</a>
        <a href="menu_update.php"><i class="bi bi-cloud-upload-fill"></i> อัปเดตข่าวสาร</a>
        <hr>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
    </div>

    <!-- เนื้อหาหลัก -->
    <div class="content">
    <?php
    $conn = new mysqli("localhost", "root", "root", "classroom_booking");
    $conn->set_charset("utf8mb4");

    // ดึงภาพหน้าปก
    $result = $conn->query("SELECT image_path FROM cover_images ORDER BY uploaded_at DESC LIMIT 1");
    $row = $result->fetch_assoc();
    $coverPath = $row ? $row['image_path'] : 'default_cover.jpg';

    // ดึงวิดีโอ YouTube ล่าสุด
    $videoRes = $conn->query("SELECT vdo_ex FROM videos ORDER BY uploaded_at DESC LIMIT 1");
    $videoRow = $videoRes->fetch_assoc();
    $videoId = $videoRow ? $videoRow['vdo_ex'] : null;

    // ดึงภาพข่าวสารล่าสุดจาก news 
    $newsRes = $conn->query("SELECT image_path FROM news ORDER BY uploaded_at DESC LIMIT 1");
    $newsRow = $newsRes->fetch_assoc();
    $newsImagePath = $newsRow ? $newsRow['image_path'] : 'default_news.jpg';

    $conn->close();
    ?>

<img src="<?= htmlspecialchars($coverPath) ?>"
     alt="รูปหน้าปก"
     class="img-fluid w-100"
     style="height: auto; max-height: 250px; object-fit: cover;">
    <br><br>

    <h2>👋 ยินดีต้อนรับคุณ <?= htmlspecialchars($user['firstname']) ?> <?= htmlspecialchars($user['lastname']) ?></h2>
    <p>นี่คือระบบจองห้องเรียนสำหรับอาจารย์คณะบริหารธุรกิจ มหาวิทยาลัยเทคโนโลยีราชมงคลศรีวิชัย รองรับการจองห้องเรียน ค้นหาห้องเรียน และจัดการข้อมูลต่าง ๆ</p>

    <div class="alert alert-info mt-4">
        📅 อย่าลืมตรวจสอบห้องว่างก่อนทำการจอง!
    </div>

    <?php if ($videoId): ?>
    <div class="row">
        <!-- วิดีโอ YouTube ซ้าย -->
        <div class="row">
    <!-- วิดีโอ YouTube ซ้าย -->
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <div class="card shadow-sm">
            <div style="width: 100%; height: 390px; overflow: hidden; background-color: #000;">
                <iframe src="https://www.youtube.com/embed/<?= htmlspecialchars($videoId) ?>"
                        style="width: 100%; height: 100%; object-fit: cover;"
                        frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- รูปภาพข่าวสาร ขวา -->
    <div class="col-12 col-md-6">
        <div class="card shadow-sm">
        <img src="<?= htmlspecialchars($newsImagePath) ?>" 
     class="img-fluid w-100 d-block"
     style="height: 390px; object-fit: cover; cursor: zoom-in;" 
     alt="ภาพข่าวสารล่าสุด" 
     data-bs-toggle="modal" 
     data-bs-target="#imageModal" />
        </div>
    </div>
</div>
    </div>
    <?php endif; ?>
    </div>

<!-- Modal สำหรับแสดงภาพขยาย -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0">
        <img id="modalImage" src="" class="img-fluid w-100 rounded shadow" alt="ภาพขยาย">
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const img = document.querySelector('[data-bs-target="#imageModal"]');
    const modalImg = document.getElementById('modalImage');

    if (img && modalImg) {
      img.addEventListener('click', function () {
        modalImg.src = this.src;
      });
    }
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>