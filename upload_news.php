<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $uploadDir = 'uploads/news/';
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

            $sql = "INSERT INTO news (title, image_path) VALUES ('$title', '$imagePath')";
            if ($conn->query($sql)) {
                echo "<script>alert('อัปโหลดข่าวสารสำเร็จ'); window.location='home.php';</script>";
            } else {
                echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
            }
            $conn->close();
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
    <title>อัปโหลดข่าวสาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>📰 อัปโหลดข่าวสาร</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">หัวข้อข่าว</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">เลือกภาพข่าว</label>
            <input type="file" name="cover_image" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">อัปโหลด</button>
        <a href="home.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</body>
</html>