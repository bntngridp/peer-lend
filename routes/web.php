<?php

use App\Modules\Auth\Controllers\LoginController;
use App\Modules\Auth\Controllers\PasswordResetController;
use App\Modules\Auth\Controllers\RegisterController;
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
Route::middleware(['auth', 'verified'])->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard (placeholder — will be implemented per-role later)
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

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

    // 👑 Admin-Only Panel Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/kyc', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'index'])->name('kyc.index');
        Route::get('/kyc/{kyc}', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'show'])->name('kyc.show');
        Route::post('/kyc/{kyc}/approve', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'approve'])->name('kyc.approve');
        Route::post('/kyc/{kyc}/reject', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'reject'])->name('kyc.reject');
        
        // Secured streaming endpoint for private documents
        Route::get('/kyc/document/{document}', [\App\Modules\KYC\Controllers\AdminKYCController::class, 'streamDocument'])->name('kyc.document');
    });
});
