<?php

use App\Modules\Auth\Controllers\LoginController;
use App\Modules\Auth\Controllers\PasswordResetController;
use App\Modules\Auth\Controllers\RegisterController;
use App\Modules\Shared\Controllers\DashboardController;
use App\Modules\Shared\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// ─── Public Landing Page ──────────────────────────────────────────────────────
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/terms', fn () => redirect()->route('register', ['modal' => 'terms']))->name('terms.show');
Route::get('/privacy', fn () => redirect()->route('register', ['modal' => 'privacy']))->name('privacy.show');
Route::post('/api/payment/webhook', [\App\Modules\Wallet\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');
Route::post('/api/payment/xendit/webhook', [\App\Modules\Wallet\Controllers\PaymentController::class, 'xenditWebhook'])->name('payment.xendit.webhook');
Route::post('/api/payment/nowpayments/ipn', [\App\Modules\Wallet\Controllers\PaymentController::class, 'nowpaymentsIpn'])->name('payment.nowpayments.ipn');
Route::get('/api/docs', fn () => view('docs.swagger'))->name('api.docs');
Route::get('/lang/{locale}', [\App\Modules\Shared\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

// ─── 🧮 Loan Calculator (Public — No Auth Required) ──────────────────────────
Route::get('/calculator', [\App\Modules\Loan\Controllers\LoanCalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculator/calculate', [\App\Modules\Loan\Controllers\LoanCalculatorController::class, 'calculate'])->name('calculator.calculate');


// ─── Auth Routes (Guest only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    // Register & OTP Verification
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/register/verify-otp', [RegisterController::class, 'showOtpForm'])->name('register.otp');
    Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
    Route::post('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');

    // Login
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Forgot Password
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
    // Verification notice fallback
    Route::get('/email/verify', fn () => redirect()->route('login'))->name('verification.notice');

    // Google OAuth Routes
    Route::get('/auth/google', [\App\Modules\Auth\Controllers\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [\App\Modules\Auth\Controllers\GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'two_factor'])->group(function () {
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
    Route::post('/2fa/disable', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/verify', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
    Route::post('/2fa/verify', [\App\Modules\Auth\Controllers\TwoFactorController::class, 'verify'])->name('2fa.verify.post');

    // 👤 User Profile Routes
    Route::get('/profile', [\App\Modules\User\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Modules\User\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Modules\User\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/notifications', [\App\Modules\User\Controllers\NotificationPreferenceController::class, 'update'])->name('profile.notifications.update');
    Route::put('/profile/system', [\App\Modules\User\Controllers\SystemPreferenceController::class, 'update'])->name('profile.system.update');
    Route::post('/theme/toggle', [\App\Modules\User\Controllers\SystemPreferenceController::class, 'toggleTheme'])->name('theme.toggle');
    Route::post('/profile/tokens', [\App\Modules\User\Controllers\ApiTokenController::class, 'store'])->name('profile.tokens.store');
    Route::delete('/profile/tokens/{token}', [\App\Modules\User\Controllers\ApiTokenController::class, 'destroy'])->name('profile.tokens.destroy');
    Route::post('/profile/sessions/revoke-others', [\App\Modules\User\Controllers\ProfileController::class, 'revokeOtherSessions'])->name('profile.sessions.revoke-others');
    Route::delete('/profile/sessions/{sessionId}', [\App\Modules\User\Controllers\ProfileController::class, 'revokeSession'])->name('profile.sessions.destroy');

    // 🔍 KYC Verification Routes
    Route::get('/kyc', [\App\Modules\KYC\Controllers\KYCController::class, 'index'])->name('kyc.index');
    Route::post('/kyc', [\App\Modules\KYC\Controllers\KYCController::class, 'submit'])->name('kyc.submit');

    // 💳 Virtual Wallet Routes
    Route::get('/wallet', [\App\Modules\Wallet\Controllers\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/collateral', [\App\Modules\Loan\Controllers\CollateralDashboardController::class, 'index'])->name('collateral.index');
    Route::middleware('kyc')->group(function () {
        Route::post('/wallet/deposit', [\App\Modules\Wallet\Controllers\WalletController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/wallet/deposit/initiate', [\App\Modules\Wallet\Controllers\PaymentController::class, 'initiateDeposit'])->name('wallet.deposit.initiate');
        Route::post('/wallet/withdraw', [\App\Modules\Wallet\Controllers\WalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::post('/wallet/withdraw/initiate', [\App\Modules\Wallet\Controllers\PaymentController::class, 'initiateWithdrawal'])->name('wallet.withdraw.initiate');
        Route::post('/wallet/crypto/deposit/initiate', [\App\Modules\Wallet\Controllers\PaymentController::class, 'initiateCryptoDeposit'])->name('wallet.crypto.deposit.initiate');
        Route::post('/wallet/crypto/withdraw/initiate', [\App\Modules\Wallet\Controllers\PaymentController::class, 'initiateCryptoWithdrawal'])->name('wallet.crypto.withdraw.initiate');
    });

    // 🤝 P2P Loan Marketplace Routes (Lender & Admin)
    Route::middleware('role:lender,admin')->group(function () {
        Route::get('/marketplace', [\App\Modules\Loan\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
        Route::get('/marketplace/{loan}', [\App\Modules\Loan\Controllers\MarketplaceController::class, 'show'])->name('marketplace.show');
        Route::post('/marketplace/{loan}/fund', [\App\Modules\Loan\Controllers\MarketplaceController::class, 'fund'])
            ->middleware('kyc')
            ->name('marketplace.fund');

        // 🤖 Auto-Invest settings update
        Route::post('/loans/auto-invest', [\App\Modules\Loan\Controllers\AutoInvestRuleController::class, 'update'])
            ->middleware('kyc')
            ->name('loans.auto-invest.update');
    });

    // 📝 Borrower Loan Applications & Installment Routes
    Route::middleware(['role:borrower,admin', 'kyc'])->group(function () {
        Route::get('/loans', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'index'])->name('loans.index');
        Route::get('/loans/create', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'create'])->name('loans.create');
        Route::post('/loans', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'store'])->name('loans.store');
    });

    // 🤝 Shared Loan Detail, Installments, Repayments, Chat & Contract Routes (Lender, Borrower & Admin)
    Route::middleware(['role:borrower,lender,admin', 'kyc'])->group(function () {
        Route::get('/loans/{loan}/installments', [\App\Modules\Loan\Controllers\LoanRequestController::class, 'installments'])->name('loans.installments');
        
        // Repayments
        Route::post('/repayments/{installment}/pay', [\App\Modules\Loan\Controllers\RepaymentController::class, 'pay'])->name('repayments.pay');

        // 💬 Internal Loan Chat System
        Route::get('/loans/{loan}/messages', [\App\Modules\Loan\Controllers\LoanMessageController::class, 'fetchMessages'])->name('loans.messages.fetch');
        Route::post('/loans/{loan}/messages', [\App\Modules\Loan\Controllers\LoanMessageController::class, 'sendMessage'])->name('loans.messages.send');

        // 📄 Contract Agreement PDF/Print Page
        Route::get('/loans/{loan}/agreement', [\App\Modules\Loan\Controllers\AgreementDownloadController::class, 'download'])->name('loans.agreement');

        // 🔌 REST API Endpoints (Prefix /api/v1)
        Route::prefix('api/v1')->name('api.v1.')->group(function () {
            Route::get('/marketplace', [\App\Modules\Loan\Controllers\LoanApiController::class, 'index'])->name('marketplace.index');
            Route::get('/marketplace/{loan}', [\App\Modules\Loan\Controllers\LoanApiController::class, 'show'])->name('marketplace.show');
            Route::post('/loans/apply', [\App\Modules\Loan\Controllers\LoanApiController::class, 'apply'])
                ->middleware('kyc')
                ->name('loans.apply');
        });
    });

    // 👑 Internal Staff Panel Routes (Admin, Customer Service, Collection Officer)
    Route::prefix('admin')->name('admin.')->group(function () {
        // KYC Verification & User Directory (Admin & CS)
        Route::middleware('role:admin,customer_service')->group(function () {
            Route::get('/kyc', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'index'])->name('kyc.index');
            Route::get('/kyc/{kyc}', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'show'])->name('kyc.show');
            Route::post('/kyc/{kyc}/approve', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'approve'])->name('kyc.approve');
            Route::post('/kyc/{kyc}/reject', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'reject'])->name('kyc.reject');
            Route::get('/kyc/document/{document}', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'streamDocument'])->name('kyc.document');
            Route::get('/users', [\App\Modules\KYC\Controllers\AdminGovernanceController::class, 'users'])->name('users.index');
        });

        // Admin Loan Review (Admin, CS & Collection Officer)
        Route::middleware('role:admin,customer_service,collection_officer')->group(function () {
            Route::get('/loans', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'index'])->name('loans.index');
            Route::get('/loans/{loan}', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'show'])->name('loans.show');
            Route::post('/loans/{loan}/approve', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'approve'])->name('loans.approve');
            Route::post('/loans/{loan}/disburse', [\App\Modules\Loan\Controllers\AdminLoanController::class, 'disburse'])->name('loans.disburse');
        });

        // Transaction Audit (Admin & Collection Officer)
        Route::middleware('role:admin,collection_officer')->group(function () {
            Route::get('/transactions', [\App\Modules\KYC\Controllers\AdminGovernanceController::class, 'transactions'])->name('transactions.index');
        });

        // Superadmin Governance & Financial Suite (Admin Only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/financials', [\App\Modules\KYC\Controllers\AdminGovernanceController::class, 'financials'])->name('financials.index');
            Route::get('/roles', [\App\Modules\KYC\Controllers\AdminGovernanceController::class, 'roles'])->name('roles.index');
            Route::get('/analytics', [\App\Modules\KYC\Controllers\AdminGovernanceController::class, 'analytics'])->name('analytics.index');
        });
    });
});
