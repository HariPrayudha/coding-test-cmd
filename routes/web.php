<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanApplicationController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('demo-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Internal Financing Application Routes (Protected by Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [LoanApplicationController::class, 'index'])->name('loans.index');
    Route::post('/loans', [LoanApplicationController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}', [LoanApplicationController::class, 'show'])->name('loans.show');
    Route::post('/loans/{loan}/approve', [LoanApplicationController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanApplicationController::class, 'reject'])->name('loans.reject');
    Route::get('/api/calculate-preview', [LoanApplicationController::class, 'calculatePreview'])->name('loans.calculate-preview');
});
