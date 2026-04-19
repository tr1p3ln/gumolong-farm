# 🐑 Gumolong Farm — Sheep Farm Management System

Sistem informasi manajemen peternakan domba berbasis web (Owner/Admin) dan mobile (Pengurus Kandang).

**Tech Stack:** Laravel 11 · Next.js · PostgreSQL 15+ · TailwindCSS

---

## 📋 Prerequisites

Pastikan tools berikut sudah terinstall:

- **PHP** 8.2 atau lebih baru
- **Composer** 2.x
- **Node.js** 20 LTS dan **npm**
- **PostgreSQL** 15 atau lebih baru
- **Git**

> 💡 **Disarankan menggunakan WSL2 (Ubuntu)** untuk konsistensi environment dengan tim.

---

## 🚀 Quick Setup (Untuk Tim Developer)

### 1. Clone Repository

```bash
git clone https://github.com/[username]/Gumolong.git
cd Gumolong
git checkout develop
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment File

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup PostgreSQL Database

> ⚠️ **Lakukan setup PostgreSQL dulu sebelum lanjut ke step 5.** Lihat section [Database Setup (PostgreSQL)](#-database-setup-postgresql) di bawah.

### 5. Edit `.env` untuk Koneksi Database

Buka file `.env`, sesuaikan baris berikut:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gumolong_db
DB_USERNAME=gumolong_user
DB_PASSWORD=gumolong_dev_2026
```

### 6. Run Migrations & Seeder

```bash
# Buat semua 15 tabel
php artisan migrate

# (Opsional) Seed data dummy + default super admin
php artisan db:seed
```

### 7. Build Frontend Assets

```bash
npm run dev      # Mode development (hot reload)
# atau
npm run build    # Mode production
```

### 8. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: **<http://localhost:8000>**

**Default Login (jika sudah db:seed):**
- Email: `admin@gumolong.farm`
- Password: `admin123`

---

## 🗄️ Database Setup (PostgreSQL)

### A. Pastikan PostgreSQL Service Berjalan

```bash
# Linux/WSL
sudo service postgresql start
sudo service postgresql status   # cek status

# macOS (via Homebrew)
brew services start postgresql@15

# Windows (via PostgreSQL installer)
# Service biasanya auto-start. Cek di Services panel.
```

### B. Buat Database & User

**Cara 1 — Otomatis (Recommended):**

Jalankan SQL script yang sudah disediakan:

```bash
sudo -u postgres psql -f database/sql/01_init_database.sql
```

**Cara 2 — Manual:**

```bash
sudo -u postgres psql
```

Lalu di prompt PostgreSQL, jalankan:

```sql
CREATE USER gumolong_user WITH PASSWORD 'gumolong_dev_2026';

CREATE DATABASE gumolong_db
    WITH OWNER = gumolong_user
    ENCODING = 'UTF8'
    TEMPLATE = template0;

GRANT ALL PRIVILEGES ON DATABASE gumolong_db TO gumolong_user;

\c gumolong_db
GRANT ALL ON SCHEMA public TO gumolong_user;
ALTER SCHEMA public OWNER TO gumolong_user;

\q
```

### C. Verifikasi Koneksi

```bash
psql -U gumolong_user -d gumolong_db -h 127.0.0.1
# Masukkan password: gumolong_dev_2026
# Jika berhasil masuk ke prompt psql, koneksi OK ✅
```

---

## 📊 Struktur Database (15 Tabel)

