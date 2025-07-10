<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Grup Rute Khusus untuk Admin dan Pemilik (Mengelola Barang)
Route::middleware(['auth', 'role:admin'])->group(function () { // Hanya admin dan pemilik
    Route::resource('barangs', BarangController::class);
});

Route::middleware(['auth', 'role:pemilik'])->group(function () { // Hanya admin dan pemilik
    Route::resource('barangs', BarangController::class);
});

// Grup Rute Khusus untuk Kasir dan Pemilik (Mengelola Transaksi)
Route::middleware(['auth', 'role:kasir'])->group(function () { // Hanya kasir dan pemilik
    Route::resource('transaksis', TransaksiController::class);
});

Route::middleware(['auth', 'role:pemilik'])->group(function () { // Hanya kasir dan pemilik
    Route::resource('transaksis', TransaksiController::class);
});

// Grup Rute Khusus untuk Pemilik (Mengelola Laporan)
Route::middleware(['auth', 'role:pemilik'])->group(function () { // Hanya pemilik
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/inventaris', [LaporanController::class, 'inventaris'])->name('laporan.inventaris');
});



require __DIR__.'/auth.php';
