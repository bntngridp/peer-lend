AGENTS.md — Panduan singkat untuk agen otomatis / LLM pada repo peer-lend

Ringkasan singkat
- Nama repo: peer-lend
- Stack utama: PHP (Laravel modular), Docker, Vite/Tailwind (frontend), SQLite / PostgreSQL (env-dependent), queue jobs (Redis/DB), Midtrans pembayaran
- Struktur penting: `app/Modules/` (modular domain code), `routes/web.php` (rute HTTP + webhook), `app/Jobs/` (background jobs), `config/` (kunci konfigurasi), `database/` (migrations & factories), `tests/` (unit & feature)

Aturan operasional untuk agen
- Semua perubahan non-trivial yang dibuat oleh agen WAJIB direview oleh manusia sebelum merge. Sertakan di PR: `AI-assisted: yes` + ringkasan prompt/tujuan (bebas dari secret).
- Jangan menambahkan kredensial atau file `.env` ke repo. Agen tidak boleh hardcode secret atau API keys.
- Buat commit kecil, bertahap, dan sertakan test yang relevan untuk setiap perubahan fungsional.

Perintah dasar & alur verifikasi (contoh)
- Jalankan lokal via Docker Compose (direkomendasikan):
  - docker compose up -d --build
- Install / setup (lokal non-Docker):
  - composer install
  - npm install
  - cp .env.example .env && php artisan key:generate
  - php artisan migrate --seed
- Menjalankan test suite:
  - php artisan test
  - vendor/bin/phpunit --configuration phpunit.xml
- Frontend:
  - npm run dev  (vite dev)
  - npm run build

Pola & konvensi spesifik proyek
- Modular monolith: domain dikelompokkan di `app/Modules/{Auth,Loan,Wallet,KYC,...}`. Untuk menambah fitur: buat folder module baru, controller/use-case di dalam module, dan daftarkan rute di `routes/web.php` jika perlu.
- Delivery → Use Case → Repository → Domain: business logic seharusnya hidup di use case/module service, bukan di controller atau repository.
- Gunakan `app/Jobs/` untuk pekerjaan latar (email, notifikasi, proses investasi otomatis). Contoh: `SendNotificationEmailJob.php`.
- Payment/webhook: periksa `routes/web.php` untuk endpoint webhook pembayaran (contoh: POST `/api/payment/webhook`) dan file konfigurasi pembayaran `config/midtrans.php`.

File & titik masuk yang perlu diperiksa dulu
- `README.md` — setup, env, dan perintah penting.
- `composer.json` & `package.json` — dependency toolchain (pastikan paket tersedia sebelum mengubah code yang menggunakannya).
- `routes/web.php` — semua rute HTTP, termasuk webhook & versi API.
- `app/Modules/` — implementasi domain; cari pola controller → service/usecase → repository.
- `app/Jobs/` — background workers dan job scheduling.
- `tests/` — contoh test patterns (unit & feature). Jangan merusak kontrak response JSON yang ada.

CI / PR expectations untuk agen
- Pastikan perubahan lulus `php artisan test` secara lokal atau di CI sebelum membuat PR.
- Di PR description sertakan:
  - `AI-assisted: yes`
  - Ringkasan singkat perubahan + daftar file yang diubah
  - Perintah untuk menjalankan test/verifikasi manual

Deteksi lingkungan agen/automasi
- Repo mengandung dependency terkait deteksi agen/agent context (lihat `composer.json` / vendor packages). Agen otomatis harus tidak mencoba menonaktifkan atau mem-bypass mekanisme deteksi yang ada.

Checklist singkat sebelum commit oleh agen
- [ ] Tidak ada secret/hardcoded credential
- [ ] Tests relevan ditambahkan/diupdate
- [ ] Semua test lulus (`php artisan test`)
- [ ] Perubahan kecil & terfokus
- [ ] `AI-assisted: yes` ditambahkan ke deskripsi PR

Catatan terakhir (Ringkas)
Ikuti pola modular di `app/Modules/`, verifikasi rute di `routes/web.php`, dan selalu utamakan review manusia untuk perubahan fungsional. Jika butuh help men-generate scaffolding module + tests, buat PR terpisah kecil dan sertakan test yang gagal lalu perbaiki sampai hijau. Fighting! ✨

