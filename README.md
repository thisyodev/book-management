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

## เริ่มต้นใช้งาน (Local Setup)

1) Clone และติดตั้ง dependency

```bash
composer install
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

## Deploy บน Render (Docker) + Supabase

โปรเจกต์นี้เตรียมไฟล์ deploy ไว้แล้ว:
- `Dockerfile`
- `render.yaml`
- `docker/apache-start.sh`

### ขั้นตอน

1) Push โค้ดขึ้น GitHub

2) ไปที่ Render Dashboard > New > Blueprint
- เลือก repo ของโปรเจกต์นี้
- Render จะอ่านค่าจาก `render.yaml`

3) ตั้งค่า Environment Variables ที่เป็น `sync: false`
- `APP_URL` = URL ของ service บน Render
- `ASSET_URL` = URL เดียวกับ `APP_URL`
- `APP_KEY` = ผลลัพธ์จาก `php artisan key:generate --show`
- `JWT_SECRET` = secret สำหรับ jwt-auth
- `DB_URL` = Supabase Postgres connection string (ต้องมี `sslmode=require`)

ค่าแนะนำที่ใช้จริงใน production:

```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
DB_SSLMODE=require
DB_SCHEMA=laravel
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
RUN_MIGRATIONS=false
API_BOOKS_CACHE_TTL=60
API_LOG_SUCCESS=false
```

4) Deploy
- ระบบจะ build image จาก Dockerfile
- ตอน start **จะไม่**รัน migration โดยค่าเริ่มต้น (`RUN_MIGRATIONS=false`)
- ถ้าต้องการรัน migration ให้เปลี่ยนเป็น `RUN_MIGRATIONS=true`

### ตัวอย่าง DATABASE_URL (Supabase)

```text
postgresql://USER:PASSWORD@HOST:5432/postgres?sslmode=require
```

ค่าที่แนะนำสำหรับ API performance บน Render:

```env
API_BOOKS_CACHE_TTL=60
API_LOG_SUCCESS=false
```

สถานะปัจจุบัน:

- โปรเจกต์นี้ใช้ `CACHE_STORE=file` เป็นหลักใน production
- Redis ถูกติดตั้งและตั้งค่าไว้ใน container เพื่อรองรับอนาคต แต่ยังไม่ใช้งานเป็น default

หากต้องการสลับไปใช้ Redis ภายหลัง:

```env
CACHE_STORE=redis
```

### หมายเหตุ

- หากไม่ต้องการให้รัน migration ตอน start ให้ตั้ง `RUN_MIGRATIONS=false`
- หลังเปลี่ยนค่า env สำคัญ แนะนำ Manual Deploy ใหม่อีกครั้ง
- ถ้าเปลี่ยนค่า cache/session แล้วเจอ error ให้ deploy ใหม่เพื่อให้ startup script สร้างโฟลเดอร์ storage ครบ

## Production URL

- App: `https://book-management-8sxg.onrender.com/book`
- Swagger UI: `https://book-management-8sxg.onrender.com/api/documentation`

หมายเหตุ Swagger:
- เอกสารรองรับ HTTPS แล้ว (ลดปัญหา Mixed Content)
- ถ้า docs ยังไม่อัปเดต ให้รัน `php artisan l5-swagger:generate`

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
