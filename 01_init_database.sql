-- ═══════════════════════════════════════════════════════════════
-- GUMOLONG FARM — PostgreSQL Database Initialization
-- File: 01_init_database.sql
-- Jalankan: sudo -u postgres psql -f 01_init_database.sql
-- ═══════════════════════════════════════════════════════════════

-- 1. Buat USER (role) untuk aplikasi
CREATE USER gumolong_user WITH PASSWORD 'gumolong_dev_2026';

-- 2. Buat DATABASE
CREATE DATABASE gumolong_db
    WITH OWNER = gumolong_user
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TEMPLATE = template0;

-- 3. Grant semua privilege ke user
GRANT ALL PRIVILEGES ON DATABASE gumolong_db TO gumolong_user;

-- 4. Connect ke database baru dan grant schema privilege
\c gumolong_db
GRANT ALL ON SCHEMA public TO gumolong_user;
ALTER SCHEMA public OWNER TO gumolong_user;

-- 5. Verifikasi
\du gumolong_user
\l gumolong_db

-- ═══════════════════════════════════════════════════════════════
-- CATATAN:
-- - Password 'gumolong_dev_2026' bisa diganti sesuai kebutuhan
-- - Setiap tim member harus jalankan script ini di PostgreSQL lokal
-- - Setelah ini, jalankan: php artisan migrate
-- ═══════════════════════════════════════════════════════════════
