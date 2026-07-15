# Software & Web Design Document (SWDD)
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

1. [Arsitektur Sistem](#1-arsitektur-sistem)
2. [Struktur Direktori](#2-struktur-direktori)
3. [Desain Modul & Komponen](#3-desain-modul--komponen)
4. [Desain Database](#4-desain-database)
5. [Desain API & Routing](#5-desain-api--routing)
6. [Desain UI/UX](#6-desain-uiux)
7. [Desain Keamanan](#7-desain-keamanan)
8. [Desain Queue & Background Jobs](#8-desain-queue--background-jobs)
9. [Desain Events & Listeners](#9-desain-events--listeners)
10. [Tech Stack](#10-tech-stack)
11. [Deployment Design](#11-deployment-design)

---

## 1. Arsitektur Sistem

### 1.1 Pola Arsitektur

Peer-Lend menggunakan pola **Modular Monolith** — sebuah pendekatan di mana seluruh aplikasi berjalan dalam satu proses tunggal (monolith), namun secara internal diorganisir ke dalam modul-modul domain yang memiliki batas tanggung jawab yang jelas (Domain-Driven Design / DDD).

**Keuntungan pola ini untuk portofolio:**
- Tetap mudah di-deploy (satu container/server).
- Mendemonstrasikan pemahaman DDD dan pemisahan concerns.
- Mudah diekstrak menjadi microservice di masa depan jika dibutuhkan.

### 1.2 Layer Arsitektur Per Modul

```
HTTP Request
     │
     ▼
[ Middleware Layer ]
  - Auth (Sanctum/Session)
  - RBAC (Permission Check)
  - Rate Limiting
  - CSRF
     │
     ▼
[ Routing Layer ]
  routes/web.php
  routes/api.php
  app/Modules/{Module}/Routes/
     │
     ▼
[ Controller Layer ]
  app/Modules/{Module}/Controllers/
  - Parse request, validate (via Form Request)
  - Delegate ke Service
  - Return response (Blade view / JSON)
     │
     ▼
[ Service Layer ]  ← Business Logic
  app/Modules/{Module}/Services/
  - Orchestrate business rules
  - Dispatch events / jobs
  - Koordinasi antar repository
     │
     ▼
[ Repository Layer ]  ← Data Access
  app/Modules/{Module}/Repositories/
  - Query Eloquent models
  - Zero business logic
     │
     ▼
[ Model / Eloquent Layer ]
  app/Models/
  - Representasi tabel database
  - Relasi, scopes, casting
     │
     ▼
[ Database — PostgreSQL ]
```

### 1.3 Diagram Arsitektur Tingkat Tinggi

```
┌─────────────────────────────────────────────────────┐
│                     BROWSER / CLIENT                │
└───────────────────────┬─────────────────────────────┘
                        │ HTTPS
┌───────────────────────▼─────────────────────────────┐
│               Laravel Application                    │
│                                                      │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────┐  │
│  │  Web     │  │  Admin   │  │   API (future)   │  │
│  │  Routes  │  │  Routes  │  │   Routes         │  │
│  └────┬─────┘  └────┬─────┘  └────────┬─────────┘  │
│       │              │                 │             │
│  ┌────▼──────────────▼─────────────────▼──────────┐ │
│  │              Middleware Stack                   │ │
│  │  (Auth, RBAC, RateLimit, CSRF, Logging)        │ │
│  └────────────────────┬────────────────────────────┘ │
│                       │                              │
│  ┌────────────────────▼────────────────────────────┐ │
│  │              Module Controllers                  │ │
│  │  Auth | User | KYC | Wallet | Loan | Funding   │ │
│  │  Repayment | Payment | Notification | Admin     │ │
│  └────────────────────┬────────────────────────────┘ │
│                       │                              │
│  ┌────────────────────▼────────────────────────────┐ │
│  │              Service Layer                       │ │
│  └────────────────────┬────────────────────────────┘ │
│                       │                              │
│  ┌────────────────────▼────────────────────────────┐ │
│  │            Repository / Eloquent                 │ │
│  └────────────────────┬────────────────────────────┘ │
│                       │                              │
│  ┌──────────┐  ┌──────▼──────┐  ┌───────────────┐  │
│  │  Queue   │  │  PostgreSQL  │  │  File Storage  │  │
│  │  Worker  │  │  (Docker)   │  │  (Local/S3)   │  │
│  └──────────┘  └─────────────┘  └───────────────┘  │
└─────────────────────────────────────────────────────┘
```

---

## 2. Struktur Direktori

```
peer-lend/
│
├── app/
│   ├── Modules/                     ← Modul domain bisnis
│   │   ├── Auth/
│   │   │   ├── Controllers/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   └── PasswordResetController.php
│   │   │   ├── Requests/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Services/
│   │   │   │   └── AuthService.php
│   │   │   └── Routes/
│   │   │       └── auth.php
│   │   │
│   │   ├── User/
│   │   │   ├── Controllers/
│   │   │   │   └── ProfileController.php
│   │   │   ├── Requests/
│   │   │   ├── Services/
│   │   │   │   └── ProfileService.php
│   │   │   ├── Repositories/
│   │   │   │   └── UserRepository.php
│   │   │   └── Routes/
│   │   │
│   │   ├── KYC/
│   │   │   ├── Controllers/
│   │   │   │   └── KYCController.php
│   │   │   ├── Services/
│   │   │   │   └── KYCService.php
│   │   │   ├── Repositories/
│   │   │   └── Routes/
│   │   │
│   │   ├── Wallet/
│   │   │   ├── Controllers/
│   │   │   │   ├── WalletController.php
│   │   │   │   └── TransactionController.php
│   │   │   ├── Services/
│   │   │   │   ├── WalletService.php
│   │   │   │   └── TransactionService.php
│   │   │   ├── Repositories/
│   │   │   └── Routes/
│   │   │
│   │   ├── Loan/
│   │   │   ├── Controllers/
│   │   │   │   ├── LoanRequestController.php
│   │   │   │   └── MarketplaceController.php
│   │   │   ├── Services/
│   │   │   │   ├── LoanService.php
│   │   │   │   ├── InstallmentService.php    ← Generate jadwal cicilan
│   │   │   │   └── CreditScoringService.php  ← Kalkulasi risk grade
│   │   │   ├── Repositories/
│   │   │   │   └── LoanRepository.php
│   │   │   └── Routes/
│   │   │
│   │   ├── Funding/
│   │   │   ├── Controllers/
│   │   │   │   └── FundingController.php
│   │   │   ├── Services/
│   │   │   │   └── FundingService.php
│   │   │   ├── Repositories/
│   │   │   └── Routes/
│   │   │
│   │   ├── Repayment/
│   │   │   ├── Controllers/
│   │   │   │   └── RepaymentController.php
│   │   │   ├── Services/
│   │   │   │   ├── RepaymentService.php
│   │   │   │   └── DistributionService.php  ← Distribusi ke lender
│   │   │   ├── Repositories/
│   │   │   └── Routes/
│   │   │
│   │   ├── Payment/
│   │   │   ├── Controllers/
│   │   │   │   └── PaymentController.php
│   │   │   ├── Services/
│   │   │   │   └── PaymentGatewayService.php
│   │   │   └── Routes/
│   │   │
│   │   ├── Notification/
│   │   │   ├── Services/
│   │   │   │   └── NotificationService.php
│   │   │   └── Mail/
│   │   │       ├── KYCApprovedMail.php
│   │   │       ├── LoanApprovedMail.php
│   │   │       └── InstallmentReminderMail.php
│   │   │
│   │   ├── Admin/
│   │   │   ├── Controllers/
│   │   │   │   ├── AdminDashboardController.php
│   │   │   │   ├── AdminKYCController.php
│   │   │   │   ├── AdminLoanController.php
│   │   │   │   └── AdminUserController.php
│   │   │   ├── Services/
│   │   │   └── Routes/
│   │   │
│   │   └── Shared/
│   │       ├── Traits/
│   │       │   └── HasAuditLog.php
│   │       ├── Helpers/
│   │       │   └── CurrencyHelper.php
│   │       └── Exceptions/
│   │           ├── InsufficientBalanceException.php
│   │           └── KYCNotApprovedException.php
│   │
│   ├── Models/                      ← Eloquent Models
│   │   ├── User.php
│   │   ├── Profile.php
│   │   ├── KYC.php
│   │   ├── KYCDocument.php
│   │   ├── Wallet.php
│   │   ├── WalletTransaction.php
│   │   ├── Currency.php
│   │   ├── LoanRequest.php
│   │   ├── LoanFunding.php
│   │   ├── LoanAgreement.php
│   │   ├── LoanInstallment.php
│   │   ├── LoanRepayment.php
│   │   ├── LoanCategory.php
│   │   ├── Payment.php
│   │   ├── Notification.php
│   │   ├── AuditLog.php
│   │   ├── Setting.php
│   │   ├── InterestRate.php
│   │   └── FeeConfiguration.php
│   │
│   ├── Policies/                    ← Authorization policies
│   │   ├── LoanPolicy.php
│   │   ├── FundingPolicy.php
│   │   └── WalletPolicy.php
│   │
│   ├── Events/                      ← Domain events
│   │   ├── KYCApproved.php
│   │   ├── LoanFunded.php
│   │   ├── LoanDisbursed.php
│   │   └── RepaymentPaid.php
│   │
│   ├── Listeners/                   ← Event handlers
│   │   ├── SendKYCApprovedNotification.php
│   │   ├── GenerateLoanAgreement.php
│   │   ├── DisburseFundsToLenders.php
│   │   └── DistributeRepaymentToLenders.php
│   │
│   ├── Jobs/                        ← Queue jobs
│   │   ├── SendEmailJob.php
│   │   ├── GenerateAgreementJob.php
│   │   └── ProcessRepaymentDistributionJob.php
│   │
│   ├── Console/
│   │   └── Commands/
│   │       ├── CheckOverdueInstallments.php
│   │       └── SendInstallmentReminders.php
│   │
│   ├── Http/
│   │   └── Middleware/
│   │       ├── CheckKYCApproved.php
│   │       └── EnsureRoleMiddleware.php
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── EventServiceProvider.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php          ← Layout utama user
│       │   └── admin.blade.php        ← Layout admin
│       ├── auth/
│       ├── dashboard/
│       ├── marketplace/
│       ├── loan/
│       ├── wallet/
│       ├── kyc/
│       └── admin/
│
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── RoleSeeder.php
│       ├── CurrencySeeder.php
│       ├── SettingSeeder.php
│       └── DatabaseSeeder.php
│
├── docs/
│   └── requirements/
│       ├── SRS.md
│       └── SWDD.md
│
└── tests/
    ├── Feature/
    │   ├── Auth/
    │   ├── KYC/
    │   ├── Loan/
    │   └── Wallet/
    └── Unit/
        ├── Services/
        └── Helpers/
```

---

## 3. Desain Modul & Komponen

### 3.1 Auth Module

**Komponen:**
- `RegisterController` → validasi input, hash password, create user, kirim email verifikasi.
- `LoginController` → autentikasi dengan Laravel Sanctum (session-based untuk web).
- `PasswordResetController` → generate reset token, kirim email, update password.
- `AuthService` → business logic registrasi & autentikasi.

**State Diagram User:**
```
[Unregistered] → Register → [Unverified] → Verify Email → [Active]
[Active] → Admin Freeze → [Frozen]
[Frozen] → Admin Unfreeze → [Active]
```

### 3.2 KYC Module

**Komponen:**
- `KYCController` → handle upload dokumen, tampilkan status.
- `KYCService` → validasi file upload, simpan ke storage, update status.
- `AdminKYCController` → review KYC, approve/reject dengan alasan.

**State Machine KYC:**
```
[No KYC] → Submit → [Pending] → Admin Review → [Approved]
                                              → [Rejected] → Re-submit → [Pending]
```

### 3.3 Wallet Module

**Desain Wallet Service (Atomic Transaction):**
```php
// Contoh alur debit wallet (funding)
DB::transaction(function () use ($wallet, $amount) {
    // 1. Lock wallet row (SELECT FOR UPDATE)
    $wallet = Wallet::lockForUpdate()->find($walletId);
    
    // 2. Validasi saldo cukup
    if ($wallet->available_balance < $amount) {
        throw new InsufficientBalanceException();
    }
    
    // 3. Update balance
    $wallet->decrement('available_balance', $amount);
    $wallet->increment('hold_balance', $amount);
    
    // 4. Catat transaksi
    WalletTransaction::create([...]);
});
```

### 3.4 Loan Module

**InstallmentService — Kalkulasi Cicilan:**

Platform menggunakan skema **Flat Rate (Equal Principal)**:
- Principal per bulan = `Loan Amount / Duration`
- Interest per bulan = `Loan Amount * (Interest Rate / 12 / 100)`
- Total per bulan = `Principal + Interest`

**CreditScoringService — Risk Grade:**

| Faktor                | Bobot |
| --------------------- | ----- |
| Monthly Income        | 35%   |
| Loan Amount/Income    | 30%   |
| Occupation Type       | 20%   |
| Loan Duration         | 15%   |

| Skor   | Grade | Interest Range |
| ------ | ----- | -------------- |
| 85-100 | A     | 8% - 10%       |
| 70-84  | B     | 11% - 14%      |
| 55-69  | C     | 15% - 18%      |
| < 55   | D     | 19% - 24%      |

### 3.5 Funding Module

**Alur Funding (DistributionService):**
```
Lender submit funding
    │
    ▼
Validasi: KYC Approved? Saldo cukup? Loan masih open?
    │
    ▼
DB Transaction:
  - Debit available_balance Lender
  - Credit hold_balance Lender
  - Create loan_funding record
  - Update loan funded_percentage
    │
    ▼
Cek: funded_percentage >= 100%?
    │
    ├── Ya → Update status loan ke 'funded' → Dispatch LoanFunded event
    └── Tidak → Selesai
```

### 3.6 Repayment Module

**DistributionService — Proporsional Distribution:**
```
Repayment diterima (amount = Principal + Interest)
    │
    ▼
Ambil semua loan_fundings untuk loan tersebut
    │
    ▼
Untuk setiap lender:
  lender_share = repayment_amount * (lender_funding / total_loan)
    │
    ▼
DB Transaction:
  - Credit available_balance Lender
  - Debit hold_balance Lender (jika masih ada)
  - Create wallet_transaction untuk masing-masing Lender
    │
    ▼
Update installment status → 'paid'
Dispatch RepaymentPaid event
```

---

## 4. Desain Database

### 4.1 Indexing Strategy

```sql
-- users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_is_active ON users(is_active);

-- kycs
CREATE INDEX idx_kycs_status ON kycs(status);
CREATE INDEX idx_kycs_user_id ON kycs(user_id);

-- wallets
CREATE INDEX idx_wallets_user_id ON wallets(user_id);

-- wallet_transactions
CREATE INDEX idx_wallet_transactions_wallet_id ON wallet_transactions(wallet_id);
CREATE INDEX idx_wallet_transactions_type ON wallet_transactions(type);
CREATE INDEX idx_wallet_transactions_created_at ON wallet_transactions(created_at DESC);

-- loan_requests
CREATE INDEX idx_loan_requests_borrower_id ON loan_requests(borrower_id);
CREATE INDEX idx_loan_requests_status ON loan_requests(status);
CREATE INDEX idx_loan_requests_status_created ON loan_requests(status, created_at DESC);

-- loan_fundings
CREATE INDEX idx_loan_fundings_loan_id ON loan_fundings(loan_id);
CREATE INDEX idx_loan_fundings_lender_id ON loan_fundings(lender_id);

-- loan_installments
CREATE INDEX idx_loan_installments_loan_id ON loan_installments(loan_id);
CREATE INDEX idx_loan_installments_due_date ON loan_installments(due_date);
CREATE INDEX idx_loan_installments_status ON loan_installments(status);

-- notifications
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_read_at ON notifications(read_at);
```

### 4.2 Eloquent Model Relationships

```php
// User Model
class User extends Authenticatable {
    public function profile(): HasOne { return $this->hasOne(Profile::class); }
    public function kyc(): HasOne { return $this->hasOne(KYC::class); }
    public function wallets(): HasMany { return $this->hasMany(Wallet::class); }
    public function loanRequests(): HasMany { return $this->hasMany(LoanRequest::class, 'borrower_id'); }
    public function fundings(): HasMany { return $this->hasMany(LoanFunding::class, 'lender_id'); }
    public function notifications(): HasMany { return $this->hasMany(Notification::class); }
    public function roles(): BelongsToMany { return $this->belongsToMany(Role::class, 'role_user'); }
}

// LoanRequest Model
class LoanRequest extends Model {
    public function borrower(): BelongsTo { return $this->belongsTo(User::class, 'borrower_id'); }
    public function fundings(): HasMany { return $this->hasMany(LoanFunding::class, 'loan_id'); }
    public function agreement(): HasOne { return $this->hasOne(LoanAgreement::class, 'loan_id'); }
    public function installments(): HasMany { return $this->hasMany(LoanInstallment::class, 'loan_id'); }
    public function repayments(): HasMany { return $this->hasMany(LoanRepayment::class, 'loan_id'); }
    public function category(): BelongsTo { return $this->belongsTo(LoanCategory::class); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
}
```

---

## 5. Desain API & Routing

### 5.1 Web Routes Structure

```
GET  /                          → Landing page
GET  /marketplace               → Daftar loan open
GET  /marketplace/{id}          → Detail loan

AUTH Routes (middleware: guest)
GET  /register
POST /register
GET  /login
POST /login
POST /logout
GET  /forgot-password
POST /forgot-password
GET  /reset-password/{token}
POST /reset-password

Authenticated Routes (middleware: auth, verified)
GET  /dashboard

Profile
GET  /profile/edit
PUT  /profile/update

KYC
GET  /kyc
POST /kyc/submit

Wallet
GET  /wallet
POST /wallet/deposit
POST /wallet/withdraw
GET  /wallet/transactions

Loan (Borrower)
GET  /loans
GET  /loans/create
POST /loans
GET  /loans/{id}
PUT  /loans/{id}

Funding (Lender)
POST /marketplace/{id}/fund
GET  /my-fundings

Repayment (Borrower)
GET  /repayments
POST /repayments/{installmentId}/pay

Notifications
GET  /notifications
POST /notifications/{id}/read

Admin Routes (middleware: auth, role:admin)
GET  /admin
GET  /admin/kyc
POST /admin/kyc/{id}/approve
POST /admin/kyc/{id}/reject
GET  /admin/loans
POST /admin/loans/{id}/approve
POST /admin/loans/{id}/reject
GET  /admin/users
POST /admin/users/{id}/freeze
POST /admin/users/{id}/unfreeze
GET  /admin/settings
PUT  /admin/settings
```

### 5.2 Form Request Validation

```php
// Contoh: CreateLoanRequest
class CreateLoanRequest extends FormRequest {
    public function rules(): array {
        return [
            'amount'        => 'required|numeric|min:1000000|max:500000000',
            'interest_rate' => 'required|numeric|min:6|max:24',
            'duration'      => 'required|integer|min:3|max:60',
            'tenor_type'    => 'required|in:monthly,weekly',
            'purpose'       => 'required|string|max:500',
            'category_id'   => 'required|exists:loan_categories,id',
            'currency_id'   => 'required|exists:currencies,id',
            'collateral'    => 'nullable|string|max:500',
            'description'   => 'required|string|min:50|max:2000',
        ];
    }
}
```

---

## 6. Desain UI/UX

### 6.1 Color Palette & Design System

```css
/* Primary Colors */
--color-primary:      #4F46E5;   /* Indigo-600  — trust & financial */
--color-primary-dark: #3730A3;   /* Indigo-800  */
--color-secondary:    #10B981;   /* Emerald-500 — growth & success */
--color-danger:       #EF4444;   /* Red-500     — error & default */
--color-warning:      #F59E0B;   /* Amber-500   — warning & pending */

/* Neutral */
--color-gray-50:  #F9FAFB;
--color-gray-100: #F3F4F6;
--color-gray-900: #111827;

/* Typography */
--font-family: 'Inter', sans-serif;
```

### 6.2 Layout Utama (Authenticated User)

```
┌────────────────────────────────────────────────────────┐
│  HEADER: Logo | Nav Links | Notification Bell | Avatar │
├──────────┬─────────────────────────────────────────────┤
│          │                                             │
│ SIDEBAR  │            MAIN CONTENT AREA               │
│ (nav)    │                                             │
│          │                                             │
├──────────┴─────────────────────────────────────────────┤
│  FOOTER                                                │
└────────────────────────────────────────────────────────┘
```

### 6.3 Halaman-Halaman Utama

#### Landing Page (/)
- Hero section: tagline + CTA button (Pinjam Dana / Mulai Investasi)
- Stats: Total Loan Disalurkan, Total Lender, Total Borrower
- How It Works: 3-step infographic
- Testimonials
- FAQ

#### Marketplace (/marketplace)
- Search bar + Filter panel (Currency, Duration, Interest, Risk Grade)
- Loan cards grid: progress bar, interest badge, duration, risk grade
- Pagination

#### Dashboard (/dashboard)
- Borrower view: Loan summary, upcoming installments, wallet balance
- Lender view: Portfolio summary, active fundings, expected returns, wallet balance

#### Wallet (/wallet)
- Balance cards: Available / Hold / Total
- Quick actions: Deposit / Withdraw
- Transaction history table dengan filter

#### Admin Dashboard (/admin)
- KPI cards: Pending KYC, Pending Loans, Active Loans, Revenue
- Charts: Monthly volume, Loan status distribution
- Quick access table: Recent KYC submissions, Recent loan applications

### 6.4 Status Badge Color Guide

| Status          | Warna    | CSS Class              |
| --------------- | -------- | ---------------------- |
| pending         | Kuning   | badge-warning          |
| approved        | Hijau    | badge-success          |
| rejected        | Merah    | badge-danger           |
| open_funding    | Biru     | badge-info             |
| funded          | Indigo   | badge-primary          |
| active          | Hijau    | badge-success          |
| completed       | Abu-abu  | badge-secondary        |
| default         | Merah    | badge-danger           |
| cancelled       | Abu-abu  | badge-secondary        |

---

## 7. Desain Keamanan

### 7.1 Authentication Flow

```
User Login (email + password)
     │
     ▼
Validasi kredensial (Hash::check)
     │
     ▼
Buat session (Laravel Session Auth)
     │
     ▼
[Aksi Sensitif: withdraw/disbursement]
     │
     ▼
Request OTP → Kirim ke email → User input OTP
     │
     ▼
Verifikasi OTP (valid 5 menit) → Lanjut eksekusi
```

### 7.2 RBAC Implementation

```php
// Middleware: EnsureRoleMiddleware
class EnsureRoleMiddleware {
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
}

// Penggunaan di route:
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin routes
});

Route::middleware(['auth', 'role:borrower,admin'])->group(function () {
    // Borrower routes
});
```

### 7.3 File Upload Security (KYC)

```php
class KYCService {
    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'application/pdf'];
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    public function uploadDocument(UploadedFile $file, string $type): string {
        // 1. Validasi MIME type (bukan hanya ekstensi)
        if (!in_array($file->getMimeType(), self::ALLOWED_MIMES)) {
            throw new InvalidFileTypeException();
        }
        
        // 2. Validasi ukuran
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new FileTooLargeException();
        }
        
        // 3. Generate nama file aman (UUID, bukan nama asli user)
        $filename = Str::uuid() . '.' . $file->extension();
        
        // 4. Simpan di direktori non-public
        return $file->storeAs("kyc/{$type}", $filename, 'private');
    }
}
```

### 7.4 Database Security

- Selalu gunakan Eloquent ORM atau Query Builder (bukan raw SQL string concatenation).
- Gunakan `DB::transaction()` untuk semua operasi keuangan.
- Gunakan `lockForUpdate()` untuk mencegah race condition pada operasi wallet.

---

## 8. Desain Queue & Background Jobs

### 8.1 Queue Configuration

```env
QUEUE_CONNECTION=database  # Development
# QUEUE_CONNECTION=redis   # Production (recommended)
```

### 8.2 Daftar Jobs

| Job                               | Trigger                          | Deskripsi                                    |
| --------------------------------- | -------------------------------- | -------------------------------------------- |
| SendEmailJob                      | Various events                   | Generic email sending via Laravel Mail       |
| GenerateAgreementJob              | LoanFunded event                 | Generate PDF kontrak Loan Agreement          |
| ProcessRepaymentDistributionJob   | RepaymentPaid event              | Distribusi dana cicilan ke Lender            |
| NotifyInstallmentDueJob           | Scheduled (cron H-3, H-1)        | Kirim reminder jatuh tempo cicilan           |
| CheckOverdueInstallmentsJob       | Scheduled (cron daily)           | Mark installment overdue & hitung penalty    |

### 8.3 Scheduled Tasks (Cron)

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void {
    // Cek installment jatuh tempo setiap hari tengah malam
    $schedule->command('loans:check-overdue')->dailyAt('00:05');
    
    // Kirim reminder H-3 sebelum jatuh tempo
    $schedule->command('loans:send-reminder --days=3')->dailyAt('09:00');
    
    // Kirim reminder H-1 sebelum jatuh tempo
    $schedule->command('loans:send-reminder --days=1')->dailyAt('09:00');
}
```

---

## 9. Desain Events & Listeners

### 9.1 Event Map

```
Event                  → Listener(s)
───────────────────────────────────────────────────────────────
KYCApproved            → SendKYCApprovedNotification
                       → CreateDefaultWallet

LoanApproved           → SendLoanApprovedNotification
LoanRejected           → SendLoanRejectedNotification

LoanFunded             → GenerateAgreementJob (queued)
                       → SendLoanFundedNotification (to borrower)
                       → SendFundingSuccessNotification (to each lender)

LoanDisbursed          → UpdateLoanStatusToActive
                       → GenerateInstallmentSchedule

RepaymentPaid          → ProcessRepaymentDistributionJob (queued)
                       → UpdateInstallmentStatus
                       → SendRepaymentConfirmationNotification

InstallmentOverdue     → ApplyPenalty
                       → SendOverdueNotification
                       → CheckForDefaultStatus
```

### 9.2 Contoh Event & Listener

```php
// Event
class LoanFunded {
    public function __construct(
        public readonly LoanRequest $loan
    ) {}
}

// Listener
class GenerateLoanAgreement implements ShouldQueue {
    public function handle(LoanFunded $event): void {
        // Generate agreement, simpan ke storage, update status
        $this->agreementService->generate($event->loan);
    }
}
```

---

## 10. Tech Stack

### 10.1 Backend

| Komponen         | Teknologi                  | Keterangan                                  |
| ---------------- | -------------------------- | ------------------------------------------- |
| Framework        | PHP Laravel 11             | Modular Monolith                            |
| PHP Version      | PHP 8.3                    | -                                           |
| ORM              | Eloquent                   | Built-in Laravel                            |
| Authentication   | Laravel Auth (Session)     | Session-based untuk web                     |
| Authorization    | Laravel Policies + RBAC    | Custom role middleware                      |
| Queue            | Laravel Queue (DB/Redis)   | Background job processing                   |
| Mail             | Laravel Mail (Mailtrap)    | Development email testing                   |
| File Storage     | Laravel Filesystem (Local) | Configurable ke S3 via env                  |
| Testing          | PHPUnit + Laravel HTTP Test| Feature & Unit testing                      |

### 10.2 Frontend

| Komponen         | Teknologi                  | Keterangan                                  |
| ---------------- | -------------------------- | ------------------------------------------- |
| View Engine      | Laravel Blade              | Server-side rendering                       |
| CSS Framework    | Tailwind CSS               | Utility-first, clean & modern               |
| JS               | Alpine.js                  | Lightweight reactivity untuk interaksi UI   |
| Build Tool       | Vite                       | Fast asset bundling                         |
| Charts           | Chart.js                   | Dashboard analytics charts                  |

### 10.3 Infrastructure

| Komponen         | Teknologi                  | Keterangan                                  |
| ---------------- | -------------------------- | ------------------------------------------- |
| Database         | PostgreSQL 16              | Primary data store                          |
| Container        | Docker + Docker Compose    | Isolasi environment                         |
| Version Control  | Git + GitHub               | Repository: bntngridp/peer-lend             |

---

## 11. Deployment Design

### 11.1 Docker Compose (Development)

```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_PORT=5432

  db:
    image: postgres:16-alpine
    container_name: peer-lend-container-postgreySQL
    environment:
      POSTGRES_DB: peer-lend-db
      POSTGRES_PASSWORD: secretpassword
    ports:
      - "5433:5432"
    volumes:
      - peer_lend_pgdata:/var/lib/postgresql/data

  queue:
    build:
      context: .
    command: php artisan queue:work --sleep=3 --tries=3
    depends_on:
      - db

volumes:
  peer_lend_pgdata:
```

### 11.2 Environment Variables

```env
# Application
APP_NAME="Peer-Lend"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=peer-lend-db
DB_USERNAME=postgres
DB_PASSWORD=secretpassword

# Queue
QUEUE_CONNECTION=database

# Mail (Development)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="noreply@peer-lend.com"
MAIL_FROM_NAME="Peer-Lend"

# Storage
FILESYSTEM_DISK=local
```
