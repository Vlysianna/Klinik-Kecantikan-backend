# 💄 Klinik Kecantikan — Backend

REST API backend untuk sistem manajemen **Klinik Kecantikan** berbasis **Laravel**. Menyediakan autentikasi, manajemen produk, transaksi POS, dan laporan penjualan harian.

---

## 🧰 Tech Stack

| Teknologi | Keterangan |
|---|---|
| PHP | >= 8.2 |
| Laravel | ^12.x |
| Laravel Sanctum | Autentikasi token API |
| MySQL | Database utama |
| Composer | Package manager PHP |

---

## 📁 Struktur Proyek

```
app/
├── Http/
│   └── Controllers/
│       ├── AuthController.php
│       ├── ProductController.php
│       └── TransactionController.php
├── Models/             # Eloquent models
database/
├── migrations/         # Skema tabel database
│   ├── create_users_table.php
│   ├── create_products_table.php
│   ├── create_transactions_table.php
│   └── create_transaction_details_table.php
└── seeders/            # Data awal (seeder)
routes/
└── api.php             # Definisi endpoint API
config/
└── cors.php            # Konfigurasi CORS
```

---

## ⚙️ Prerequisites

Pastikan sudah terinstall:
- **PHP** >= 8.2
- **Composer** >= 2.x
- **MySQL** (atau Laragon / XAMPP)

---

## 🚀 Setup & Instalasi

### 1. Clone Repository

```bash
git clone <url-repository>
cd Klinik-Kecantikan-backend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Sesuaikan konfigurasi database di `.env`:

```env
APP_NAME=Laravel
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klinik-kecantikan-be
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Buat Database

Buat database baru di MySQL dengan nama `klinik-kecantikan-be`, lalu jalankan migrasi:

```bash
php artisan migrate
```

### 6. (Opsional) Jalankan Seeder

```bash
php artisan db:seed
```

### 7. Jalankan Development Server

```bash
php artisan serve
```

API akan berjalan di: **http://localhost:8000**

---

## 🔌 API Endpoints

Semua endpoint (kecuali login) membutuhkan header:
```
Authorization: Bearer <token>
```

### Auth

| Method | Endpoint | Keterangan |
|---|---|---|
| POST | `/api/login` | Login & dapatkan token |
| POST | `/api/logout` | Logout (hapus token) |
| GET | `/api/user` | Data user yang login |

### Produk

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/products` | Daftar semua produk |
| POST | `/api/products` | Tambah produk baru |
| GET | `/api/products/{id}` | Detail produk |
| PUT | `/api/products/{id}` | Update produk |
| DELETE | `/api/products/{id}` | Hapus produk |

### Transaksi

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/transactions` | Daftar transaksi |
| POST | `/api/transactions` | Buat transaksi baru |
| GET | `/api/transactions/{id}` | Detail transaksi |

### Laporan

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/reports/daily` | Laporan penjualan harian |

---

## 🗄️ Struktur Database

| Tabel | Keterangan |
|---|---|
| `users` | Data admin/kasir |
| `products` | Produk & layanan klinik |
| `transactions` | Header transaksi POS |
| `transaction_details` | Detail item per transaksi |
| `personal_access_tokens` | Token Sanctum |

---


