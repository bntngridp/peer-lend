# 🔑 Seeder Accounts — peer-lend

Daftar akun yang otomatis dibuat saat menjalankan `php artisan migrate --seed` atau `php artisan db:seed`.

> **Password semua akun**: `password123`

---

## Cara Menjalankan Seeder

```bash
# Jalankan seeder tanpa reset database
php artisan db:seed

# Reset database + seeder (hati-hati: menghapus semua data!)
php artisan migrate:fresh --seed

# Via Docker
docker compose exec app php artisan migrate:fresh --seed --force
```

---

## 👑 Admin Panel

| # | Email | Password | Nama Lengkap | Role | Saldo IDR | KYC | Akses |
|:---:|:---|:---|:---|:---:|---:|:---:|:---:|
| 1 | `admin@peerlend.com` | `password123` | System Administrator | `admin` | Rp 0 | ✅ | Admin Panel |
| 2 | `admin1@lendflow.com` | `password123` | Primary System Admin | `admin` | Rp 100.000.000 | ✅ | Admin Panel |
| 3 | `admin2@lendflow.com` | `password123` | Compliance Officer Admin | `admin` | Rp 50.000.000 | ✅ | Admin Panel |

---

## 🏦 Borrower (Peminjam)

| # | Email | Password | Nama Lengkap | Role | Saldo IDR | KYC |
|:---:|:---|:---|:---|:---:|---:|:---:|
| 4 | `borrower1@lendflow.com` | `password123` | Budi Santoso | `borrower` | Rp 15.000.000 | ✅ |
| 5 | `borrower2@lendflow.com` | `password123` | Siti Aminah | `borrower` | Rp 8.000.000 | ✅ |

---

## 💰 Lender (Investor/Pemberi Dana)

| # | Email | Password | Nama Lengkap | Role | Saldo IDR | KYC |
|:---:|:---|:---|:---|:---:|---:|:---:|
| 6 | `lender1@lendflow.com` | `password123` | Rizky Pratama | `lender` | Rp 250.000.000 | ✅ |
| 7 | `lender2@lendflow.com` | `password123` | Dewi Lestari | `lender` | Rp 500.000.000 | ✅ |

---

## 🎧 Customer Service

| # | Email | Password | Nama Lengkap | Role | Saldo IDR | KYC | Akses |
|:---:|:---|:---|:---|:---:|---:|:---:|:---:|
| 8 | `cs1@lendflow.com` | `password123` | Andi CS Support 1 | `customer_service` | Rp 0 | ✅ | KYC Review, User List |
| 9 | `cs2@lendflow.com` | `password123` | Maya CS Support 2 | `customer_service` | Rp 0 | ✅ | KYC Review, User List |

---

## 📋 Collection Officer

| # | Email | Password | Nama Lengkap | Role | Saldo IDR | KYC | Akses |
|:---:|:---|:---|:---|:---:|---:|:---:|:---:|
| 10 | `collector1@lendflow.com` | `password123` | Eko Collection Officer 1 | `collection_officer` | Rp 0 | ✅ | Loan Review, Transaksi |
| 11 | `collector2@lendflow.com` | `password123` | Fajar Collection Officer 2 | `collection_officer` | Rp 0 | ✅ | Loan Review, Transaksi |

---

## 🔐 Role & Akses Matrix

| Role | Dashboard | KYC Review | User List | Loan Review | Transaksi | Financials | Analytics |
|:---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `admin` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `borrower` | ✅ | — | — | Milik sendiri | — | — | — |
| `lender` | ✅ | — | — | Marketplace | — | — | — |
| `customer_service` | ✅ | ✅ | ✅ | — | — | — | — |
| `collection_officer` | ✅ | — | — | ✅ | ✅ | — | — |

---

## 📁 Sumber Seeder

| File Seeder | Akun yang Dibuat |
|:---|:---|
| `database/seeders/AdminUserSeeder.php` | `admin@peerlend.com` |
| `database/seeders/DummyUsersSeeder.php` | `admin1`, `admin2`, `borrower1`, `borrower2`, `lender1`, `lender2`, `cs1`, `cs2`, `collector1`, `collector2` @lendflow.com |

---

> ⚠️ **Jangan gunakan akun ini di production!** File ini hanya untuk keperluan development & testing lokal.
