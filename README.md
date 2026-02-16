# Book Management

ระบบจัดการหนังสือด้วย Laravel 12 รองรับหน้าเว็บ, REST API และ Swagger Docs

## Database ที่ใช้งาน

- ใช้ **Supabase PostgreSQL**
- กำหนดผ่านตัวแปร `DB_CONNECTION=pgsql` และ `DB_URL` ในไฟล์ `.env`

ตัวอย่าง:

```env
DB_CONNECTION=pgsql
DB_URL=postgresql://USER:PASSWORD@HOST:6543/postgres?sslmode=require
DB_SSLMODE=require
```

## Tech Stack

- PHP 8.2+
- Laravel 12
- JWT Auth (`tymon/jwt-auth`)
- Swagger (`darkaonline/l5-swagger`)

## เริ่มต้นใช้งาน (Local)

1) ติดตั้ง dependency

```bash
composer install
```

2) ตั้งค่า environment

```bash
cp .env.example .env
php artisan key:generate
```

3) ตั้งค่า Supabase ใน `.env` แล้วรัน migrate

```bash
php artisan migrate
```

4) (ไม่บังคับ) seed ข้อมูลตัวอย่าง

```bash
php artisan db:seed
```

## การรันโปรเจกต์

```bash
php artisan serve
```

## เส้นทางหลัก

- Web: `/books`
- Root (`/`) จะ redirect ไป `/books`
- Login: `/login`
- Register: `/register`
- Swagger: `/api/documentation`

## API หลัก

- Auth: `POST /api/register`, `POST /api/login`, `POST /api/logout`, `GET /api/me`
- Books: `GET /api/books`, `GET /api/books/{id}`, `POST /api/books`, `PUT /api/books/{id}`, `DELETE /api/books/{id}`

## Generate Swagger

```bash
php artisan l5-swagger:generate
```

ไฟล์ docs:

```text
storage/api-docs/api-docs.json
```

## Logging

- Success log: `storage/logs/success-YYYY-MM-DD.log`
- Error log: `storage/logs/error-YYYY-MM-DD.log`

```env
LOG_DAILY_DAYS=14
API_LOG_SUCCESS=false
```

