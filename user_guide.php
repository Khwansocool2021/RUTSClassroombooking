<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>คู่มือการใช้งานระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f8fb;
        }
        .guide-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
        }
        .search-input {
            max-width: 400px;
        }
        .guide-title {
            color: #0d6efd;
        }
        .highlight {
            background-color: #fffbcc;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-primary mb-4"><i class="bi bi-book"></i> คู่มือการใช้งานระบบจองห้องเรียน</h2>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" id="searchBox" class="form-control search-input" placeholder="🔍 ค้นหาหัวข้อหรือคำสำคัญ..." onkeyup="searchGuide()">
    </div>

    <!-- Guide Content -->
    <div class="guide-section" id="guideContent">
        <h4 class="guide-title">👤 การเข้าสู่ระบบ</h4>
        <p>ผู้ใช้สามารถเข้าสู่ระบบด้วยชื่อผู้ใช้และรหัสผ่านที่ได้รับจากผู้ดูแลระบบ</p>

        <h4 class="guide-title">📋 หน้าหลัก</h4>
        <p>แสดงข่าวสารทั่วไป พร้อมเมนูนำทางไปยังหน้าอื่น ๆ เช่น ข้อมูลห้อง และการจองห้อง</p>

        <h4 class="guide-title">🏫 ข้อมูลห้องเรียน</h4>
        <p>สามารถดูรายละเอียดของห้องเรียนแต่ละห้อง เช่น อาคาร หมายเลขห้อง ขนาด และครุภัณฑ์</p>

        <h4 class="guide-title">📝 การจองห้อง</h4>
        <p>ผู้ใช้งานสามารถเลือกห้อง วันที่ และช่วงเวลาเพื่อทำการจองห้องเรียนได้ผ่านแบบฟอร์ม</p>

        <h4 class="guide-title">🗓️ ตารางสอน</h4>
        <p>แสดงตารางสอนทั้งหมดในรูปแบบปฏิทิน โดยสามารถดูตามวันและเวลาได้</p>

        <h4 class="guide-title">🔧 จัดการข้อมูล (สำหรับผู้ดูแลระบบ)</h4>
        <ul>
            <li>จัดการผู้ใช้งาน</li>
            <li>จัดการข้อมูลห้อง</li>
            <li>จัดการตารางสอน</li>
            <li>จัดการรายการจอง</li>
        </ul>

        <h4 class="guide-title">📞 ติดต่อเจ้าหน้าที่</h4>
        <p>หากพบปัญหาเกี่ยวกับระบบ กรุณาติดต่อผู้ดูแลระบบที่เบอร์โทรศัพท์หรืออีเมลที่ให้ไว้</p>
    </div>

    <a href="home.php" class="btn btn-secondary mt-4">⬅️ กลับหน้าหลัก</a>
</div>

<!-- Bootstrap & Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script>
    function searchGuide() {
        let input = document.getElementById("searchBox").value.toLowerCase();
        let content = document.getElementById("guideContent");
        let sections = content.getElementsByTagName("p");
        let titles = content.getElementsByTagName("h4");

        for (let i = 0; i < sections.length; i++) {
            sections[i].classList.remove("highlight");
            sections[i].style.display = "block";
        }

        for (let i = 0; i < titles.length; i++) {
            titles[i].style.display = "block";
        }

        if (input.trim() !== "") {
            for (let i = 0; i < sections.length; i++) {
                let text = sections[i].textContent.toLowerCase();
                if (!text.includes(input)) {
                    sections[i].style.display = "none";
                    if (titles[i]) titles[i].style.display = "none";
                } else {
                    sections[i].classList.add("highlight");
                }
            }
        }
    }
</script>
</body>
</html>