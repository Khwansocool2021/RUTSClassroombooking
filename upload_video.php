<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "root", "classroom_booking");
$conn->set_charset("utf8mb4");

// กำหนด ID เป็น 1 โดยตรง
$id = 1;

// ถ้ามีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vdo_ex = $conn->real_escape_string($_POST['vdo_ex']);

    $sql = "UPDATE videos SET vdo_ex = '$vdo_ex' WHERE id = $id";
    if ($conn->query($sql)) {
        echo "<script>alert('อัปเดตรหัสวิดีโอ YouTube สำเร็จ'); window.location='home.php';</script>";
        exit;
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

// ดึงข้อมูลปัจจุบันของ ID 1
$result = $conn->query("SELECT * FROM videos WHERE id = $id");
if ($result->num_rows !== 1) {
    echo "ไม่พบข้อมูลวิดีโอที่มี ID = 1";
    exit;
}
$video = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขวิดีโอ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>✏️ แก้ไขวิดีโอ</h3>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">รหัสวิดีโอ YouTube</label>
            <input type="text" name="vdo_ex" class="form-control" value="<?= htmlspecialchars($video['vdo_ex']) ?>" required>
        </div>
        <button type="submit" class="btn btn-warning">อัปเดต</button>
        <a href="home.php" class="btn btn-secondary">ย้อนกลับ</a>
    </form>
</body>
</html>