| Tabel | Modul | Deskripsi |
|-------|-------|-----------|
| `user` | User Management (FR-1.2) | Multi-role: super_admin, admin, kepala_kandang, pengurus_kandang |
| `kandang` | Master Data | Daftar kandang dengan tipe & kapasitas |
| `domba` | A-03 Data Domba | Master domba dengan ear tag B-xxx/J-xxx + silsilah |
| `pakan_stok` | A-04 Stok Pakan | Stok pakan: rumput, konsentrat, silase, ampas tahu |
| `pemberian_pakan` | A-08 Pakan Individual | Tracking pemberian per domba per sesi |
| `obat_vaksin` | A-05 Obat & Vaksin | Master obat & vaksin dengan stok & expired |
| `medical_record` | A-07 Kesehatan | Riwayat sakit, gejala, diagnosa, sembuh |
| `pemakaian_obat` | A-07 Kesehatan | Detail penggunaan obat per medical record |
| `vaksinasi` | A-07 Kesehatan | Riwayat vaksinasi per domba |
| `penimbangan` | A-06 Tracking Pertumbuhan | Tracking berat & ADG |
| `perkawinan` | A-09 Reproduksi | Catat kawin + konfirmasi kebuntingan (USG/observasi) |
| `kelahiran` | A-09 Reproduksi | Catat kelahiran dari hasil perkawinan |
| `anak_lahir` | A-09 Reproduksi | Detail tiap anak yang lahir |
| `tugas_harian` | A-11 Daily Task | Checklist harian pengurus kandang |
| `notifikasi` | A-12 Notifikasi | Alert: stok menipis, expired, HPL, vaksin, ADG rendah |

📐 **Diagram ERD lengkap:** Lihat file `database/erd_gumolong_v2.dbml` (paste ke [dbdiagram.io](https://dbdiagram.io/d) untuk visualisasi).

---

## 🔧 Useful Commands

```bash
# Reset database (hapus semua data, jalankan ulang migration)
php artisan migrate:fresh

# Reset + seed data dummy
php artisan migrate:fresh --seed

# Rollback migration terakhir
php artisan migrate:rollback

# Cek status migration
php artisan migrate:status

# Buat migration baru
php artisan make:migration nama_migration

# Buat seeder baru
php artisan make:seeder NamaSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 🐛 Troubleshooting

| Masalah | Solusi |
|---|---|
| `php artisan serve` error path WSL/Windows | Jalankan dari **terminal WSL** (bukan PowerShell), pastikan PHP terinstall di WSL |
| Migration gagal `connection refused` | Cek `sudo service postgresql status` — pastikan running |
| Migration gagal `password authentication failed` | Cek `DB_USERNAME` & `DB_PASSWORD` di `.env` sesuai user PostgreSQL |
| Migration gagal `database "gumolong_db" does not exist` | Jalankan dulu SQL init di section [Database Setup](#-database-setup-postgresql) |
| Migration gagal `permission denied for schema public` | Jalankan: `GRANT ALL ON SCHEMA public TO gumolong_user;` di psql |
| `npm install` error node version mismatch | Install Node 20 LTS via [NodeSource](https://github.com/nodesource/distributions) |
| Composer error `memory limit` | `COMPOSER_MEMORY_LIMIT=-1 composer install` |
| Permission error storage/cache | `chmod -R 775 storage bootstrap/cache` |
| ENUM error saat migrate | Pastikan PostgreSQL versi 15+ dan Laravel 11+ |

---

## 👥 Tim Developer Workflow

1. **Pull latest develop:**
   ```bash
   git checkout develop
   git pull origin develop
   ```

2. **Update dependencies (jika ada perubahan):**
   ```bash
   composer install
   npm install
   ```

3. **Update database (jika ada migration baru):**
   ```bash
   php artisan migrate
   ```

4. **Buat branch baru untuk fitur:**
   ```bash
   git checkout -b feature/nama-fitur
   ```

5. **Commit & push:**
   ```bash
   git add .
   git commit -m "feat: deskripsi fitur"
   git push origin feature/nama-fitur
   ```

6. **Buat Pull Request ke `develop`** di GitHub.

---

## 📚 Dokumentasi Tambahan

- **Functional Requirements:** `docs/FR.pdf`
- **Use Case Diagram:** `docs/use_case.puml`
- **BPMN:** `docs/bpmn/`
- **ERD:** `database/erd_gumolong_v2.dbml`
- **Lo-fi Wireframes:** `docs/wireframes/`
- **Hi-fi Designs:** Stitch project link (lihat di docs)

---

## 🤝 Kontributor

Project ini adalah Tugas Akhir **Program Studi Sistem Informasi - ISB-404**, Institut Teknologi Nasional Bandung.  
Mitra industri: **Gumolong Farm**.

---

**Built with ❤️ for Indonesian sheep farming**