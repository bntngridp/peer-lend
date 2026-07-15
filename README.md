# LendFlow: Peer-to-Peer (P2P) Lending & Collateral FinTech Platform

LendFlow adalah platform teknologi finansial *Peer-to-Peer (P2P) Lending* kelas *enterprise* berbasis **PHP Laravel 11**, **PostgreSQL**, dan **Redis**. Platform ini mengusung arsitektur **Modular Monolith** dengan pemisahan domain yang bersih, memfasilitasi transaksi pendanaan terdistribusi secara transparan, aman, dan efisien antara Peminjam (*Borrower*) dan Pemberi Dana (*Lender*).

---

## 🎨 Fitur Utama & Keunggulan

- **📊 Interactive Dashboard & Live Analytics**: Visualisasi data keuangan dinamis berbasis Chart.js (Doughnut Chart pelunasan Borrower & Bar Chart distribusi grade investasi Lender).
- **🔌 OpenAPI 3.0 REST API + Swagger UI**: Dokumentasi REST API interaktif mandiri di `/api/docs` lengkap dengan skema request/response untuk integrasi mobile app dan third-party.
- **🤖 Smart Auto-Invest Matching Engine**: Robot pendanaan otomatis bagi Lender berdasarkan aturan grade risiko pinjaman (A–D), batas LTV, dan limit alokasi dana per transaksi.
- **💳 Midtrans Payment Gateway (Sandbox)**: Integrasi deposit saldo IDR via Snap Popup Midtrans dengan Webhook callback otomatis terproteksi signature SHA512.
- **⚙️ Laravel Queue & Background Jobs**: Email notifikasi HTML dikirim secara asinkron via `SendNotificationEmailJob` (database queue driver) agar latensi request tetap cepat.
- **⏰ Automated Repayment Reminders**: Scheduler harian memindai cicilan jatuh tempo 3 hari ke depan dan mengirim pengingat otomatis melalui antrean email.
- **💬 Closed-Group Internal Messaging**: Ruang obrolan antar Borrower, Lender, dan Admin (per pinjaman) dengan proteksi akses 403 Forbidden untuk user tidak berwenang.
- **📜 Legal Agreement Generator**: Surat perjanjian kontrak pinjaman formal berkop hukum siap cetak/unduh PDF dengan jadwal amortisasi rinci dan meterai digital.
- **🧮 Loan Simulator Calculator**: Simulasi pinjaman real-time (anuitas / flat) menampilkan total cicilan, total bunga, dan jadwal amortisasi bulanan.
- **📈 Crypto LTV Collateral + Auto-Liquidation**: Penilaian agunan kripto (BTC, ETH, USDT) dengan mekanisme likuidasi otomatis jika LTV menyentuh 80%.
- **⚡ Daily Penalty Engine**: Penghitung denda keterlambatan harian (0.1%/hari) via scheduler Artisan otomatis.
- **🛡️ Enterprise Security**: 2-Factor Authentication (2FA) via Google Authenticator, KYC document secure streaming, dan Role-Based Access Control (RBAC: Admin, Borrower, Lender).

---

## 🏗️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.3, Laravel 11 (Modular Monolith) |
| **Database** | PostgreSQL 16 |
| **Cache & Queue** | Redis (via database driver untuk development) |
| **Frontend** | Laravel Blade, Alpine.js, Chart.js, Tailwind CSS |
| **Payment Gateway** | Midtrans Sandbox (Snap API) |
| **API Documentation** | OpenAPI 3.0 + Swagger UI (CDN) |
| **DevOps** | Docker, Docker Compose, Nginx, PHP-FPM |
| **Testing** | PHPUnit (52 tests, 250 assertions) |

---

## 🐳 Quick Start — Docker (Direkomendasikan)

LendFlow sudah dikontainerisasi penuh dengan Docker Compose.

```bash
# 1. Clone & copy environment
cp .env.example .env

# 2. Build dan jalankan semua container
docker compose up -d --build

# 3. Migrasi database & seed data awal
docker compose exec app php artisan migrate --seed
```

Akses aplikasi di **[http://localhost](http://localhost)** | API Docs di **[http://localhost/api/docs](http://localhost/api/docs)**

**Layanan Container yang Berjalan:**
| Container | Fungsi | Port |
|-----------|--------|------|
| `peer_lend_app` | Backend PHP 8.3-FPM | — |
| `peer_lend_nginx` | Web Server Nginx | 80 |
| `peer_lend_postgres` | Database PostgreSQL 16 | 5433 |
| `peer_lend_redis` | Cache & Queue | 6380 |
| `peer_lend_queue` | Laravel Queue Worker | — |
| `peer_lend_scheduler` | Cron Scheduler | — |

---

## 🛠️ Local Development (Tanpa Docker)

```bash
# 1. Install dependencies
composer install && npm install && npm run build

# 2. Copy & konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3. Migrasi & seed database
php artisan migrate --seed

# 4. Jalankan semua service paralel (server, queue, vite)
npm run dev
```

**Konfigurasi `.env` wajib:**
```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=peer-lend-db

MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```

---

## ⚙️ Artisan Commands Operasional

| Command | Fungsi | Jadwal |
|---------|--------|--------|
| `php artisan peer-lend:run-auto-invest` | Pencocokan & pendanaan otomatis Auto-Invest | Tiap jam |
| `php artisan peer-lend:calculate-penalties` | Hitung denda cicilan terlambat harian | Harian |
| `php artisan peer-lend:send-repayment-reminders` | Kirim reminder cicilan jatuh tempo 3 hari | Harian |
| `php artisan peer-lend:update-crypto-ltv` | Update harga & rasio LTV aset kripto | Tiap jam |
| `php artisan queue:work` | Jalankan worker antrean background jobs | Terus-menerus |

---

## 🧪 Test Suite

```bash
php artisan test
```

**Hasil Terkini: 52 tests, 250 assertions — ✅ Semua Hijau**

| Test Class | Tests | Keterangan |
|---|---|---|
| `Phase10And11MidtransSwaggerTest` | 4 | Payment Gateway & Swagger |
| `Phase8And9AutoInvestApiTest` | 4 | REST API & Auto-Invest |
| `Phase7ChatAndAgreementTest` | 5 | Chat & PDF Agreement |
| `Phase6CalculatorCreditScoringTest` | 7 | Kalkulator & Credit Score |
| `Phase5LateFeeOracleTest` | 5 | Penalty Engine & LTV |
| `+ other suites` | 27 | Auth, KYC, Wallet, Admin |

---

## 📚 API Documentation

Setelah server berjalan, buka Swagger UI interaktif di:
```
http://localhost/api/docs
```

**Endpoint yang tersedia:**
- `GET  /api/v1/marketplace` — Daftar pinjaman terbuka (paginated)
- `GET  /api/v1/marketplace/{id}` — Detail pinjaman + riwayat funding
- `POST /api/v1/loans/apply` — Ajukan pinjaman baru (KYC required)

---

## License

LendFlow is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
