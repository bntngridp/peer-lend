# Peer-Lend: P2P Crypto Lending Platform

Peer-Lend adalah platform P2P Lending inovatif berbasis Laravel 11, PostgreSQL, dan Redis, yang memungkinkan peminjam (borrower) menggunakan aset crypto sebagai kolateral (LTV-based) dan pendana (lender) mendanai pinjaman secara terdistribusi.

---

## Fitur Utama

- **LTV Collateral Management**: Sistem kalkulasi nilai kolateral berbasis Real-time Crypto Price Oracle (CoinGecko API) dengan caching dan fallback mock system.
- **Auto Credit Scoring Engine**: Perhitungan skor kredit otomatis untuk menentukan *Risk Grade* (A, B, C, D) dan suku bunga pinjaman berdasarkan KYC, kelengkapan profil, dan riwayat pinjaman.
- **Interactive Loan Calculator**: Simulasi kalkulasi cicilan, bunga flat/midpoint, total biaya awal, dan jadwal amortisasi bulanan tanpa reload (AJAX-powered).
- **Automated Liquidation Engine**: Sistem monitoring rasio LTV harian untuk melikuidasi kolateral secara otomatis apabila mencapai ambang batas 80%.
- **Late Fee & Penalty Engine**: Perhitungan denda cicilan terlambat (0.1% per hari) secara terjadwal otomatis setiap hari.
- **Security Baseline**: 2-Factor Authentication (2FA) via Google Authenticator, KYC secure document streaming, dan Role-based middleware protection (Admin, Borrower, Lender).

---

## 🐳 Docker Deployment (Production-Ready)

Platform ini sudah dikonainerisasi secara penuh dengan Docker. Pastikan Anda sudah menginstal Docker & Docker Compose sebelum memulai.

### 1. Persiapan Environment
Salin template environment dan sesuaikan konfigurasinya:
```bash
cp .env.example .env
```
*Pastikan parameter database di `.env` merujuk ke service database docker (`DB_HOST=postgres`, `REDIS_HOST=redis`).*

### 2. Build dan Jalankan Container
Jalankan perintah berikut untuk membuild asset frontend secara multi-stage dan mengaktifkan seluruh service di background:
```bash
docker compose up -d --build
```
Ini akan mengaktifkan 6 container:
1. `peer_lend_app`: Backend PHP 8.3-FPM
2. `peer_lend_nginx`: Web Server Nginx (Port 80)
3. `peer_lend_postgres`: Database PostgreSQL 16 (Port 5433 eksternal)
4. `peer_lend_redis`: Cache & Queue storage (Port 6380 eksternal)
5. `peer_lend_queue`: Worker untuk antrean transaksi/pembayaran
6. `peer_lend_scheduler`: Cron task runner untuk pembaruan LTV dan kalkulasi denda harian

### 3. Inisialisasi Database
Setelah semua container berjalan lancar, lakukan migrasi dan seeding data:
```bash
docker compose exec app php artisan migrate --seed
```

Aplikasi kini dapat diakses melalui browser Anda di **[http://localhost](http://localhost)**.

---

## 🧪 Pengujian Suite
Untuk menjalankan seluruh 37 tests (termasuk dashboard, KYC, keamanan, kalkulator, dan kredit):
```bash
docker compose exec app php artisan test
```

## License
The Peer-Lend platform is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

