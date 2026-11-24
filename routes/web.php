<?php

use App\Http\Controllers\ErdController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

// 1. Halaman Publik Pelanggan & ERD Interaktif
Route::get('/', [OrderController::class, 'index'])->name('customer.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('customer.checkout');
Route::get('/erd', [ErdController::class, 'index'])->name('erd.index');

// 2. Profil Akun (Semua Pengguna Terautentikasi)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. Area Operasional Petugas (Admin & Kasir)
Route::middleware(['auth', 'verified', 'can:operate-laundry'])->group(function () {
    Route::get('/dashboard', [OrderController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/admin/dashboard', [OrderController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('/admin/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('admin.orders.receipt');
    Route::get('/admin/report', [OrderController::class, 'report'])->name('admin.report');
    Route::get('/admin/services', [ServiceController::class, 'index'])->name('services.index');
});

// 4. Area Manajemen Tarif Layanan (Khusus Admin — Gate: manage-laundry)
Route::middleware(['auth', 'verified', 'can:manage-laundry'])->group(function () {
    Route::resource('/admin/services', ServiceController::class)->except(['index', 'show']);
});

require __DIR__ . '/auth.php';
