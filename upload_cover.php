<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $uploadDir = 'uploads/covers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['cover_image']['name']);
        $targetFile = $uploadDir . time() . '_' . $fileName;

        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetFile)) {
            // เชื่อมต่อฐานข้อมูล
            $conn = new mysqli("localhost", "root", "root", "classroom_booking");
            $conn->set_charset("utf8mb4");

            $title = $conn->real_escape_string($_POST['title']);
            $imagePath = $conn->real_escape_string($targetFile);

            $sql = "INSERT INTO cover_images (title, image_path) VALUES ('$title', '$imagePath')";
            $conn->query($sql);
            $conn->close();

            echo "<script>alert('อัปโหลดภาพหน้าปกสำเร็จ'); window.location='home.php';</script>";
        } else {
            echo "ไม่สามารถอัปโหลดไฟล์ได้";
        }
    } else {
        echo "กรุณาเลือกไฟล์ภาพ";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>อัปโหลดภาพหน้าปก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>📷 อัปโหลดภาพหน้าปก</h3>
    <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">ชื่อภาพ</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">เลือกภาพ</label>
        <input type="file" name="cover_image" accept="image/*" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">อัปโหลด</button>
    <a href="home.php" class="btn btn-secondary ">ย้อนกลับ</a>
</form>
</body>
</html>