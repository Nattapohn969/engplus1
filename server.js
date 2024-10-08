const express = require('express');
const multer = require('multer');
const path = require('path');

const app = express();
const upload = multer({ dest: 'uploads/' }); // ระบุที่เก็บไฟล์อัปโหลด

app.post('/upload', upload.single('upload'), (req, res) => {
    if (!req.file) {
        return res.status(400).json({ uploaded: false });
    }

    // สร้าง URL ที่จะส่งกลับ
    const fileUrl = `https://your-server.com/uploads/${req.file.filename}`; // เปลี่ยน URL ให้ตรงกับเซิร์ฟเวอร์ของคุณ
    res.json({
        uploaded: true,
        url: fileUrl // ส่งกลับ URL ของไฟล์ที่อัปโหลด
    });
});

// ให้เข้าถึงไฟล์ที่อัปโหลดได้
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

app.listen(3000, () => {
    console.log('Server is running on port 3000');
});
