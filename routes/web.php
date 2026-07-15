<?php

use App\Modules\Auth\Controllers\LoginController;
use App\Modules\Auth\Controllers\PasswordResetController;
use App\Modules\Auth\Controllers\RegisterController;
use App\Modules\Shared\Controllers\DashboardController;
use App\Modules\Shared\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// ─── Public Landing Page ──────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');

// ─── Auth Routes (Guest only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Login
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Forgot Password
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'two_factor'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard — role-based (admin / borrower / lender)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🔔 Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // 🔐 2-Factor Authentication (2FA) Routes
    Route::get('/2fa/setup', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::get('/2fa/verify', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
    Route::post('/2fa/verify', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'verify'])->name('2fa.verify.post');

    // 👤 User Profile Routes
    Route::get('/profile', [\App\Modules\User\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Modules\User\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // 🔍 KYC Verification Routes
    Route::get('/kyc', [\App\Modules\KYC\Controllers\KYCController::class, 'index'])->name('kyc.index');
    Route::post('/kyc', [\App\Modules\KYC\Controllers\KYCController::class, 'submit'])->name('kyc.submit');

    // 💳 Virtual Wallet Routes
    Route::get('/wallet', [\App\Modules\Wallet\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::middleware('kyc')->group(function () {
        Route::post('/wallet/deposit', [\App\Modules\Wallet\Controllers\WalletController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/wallet/withdraw', [\App\Modules\Wallet\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
    });

    // 🤝 P2P Loan Marketplace Routes
    Route::get('/marketplace', [\App\Modules\Loan\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/{loan}', [\App\Modules\Loan\Controllers\MarketplaceController::class, 'show'])->name('marketplace.show');
    Route::post('/marketplace/{loan}/fund', [\App\Modules\Loan\Controllers\MarketplaceController::class, 'fund'])
        ->middleware('kyc')
        ->name('marketplace.fund');

    // 📝 Borrower Loan Applications & Installment Routes
    Route::middleware('kyc')->group(function () {
        Route::get('/loans', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'create'])->name('loans.create');
        Route::post('/loans', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'store'])->name('loans.store');
        Route::get('/loans/{loan}/installments', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'installments'])->name('loans.installments');
        
        // Repayments
        Route::post('/repayments/{installment}/pay', [\App\Modules\Loan\Controllers\RepaymentController::class, 'pay'])->name('repayments.pay');
    });

    // 👑 Admin-Only Panel Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/kyc', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'index'])->name('kyc.index');
        Route::get('/kyc/{kyc}', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'show'])->name('kyc.show');
        Route::post('/kyc/{kyc}/approve', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'approve'])->name('kyc.approve');
        Route::post('/kyc/{kyc}/reject', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'reject'])->name('kyc.reject');
        
        // Secured streaming endpoint for private documents
        Route::get('/kyc/document/{document}', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'streamDocument'])->name('kyc.document');

        // Admin Loan Review & Disbursement
        Route::get('/loans', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/{loan}', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'show'])->name('loans.show');
        Route::post('/loans/{loan}/approve', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'approve'])->name('loans.approve');
        Route::post('/loans/{loan}/disburse', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'disburse'])->name('loans.disburse');
    });
});
