<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\TransactionController;
use App\Models\BankStatement;

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/2fa/setup', [AuthController::class, 'show2faSetup'])->name('2fa.setup');
Route::post('/2fa/skip', [AuthController::class, 'skip2fa'])->name('2fa.skip');
Route::get('/2fa/verify', [AuthController::class, 'show2faVerify'])->name('2fa.verify');
Route::post('/2fa/verify', [AuthController::class, 'verify2fa']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/entities/create', [\App\Http\Controllers\EntityController::class, 'create'])->name('entities.create');
    Route::post('/entities', [\App\Http\Controllers\EntityController::class, 'store'])->name('entities.store');

    Route::post('/api/otp/send-email', [\App\Http\Controllers\OtpController::class, 'sendEmailOtp']);
    Route::post('/api/otp/verify-email', [\App\Http\Controllers\OtpController::class, 'verifyEmailOtp']);
    Route::post('/api/otp/send-mobile', [\App\Http\Controllers\OtpController::class, 'sendMobileOtp']);
    Route::post('/api/otp/verify-mobile', [\App\Http\Controllers\OtpController::class, 'verifyMobileOtp']);

    Route::post('/set-period', [\App\Http\Controllers\PeriodController::class, 'setPeriod'])->name('period.set');

    // Entity-scoped routes — requires active entity to belong to the user
    Route::middleware(['entity.auth'])->group(function () {
        Route::post('/upload-statement', [StatementController::class, 'upload'])->name('statement.upload');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');

        // Ledgers
        Route::get('/ledgers', [\App\Http\Controllers\LedgerController::class, 'index'])->name('ledgers.index');
        Route::get('/ledgers/create', [\App\Http\Controllers\LedgerController::class, 'create'])->name('ledgers.create');
        Route::post('/ledgers', [\App\Http\Controllers\LedgerController::class, 'store'])->name('ledgers.store');
        Route::post('/ledger-groups', [\App\Http\Controllers\LedgerController::class, 'storeGroup'])->name('ledger_groups.store');

        // Reports — Inputter role and above
        Route::middleware(['entity.role:Inputter'])->group(function () {
            Route::get('/reports/trial-balance', [\App\Http\Controllers\ReportController::class, 'trialBalance'])->name('reports.trial-balance');
            Route::get('/reports/profit-loss', [\App\Http\Controllers\ReportController::class, 'profitLoss'])->name('reports.profit-loss');
            Route::get('/reports/balance-sheet', [\App\Http\Controllers\ReportController::class, 'balanceSheet'])->name('reports.balance-sheet');
        });
    });
});
