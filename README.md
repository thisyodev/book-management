# Book Management

ระบบจัดการหนังสือ (Laravel 12) รองรับทั้ง
- หน้าเว็บสำหรับผู้ใช้ (`/book`)
- REST API สำหรับ Auth + Books (`/api/...`)
- เอกสาร API ด้วย Swagger (L5 Swagger)

## Tech Stack

- PHP 8.2+
- Laravel 12
- JWT Auth (`tymon/jwt-auth`)
- Swagger (`darkaonline/l5-swagger`)
- Vite + Tailwind + Bootstrap

## เริ่มต้นใช้งาน (Local Setup)

1) Clone และติดตั้ง dependency

```bash
composer install
npm install
```

2) ตั้งค่า environment

```bash
cp .env.example .env
php artisan key:generate
```

3) ตั้งค่าฐานข้อมูลใน `.env` แล้ว migrate

```bash
php artisan migrate
```

4) (ไม่บังคับ) seed ข้อมูลตัวอย่าง

```bash
php artisan db:seed
```

## การรันโปรเจกต์

รัน backend:

```bash
php artisan serve
```

รัน frontend assets (ระหว่างพัฒนา):

```bash
npm run dev
```

หรือ build สำหรับใช้งานจริง:

```bash
npm run build
```

## เส้นทางหลักของระบบ

- หน้าเว็บ: `/book`
- Login หน้าเว็บ: `/login`
- Register หน้าเว็บ: `/register`

### API Auth

- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (ต้องมี token)
- `GET /api/me` (ต้องมี token)

### API Books

- `GET /api/books`
- `GET /api/books/{id}`
- `POST /api/books` (ต้องมี token)
- `PUT /api/books/{id}` (ต้องมี token)
- `DELETE /api/books/{id}` (ต้องมี token)

## วิธี Generate เอกสาร API (Swagger)

โปรเจกต์ใช้ annotation ในโค้ดภายใต้โฟลเดอร์ `app/` เพื่อ generate เอกสาร

1) สร้างไฟล์ docs

```bash
php artisan l5-swagger:generate
```

2) เปิดหน้า Swagger UI

```text
/api/documentation
```

3) ไฟล์เอกสารที่ถูก generate จะอยู่ที่

```text
storage/api-docs/api-docs.json
```

หมายเหตุ:
- ถ้าแก้ annotation แล้วหน้า Swagger ยังไม่อัปเดต ให้รัน `php artisan l5-swagger:generate` ซ้ำ
- หากมี cache config ให้เคลียร์ด้วย `php artisan config:clear`

## Logging

ระบบแยก log รายวันออกเป็น 2 ไฟล์

- Success log: `storage/logs/success-YYYY-MM-DD.log`
- Error log: `storage/logs/error-YYYY-MM-DD.log`

กำหนดจำนวนวันที่เก็บได้ด้วยค่า

```env
LOG_DAILY_DAYS=14
```

## คำสั่งที่ใช้บ่อย

```bash
php artisan config:clear
php artisan route:list
php artisan test
```
