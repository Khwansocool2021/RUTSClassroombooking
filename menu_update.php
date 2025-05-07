<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เมนูอัปเดตข้อมูล</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff;
        }
        .menu-card {
            transition: transform 0.2s;
        }
        .menu-card:hover {
            transform: scale(1.05);
        }
        .menu-icon {
            font-size: 48px;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4 text-center">🔧 เมนูอัปเดตข้อมูล</h3>
        <div class="row justify-content-center g-4">

            <!-- อัปเดตรูปหน้าปก -->
            <div class="col-md-4">
                <div class="card menu-card text-center p-4 shadow-sm">
                    <i class="bi bi-image menu-icon"></i>
                    <h5 class="mt-3">อัปเดตรูปหน้าปก</h5>
                    <a href="upload_cover.php" class="btn btn-primary mt-2">เข้าสู่เมนู</a>
                </div>
            </div>

            <!-- อัปเดตคลิป Video -->
            <div class="col-md-4">
                <div class="card menu-card text-center p-4 shadow-sm">
                    <i class="bi bi-camera-video menu-icon"></i>
                    <h5 class="mt-3">อัปเดตคลิป Video</h5>
                    <a href="upload_video.php" class="btn btn-primary mt-2">เข้าสู่เมนู</a>
                </div>
            </div>

            <!-- อัปเดตข้อมูลข่าวสาร -->
            <div class="col-md-4">
                <div class="card menu-card text-center p-4 shadow-sm">
                    <i class="bi bi-newspaper menu-icon"></i>
                    <h5 class="mt-3">อัปเดตข้อมูลข่าวสาร</h5>
                    <a href="upload_news.php" class="btn btn-primary mt-2">เข้าสู่เมนู</a>
                </div>
            </div>

        </div>

        <!-- ปุ่มย้อนกลับ -->
        <div class="text-center mt-5">
            <a href="home.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> ย้อนกลับ
            </a>
        </div>
    </div>
</body>
</html>