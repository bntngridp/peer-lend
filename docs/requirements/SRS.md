# Software Requirements Specification (SRS)
## Peer-Lend — P2P Lending Platform

| Atribut        | Nilai                          |
| -------------- | ------------------------------ |
| **Versi**      | 1.0.0                          |
| **Tanggal**    | 2026-07-15                     |
| **Author**     | Bintang Ridwan Pribadi         |
| **Status**     | Draft                          |
| **Platform**   | PHP Laravel 11 (Monolith)      |
| **Database**   | PostgreSQL                     |

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Gambaran Sistem](#2-gambaran-sistem)
3. [Aktor Sistem](#3-aktor-sistem)
4. [Alur Bisnis](#4-alur-bisnis)
5. [Modul & Functional Requirements](#5-modul--functional-requirements)
6. [Non-Functional Requirements](#6-non-functional-requirements)
7. [ERD & Database Schema](#7-erd--database-schema)
8. [Fase Pengembangan](#8-fase-pengembangan)
9. [Asumsi & Batasan](#9-asumsi--batasan)

---

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen ini merupakan spesifikasi kebutuhan perangkat lunak (SRS) untuk sistem platform pinjaman berbasis peer-to-peer (P2P Lending) bernama **Peer-Lend**. Dokumen ini berfungsi sebagai acuan teknis dan bisnis selama proses pengembangan sistem.

### 1.2 Ruang Lingkup
**Peer-Lend** adalah platform fintech P2P Lending berbasis web yang mempertemukan **Borrower** (peminjam) dengan **Lender** (pemberi pinjaman/investor) secara langsung tanpa perantara bank konvensional. Platform ini dibangun menggunakan arsitektur **Modular Monolith** dengan framework PHP Laravel 11.

### 1.3 Singkatan & Istilah

| Singkatan | Kepanjangan                              |
| --------- | ---------------------------------------- |
| P2P       | Peer-to-Peer                             |
| KYC       | Know Your Customer                       |
| RBAC      | Role-Based Access Control                |
| OTP       | One-Time Password                        |
| SRS       | Software Requirements Specification      |
| SWDD      | Software & Web Design Document           |
| MVP       | Minimum Viable Product                   |
| DDD       | Domain-Driven Design                     |
| ERD       | Entity-Relationship Diagram              |
| IDR       | Indonesian Rupiah                        |

---

## 2. Gambaran Sistem

Peer-Lend adalah platform digital yang memfasilitasi transaksi pinjam-meminjam antara individu atau pelaku usaha (UMKM) sebagai Borrower dengan investor perorangan sebagai Lender. Platform bertindak sebagai *marketplace* dan *intermediary* yang menyediakan:

- Sistem verifikasi identitas (KYC)
- Penilaian risiko kredit (Credit Scoring)
- Manajemen dana melalui virtual wallet
- Marketplace pinjaman transparan
- Sistem pengelolaan cicilan dan pembayaran
- Dashboard administrasi untuk pengawasan operasional

### 2.1 Diagram Alur Utama (Rupiah)

```
Borrower
   │
   │ Daftar & KYC
   ▼
Platform Verifikasi
   │
   │ KYC Approved
   ▼
Borrower Membuat Loan Request
   │
   ▼
Admin Review & Approve Loan
   │
   ▼
Loan Listing (Marketplace — Status: Open Funding)
   │
   │ Lender melihat listing di marketplace
   ▼
Lender Mendanai Sebagian/Seluruh Pinjaman (Funding)
   │
   │ Target dana 100% terpenuhi
   ▼
Loan Agreement di-generate & di-tanda tangani
   │
   ▼
Dana Dicairkan ke Borrower (Disbursement)
   │
   ▼
Loan Active — Borrower Membayar Cicilan Bulanan
   │
   ▼
Dana Cicilan (Pokok + Bunga) Didistribusikan ke Lender
   │
   ▼
Loan Completed
```

### 2.2 Diagram Alur Crypto (Opsional — Phase 3)

```
Borrower Request Loan (Crypto Currency)
   │
   ▼
Marketplace Listing
   │
   ▼
Investor Funding → Dana masuk ke Escrow Wallet
   │
   ▼
Borrower Menerima Dana dari Escrow
   │
   ▼
Repayment → Investor menerima Pokok + Bunga
```

---

## 3. Aktor Sistem

| Aktor                | Deskripsi                                                                                               |
| -------------------- | ------------------------------------------------------------------------------------------------------- |
| **Admin**            | Pengelola platform. Bertugas menyetujui KYC, approve/reject loan, manajemen fee & bunga, monitoring.   |
| **Borrower**         | Pengguna yang mengajukan pinjaman. Wajib melewati KYC sebelum bisa membuat Loan Request.                |
| **Lender**           | Investor yang mendanai pinjaman dari Borrower melalui marketplace. Wajib melewati KYC.                  |
| **Collection Officer** *(Opsional)* | Mengelola loan yang statusnya *default* atau telat bayar.                            |
| **Customer Service** *(Opsional)* | Menangani pertanyaan dan keluhan pengguna.                                             |

> **Catatan:** Satu user dapat memiliki lebih dari satu role (misal: seorang Lender juga bisa menjadi Borrower).

---

## 4. Alur Bisnis

### 4.1 Alur Registrasi & KYC

```
User Daftar (email + password)
   │
   ▼
Email Verification
   │
   ▼
Lengkapi Profile
   │
   ▼
Submit KYC (KTP, Selfie, opsional NPWP)
   │
   ▼
Admin Review KYC
   │
   ├── Rejected → User dinotifikasi, bisa re-submit
   └── Approved → User dapat mengakses fitur Borrower/Lender
```

### 4.2 Alur Loan Request (Borrower)

```
Borrower (KYC Approved) → Buat Loan Request (Draft)
   │
   ▼
Submit untuk Review Admin
   │
   ▼
Admin Review
   │
   ├── Rejected → Borrower dinotifikasi
   └── Approved → Loan masuk ke Marketplace (Open Funding)
```

### 4.3 Alur Funding (Lender)

```
Lender melihat Marketplace
   │
   ▼
Lender memilih Loan & menentukan jumlah dana
   │
   ▼
Dana di-hold dari Wallet Lender
   │
   ▼
Progress Funding bertambah
   │
   ├── Belum 100% → Tetap Open Funding
   └── 100% terpenuhi → Status: Funded → Generate Loan Agreement
```

### 4.4 Alur Repayment

```
Loan Active
   │
   ▼
Sistem generate Jadwal Cicilan (Installment Schedule)
   │
   ▼
Setiap jatuh tempo → Notifikasi dikirim ke Borrower
   │
   ▼
Borrower membayar cicilan (dari Wallet)
   │
   ▼
Sistem mendistribusikan (Principal + Interest) ke masing-masing Lender secara proporsional
   │
   ├── Semua cicilan lunas → Status: Completed
   └── Terlambat > X hari → Status: Default → Collection Officer notified
```

---

## 5. Modul & Functional Requirements

### 5.1 Authentication & Authorization

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-AUTH-01  | User dapat mendaftar menggunakan email dan password.                                     |
| FR-AUTH-02  | User menerima email verifikasi setelah registrasi.                                       |
| FR-AUTH-03  | User dapat login menggunakan email & password yang telah diverifikasi.                   |
| FR-AUTH-04  | User dapat melakukan Forgot Password via email (reset link).                             |
| FR-AUTH-05  | Sistem mendukung OTP melalui email untuk aksi sensitif (withdraw, disbursement).         |
| FR-AUTH-06  | Sistem mengimplementasi RBAC menggunakan tabel roles, permissions, dan role_permissions. |
| FR-AUTH-07  | Satu user dapat memiliki lebih dari satu role.                                           |

### 5.2 User Profile

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-PROF-01  | User dapat mengisi & mengubah profil: Nama, Email, Phone, Alamat, Pekerjaan, Penghasilan.|
| FR-PROF-02  | User dapat mengunggah foto avatar.                                                       |
| FR-PROF-03  | Perubahan email memerlukan verifikasi ulang.                                             |

### 5.3 KYC (Know Your Customer)

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-KYC-01   | User dapat mengajukan verifikasi KYC dengan mengunggah KTP, Selfie, dan opsional NPWP.  |
| FR-KYC-02   | Dokumen KYC disimpan secara aman di storage (lokal atau cloud).                          |
| FR-KYC-03   | Status KYC memiliki lifecycle: Pending, Approved, Rejected.                              |
| FR-KYC-04   | Admin dapat melihat detail dokumen dan mengubah status KYC.                              |
| FR-KYC-05   | User menerima notifikasi ketika status KYC berubah.                                      |
| FR-KYC-06   | User yang KYC-nya Rejected dapat mengajukan ulang.                                       |
| FR-KYC-07   | User tidak dapat membuat Loan Request atau melakukan Funding sebelum KYC Approved.       |

### 5.4 Wallet

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-WAL-01   | Setiap user memiliki Wallet untuk setiap currency yang didukung.                         |
| FR-WAL-02   | Wallet memiliki dua komponen saldo: available_balance dan hold_balance.                  |
| FR-WAL-03   | Saat Lender melakukan funding, dana dipindahkan dari available ke hold balance.          |
| FR-WAL-04   | User dapat melakukan Deposit ke wallet.                                                  |
| FR-WAL-05   | User dapat melakukan Withdraw dari available_balance (bukan hold).                       |
| FR-WAL-06   | Setiap mutasi wallet dicatat di tabel wallet_transactions.                               |
| FR-WAL-07   | Operasi debit/kredit wallet bersifat atomic menggunakan database transaction.            |

### 5.5 Loan Request

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-LOAN-01  | Borrower dapat membuat Loan Request dengan semua field yang ditentukan.                  |
| FR-LOAN-02  | Loan Request memiliki lifecycle: draft, pending, open_funding, funded, active, completed, default, cancelled. |
| FR-LOAN-03  | Borrower dapat menyimpan Loan Request sebagai draft sebelum submit.                      |
| FR-LOAN-04  | Admin dapat approve atau reject Loan Request.                                            |
| FR-LOAN-05  | Sistem mencatat approved_at, funded_at, disbursed_at untuk audit trail.                  |
| FR-LOAN-06  | Loan Request mendapatkan risk_grade (A/B/C/D) berdasarkan profil borrower.              |

### 5.6 Marketplace

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-MKT-01   | Marketplace menampilkan semua Loan dengan status open_funding.                           |
| FR-MKT-02   | Setiap listing menampilkan detail lengkap loan dan progress funding.                     |
| FR-MKT-03   | Marketplace mendukung filter berdasarkan Currency, Duration, Interest, Risk Grade.       |
| FR-MKT-04   | Marketplace mendukung sorting berdasarkan Newest, Highest Interest, Funding Progress.    |
| FR-MKT-05   | Lender dapat melihat halaman detail setiap Loan sebelum mendanai.                        |

### 5.7 Funding

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-FUND-01  | Lender dapat mendanai sebagian atau seluruh Loan yang sedang open_funding.               |
| FR-FUND-02  | Minimum dan maximum funding amount dikonfigurasi di level platform.                      |
| FR-FUND-03  | Dana Lender di-hold saat funding dilakukan.                                              |
| FR-FUND-04  | Saat total funding mencapai 100%, status Loan otomatis berubah menjadi funded.           |
| FR-FUND-05  | Jika Loan dibatalkan, dana hold dikembalikan ke semua Lender.                            |
| FR-FUND-06  | Lender dapat melihat daftar semua loan yang pernah didanai.                              |

### 5.8 Loan Agreement

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-AGRM-01  | Sistem men-generate Loan Agreement otomatis ketika Loan berstatus funded.                |
| FR-AGRM-02  | Loan Agreement berisi detail Borrower, Loan, daftar Lender, dan jadwal cicilan.          |
| FR-AGRM-03  | Loan Agreement memiliki status: waiting_signature, signed, active.                       |
| FR-AGRM-04  | Semua pihak harus acknowledge sebelum dana dicairkan.                                    |
| FR-AGRM-05  | Setelah ditandatangani, dana dicairkan dan status Loan berubah menjadi active.           |

### 5.9 Repayment

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-RPMT-01  | Sistem men-generate jadwal cicilan otomatis saat Loan menjadi active.                    |
| FR-RPMT-02  | Setiap installment memiliki due_date, principal, interest, penalty, dan status.          |
| FR-RPMT-03  | Borrower membayar cicilan dari Wallet sesuai installment yang jatuh tempo.               |
| FR-RPMT-04  | Sistem mendistribusikan dana ke Lender secara proporsional sesuai porsi funding.         |
| FR-RPMT-05  | Jika pembayaran melewati due_date, sistem menerapkan penalty.                            |
| FR-RPMT-06  | Jika keterlambatan melebihi batas, status Loan berubah menjadi default.                  |
| FR-RPMT-07  | Saat semua installment lunas, status Loan berubah menjadi completed.                     |

### 5.10 Payment

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-PAY-01   | Platform mendukung Deposit via payment gateway (Midtrans / Xendit).                      |
| FR-PAY-02   | Platform mendukung Withdraw ke rekening bank terdaftar.                                  |
| FR-PAY-03   | Setiap pembayaran menghasilkan record di tabel payments.                                  |
| FR-PAY-04   | Sistem memvalidasi callback/webhook dari payment gateway.                                 |

### 5.11 Transaction History

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-TXN-01   | User dapat melihat riwayat seluruh transaksi wallet mereka.                              |
| FR-TXN-02   | History dapat difilter berdasarkan tipe transaksi, tanggal, dan currency.                |
| FR-TXN-03   | Tipe transaksi: Deposit, Withdraw, Funding, Disbursement, Repayment, Interest, Fee, Penalty. |

### 5.12 Notification

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-NOTIF-01 | Sistem mengirimkan notifikasi Email untuk event-event penting.                           |
| FR-NOTIF-02 | Sistem menyimpan notifikasi In-App yang bisa dibaca oleh user.                           |
| FR-NOTIF-03 | Event pemicu: KYC status change, Loan status change, Installment reminder, Repayment received. |
| FR-NOTIF-04 | User dapat menandai notifikasi sebagai sudah dibaca.                                     |

### 5.13 Admin Dashboard

| Kode        | Requirement                                                                              |
| ----------- | ---------------------------------------------------------------------------------------- |
| FR-ADM-01   | Admin dapat melihat dan mengubah status KYC user.                                        |
| FR-ADM-02   | Admin dapat Approve / Reject Loan Request.                                               |
| FR-ADM-03   | Admin dapat Freeze / Unfreeze akun user.                                                 |
| FR-ADM-04   | Admin dapat mengelola konfigurasi Interest Rate, Fee, Penalty Rate, Loan Amount limit.   |
| FR-ADM-05   | Admin Dashboard menampilkan ringkasan analitik platform.                                 |
| FR-ADM-06   | Admin dapat melihat Audit Log seluruh aktivitas sistem.                                  |

---

## 6. Non-Functional Requirements

### 6.1 Keamanan

| Kode         | Requirement                                                                              |
| ------------ | ---------------------------------------------------------------------------------------- |
| NFR-SEC-01   | Password di-hash menggunakan bcrypt (cost factor >= 12).                                 |
| NFR-SEC-02   | Seluruh komunikasi menggunakan HTTPS/TLS.                                                |
| NFR-SEC-03   | API dilindungi dengan CSRF protection dan rate limiting.                                 |
| NFR-SEC-04   | Semua input user divalidasi dan di-sanitize (SQL Injection, XSS prevention).            |
| NFR-SEC-05   | File upload KYC divalidasi tipe MIME dan ukuran maksimum.                                |
| NFR-SEC-06   | Aksi sensitif (withdraw, disbursement) memerlukan OTP/2FA.                               |
| NFR-SEC-07   | Seluruh endpoint dilindungi middleware autentikasi dan otorisasi RBAC.                   |

### 6.2 Performa

| Kode          | Requirement                                                                              |
| ------------- | ---------------------------------------------------------------------------------------- |
| NFR-PERF-01   | Halaman Marketplace merespons dalam < 1 detik dengan hingga 1000 listing aktif.         |
| NFR-PERF-02   | Database indexing pada kolom yang sering di-query (status, user_id, created_at).         |
| NFR-PERF-03   | Operasi berat (generate agreement, kirim email batch) diproses via Laravel Queue.        |

### 6.3 Ketersediaan & Skalabilitas

| Kode           | Requirement                                                                              |
| -------------- | ---------------------------------------------------------------------------------------- |
| NFR-AVAIL-01   | Sistem dapat di-deploy menggunakan Docker Container.                                     |
| NFR-AVAIL-02   | Database PostgreSQL dengan support indexing dan atomic transaction.                      |
| NFR-AVAIL-03   | Arsitektur Modular Monolith memudahkan ekstraksi ke microservice di masa depan.          |

### 6.4 Maintainability

| Kode           | Requirement                                                                              |
| -------------- | ---------------------------------------------------------------------------------------- |
| NFR-MAIN-01    | Kode mengikuti PSR-12 coding standard.                                                   |
| NFR-MAIN-02    | Setiap modul bisnis memiliki unit test dan feature test (coverage >= 80%).               |
| NFR-MAIN-03    | Seluruh environment variable disimpan di file .env dan tidak di-commit ke repository.   |

---

## 7. ERD & Database Schema

### 7.1 Daftar Tabel (22 Tabel)

**RBAC:** roles, permissions, role_permissions, role_user
**Users:** users, profiles
**KYC:** kycs, kyc_documents
**Finance:** currencies, wallets, wallet_transactions
**Loan:** loan_categories, loan_requests, loan_fundings, loan_agreements, loan_installments, loan_repayments
**Payment & Ops:** payments, notifications, audit_logs, settings, interest_rates, fee_configurations

### 7.2 Schema Detail

#### users
```sql
id              UUID PRIMARY KEY
email           VARCHAR(255) UNIQUE NOT NULL
password        VARCHAR(255) NOT NULL
email_verified_at TIMESTAMP NULLABLE
google2fa_secret VARCHAR(255) NULLABLE
google2fa_enabled BOOLEAN DEFAULT FALSE
is_active       BOOLEAN DEFAULT TRUE
created_at      TIMESTAMP
updated_at      TIMESTAMP
deleted_at      TIMESTAMP NULLABLE
```

#### profiles
```sql
id              UUID PRIMARY KEY
user_id         UUID FK(users) UNIQUE
full_name       VARCHAR(255)
phone           VARCHAR(20) UNIQUE
avatar_path     VARCHAR(500) NULLABLE
address         TEXT NULLABLE
city            VARCHAR(100) NULLABLE
province        VARCHAR(100) NULLABLE
occupation      VARCHAR(255) NULLABLE
monthly_income  DECIMAL(20,2) NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### kycs
```sql
id              UUID PRIMARY KEY
user_id         UUID FK(users) UNIQUE
status          ENUM(pending, approved, rejected) DEFAULT pending
rejected_reason TEXT NULLABLE
reviewed_by     UUID FK(users) NULLABLE
reviewed_at     TIMESTAMP NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### kyc_documents
```sql
id              UUID PRIMARY KEY
kyc_id          UUID FK(kycs)
type            ENUM(ktp, selfie, npwp)
file_path       VARCHAR(500)
storage_driver  VARCHAR(50) DEFAULT local
verified_at     TIMESTAMP NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### wallets
```sql
id                  UUID PRIMARY KEY
user_id             UUID FK(users)
currency_id         INT FK(currencies)
available_balance   DECIMAL(20,8) DEFAULT 0
hold_balance        DECIMAL(20,8) DEFAULT 0
created_at          TIMESTAMP
updated_at          TIMESTAMP
UNIQUE(user_id, currency_id)
```

#### wallet_transactions
```sql
id              UUID PRIMARY KEY
wallet_id       UUID FK(wallets)
type            ENUM(deposit, withdraw, loan_disbursement, repayment, interest, fee, penalty, funding, refund)
amount          DECIMAL(20,8)
balance_before  DECIMAL(20,8)
balance_after   DECIMAL(20,8)
reference_id    UUID NULLABLE
reference_type  VARCHAR(100) NULLABLE
description     TEXT NULLABLE
created_at      TIMESTAMP
```

#### loan_requests
```sql
id                  UUID PRIMARY KEY
borrower_id         UUID FK(users)
category_id         INT FK(loan_categories)
amount              DECIMAL(20,2)
interest_rate       DECIMAL(5,2)
duration            INT
tenor_type          ENUM(monthly, weekly) DEFAULT monthly
purpose             VARCHAR(500)
currency_id         INT FK(currencies)
collateral_currency_id INT FK(currencies) NULLABLE
collateral_amount   DECIMAL(20,8) DEFAULT 0
initial_ltv         DECIMAL(5,2) DEFAULT 0
current_ltv         DECIMAL(5,2) DEFAULT 0
liquidation_ltv     DECIMAL(5,2) DEFAULT 80.00
liquidation_price   DECIMAL(20,8) DEFAULT 0
description         TEXT NULLABLE
risk_grade          ENUM(A, B, C, D) NULLABLE
status              ENUM(draft, pending, open_funding, funded, active, completed, default, cancelled) DEFAULT draft
funded_percentage   DECIMAL(5,2) DEFAULT 0
approved_by         UUID FK(users) NULLABLE
approved_at         TIMESTAMP NULLABLE
funded_at           TIMESTAMP NULLABLE
disbursed_at        TIMESTAMP NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### loan_fundings
```sql
id          UUID PRIMARY KEY
loan_id     UUID FK(loan_requests)
lender_id   UUID FK(users)
amount      DECIMAL(20,2)
percentage  DECIMAL(5,2)
status      ENUM(active, refunded) DEFAULT active
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

#### loan_agreements
```sql
id                      UUID PRIMARY KEY
loan_id                 UUID FK(loan_requests) UNIQUE
agreement_number        VARCHAR(100) UNIQUE
file_path               VARCHAR(500) NULLABLE
status                  ENUM(waiting_signature, signed, active) DEFAULT waiting_signature
borrower_signed_at      TIMESTAMP NULLABLE
signed_at               TIMESTAMP NULLABLE
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

#### loan_installments
```sql
id                  UUID PRIMARY KEY
loan_id             UUID FK(loan_requests)
installment_number  INT
due_date            DATE
principal_amount    DECIMAL(20,2)
interest_amount     DECIMAL(20,2)
penalty_amount      DECIMAL(20,2) DEFAULT 0
total_amount        DECIMAL(20,2)
status              ENUM(pending, paid, overdue, waived) DEFAULT pending
paid_at             TIMESTAMP NULLABLE
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### loan_repayments
```sql
id              UUID PRIMARY KEY
loan_id         UUID FK(loan_requests)
installment_id  UUID FK(loan_installments)
amount_paid     DECIMAL(20,2)
payment_date    DATE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

#### fee_configurations
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
type        ENUM(platform_fee, origination_fee, withdrawal_fee, penalty_rate)
value       DECIMAL(10,4)
value_type  ENUM(percentage, fixed)
is_active   BOOLEAN DEFAULT TRUE
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

#### settings
```sql
id          INT PRIMARY KEY AUTO_INCREMENT
key         VARCHAR(255) UNIQUE
value       TEXT
description TEXT NULLABLE
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

### 7.3 Diagram Relasi

```
users ─────── role_user ──── roles ── role_permissions ── permissions
  │
  ├── profiles (1:1)
  ├── kycs (1:1) ─── kyc_documents (1:N)
  ├── wallets (1:N) ─── currencies
  │     └── wallet_transactions (1:N)
  ├── loan_requests [as borrower] (1:N)
  │     ├── loan_fundings (1:N) ──── users [as lender]
  │     ├── loan_agreements (1:1)
  │     ├── loan_installments (1:N)
  │     │     └── loan_repayments (1:N)
  │     └── loan_categories
  ├── notifications (1:N)
  ├── audit_logs (1:N)
  └── payments (1:N)
```

---

## 8. Fase Pengembangan

### Phase 1 — Core Platform (MVP)
- [x] Setup Laravel 11 + PostgreSQL + Docker
- [ ] Authentication & Authorization (RBAC)
- [ ] User Profile
- [ ] KYC (Upload dokumen & review admin)
- [ ] Wallet (IDR only) + Deposit/Withdraw
- [ ] Loan Request (CRUD + lifecycle status)
- [ ] Marketplace (listing & filter)
- [ ] Funding (partial funding)
- [ ] Loan Agreement (generate & signature)
- [ ] Repayment & Installment Schedule
- [ ] Transaction History
- [ ] Admin Dashboard (KYC, Loan, User management)
- [ ] Basic Notification (Email)

### Phase 2 — Business Features
- [ ] Credit Scoring & Risk Grading (A/B/C/D)
- [ ] Interest Rate Management (per risk grade)
- [ ] Late Fee & Penalty Engine
- [ ] Loan Calculator (simulasi cicilan)
- [ ] Escrow Mechanism
- [ ] Internal Messaging (Borrower — Lender)
- [ ] In-App Notification
- [ ] Payment Gateway Integration (Midtrans/Xendit)

### Phase 3 — Enterprise Features
- [ ] Activity & Audit Logs (komprehensif)
- [ ] Reports & Analytics Dashboard
- [ ] Queue & Background Jobs (Laravel Queue)
- [ ] Scheduled Tasks (cron: reminder jatuh tempo, hitung bunga)
- [ ] Unit & Feature Tests (coverage >= 80%)
- [ ] Docker Deployment (docker-compose)
- [ ] REST API Documentation (Swagger/Postman)
- [ ] Crypto Wallet (opsional)

---

## 9. Asumsi & Batasan

### 9.1 Asumsi
1. Platform beroperasi sebagai proyek portofolio (tidak membutuhkan lisensi OJK).
2. Currency utama adalah IDR. Crypto bersifat opsional dan akan diimplementasi di Phase 3.
3. Email service menggunakan Mailtrap untuk development.
4. File storage menggunakan local storage untuk development, dapat dikonfigurasi ke S3 untuk production.
5. Jadwal cicilan menggunakan skema anuitas (equal installment) sebagai default.

### 9.2 Batasan
1. Platform tidak memiliki fitur secondary market.
2. Tidak ada integrasi credit bureau (BI Checking) pada fase MVP.
3. Digital signature bersifat sederhana (checkbox acknowledge), bukan e-sign tersertifikasi.
4. Fitur Collection Officer hanya berupa notifikasi internal pada Phase 1.
