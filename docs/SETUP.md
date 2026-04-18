# Setup Project Gumolong Farm

## Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 20+
- npm
- PostgreSQL 15+

## Langkah Setup

1. Clone repo dan masuk ke direktori:
   ```bash
   git clone [URL] Gumolong && cd Gumolong
   ```

2. Switch ke branch develop:
   ```bash
   git checkout develop
   ```

3. Install PHP dependencies:
   ```bash
   composer install
   ```

4. Install JS dependencies:
   ```bash
   npm install
   ```

5. Setup environment:
   ```bash
   cp .env.example .env
   ```

6. Edit `.env` — isi `DB_USERNAME` dan `DB_PASSWORD` dengan credential lokal masing-masing.

7. Generate APP_KEY:
   ```bash
   php artisan key:generate
   ```

8. Setup PostgreSQL lokal (lihat section [Database Setup](#database-setup-postgresql) di bawah).

9. Run migrations:
   ```bash
   php artisan migrate
   ```

10. *(Opsional)* Seed data dummy:
    ```bash
    php artisan db:seed
    ```

11. Build assets:
    ```bash
    npm run dev    # development
    npm run build  # production
    ```

12. Jalankan server:
    ```bash
    php artisan serve
    ```

13. Buka <http://localhost:8000>

## Database Setup (PostgreSQL)

Start PostgreSQL service:
```bash
sudo service postgresql start        # Linux/WSL
brew services start postgresql@15    # macOS
```

Buat user dan database:
```sql
CREATE USER gumolong_user WITH PASSWORD 'password_anda';
CREATE DATABASE gumolong_db OWNER gumolong_user;
GRANT ALL PRIVILEGES ON DATABASE gumolong_db TO gumolong_user;
```

Update `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gumolong_db
DB_USERNAME=gumolong_user
DB_PASSWORD=password_anda
```

## Troubleshooting

| Masalah | Solusi |
|---|---|
| Migration gagal "connection refused" | Cek PostgreSQL service sudah running |
| Migration gagal "FATAL: password authentication failed" | Cek `DB_USERNAME` dan `DB_PASSWORD` di `.env` |
| npm error "node version mismatch" | Install Node 20 via [NodeSource](https://github.com/nodesource/distributions) |
| Composer error "memory limit" | `COMPOSER_MEMORY_LIMIT=-1 composer install` |
| Permission error di storage | `chmod -R 775 storage bootstrap/cache` |
