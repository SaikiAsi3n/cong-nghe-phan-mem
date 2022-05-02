# Laravel & Vue Blog



## Setup

1. Cài đặt composer:
```
composer install
```
2. Cài đặt npm:
```
npm install
```
3. Tạo file .env -> cài đặt biến môi trường
```
cp .env.example .env
```
4. Tạo khóa mã hóa ứng dụng
```
php artisan key:generate
```
5. Tạo database cho app

6. Trong file .env, chú ý tới    DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD. 
Nhập cho khớp với database trên local

7. Tạo các column trên database đã khai báo trong source
```
php artisan migrate
```

8. Tạo dữ liệu mẫu đã quy định trong source
```
php artisan db:seed --class=DatabaseSeeder
```