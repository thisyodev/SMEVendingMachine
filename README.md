# Vending Machine Project

ระบบจำลองตู้ขายสินค้าอัตโนมัติ (Vending Machine) ที่สามารถหยอดเงิน เลือกซื้อสินค้า และรับเงินทอนได้

---

## 🔧 Tech Stack

- Backend: Laravel 12 (PHP)
- Frontend: React.js + Tailwind CSS
- Database: MySQL

---

## 📦 ฟีเจอร์หลัก

- หยอดเหรียญ/ธนบัตร (1, 5, 10, 20, 50, 100, 500, 1000 บาท)
- เลือกซื้อสินค้าที่มีใน stock
- ตรวจสอบเงินคงเหลือและยอดที่สามารถซื้อได้
- ตรวจสอบและทอนเงินอัตโนมัติหากสามารถทำได้
- อัปเดต stock สินค้าและจำนวนเงินในเครื่อง
- ประวัติการซื้อแสดงบนหน้า UI

---

## 🐳 ใช้งานด้วย Docker Compose

โปรเจกต์นี้ใช้ Docker Compose สำหรับรัน backend, frontend, database และ phpMyAdmin

---

## 🛠️ การตั้งค่าและใช้งานฐานข้อมูล

หลังจากรัน Docker Compose และ migrate database แล้ว ให้รันคำสั่ง seed ข้อมูลเริ่มต้นด้วยคำสั่งนี้ใน container backend:

```bash
docker-compose exec backend php artisan migrate:fresh --seed
