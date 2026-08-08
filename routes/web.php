<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;

// ================= PUBLIC ROUTES =================
// 1. Redirect halaman utama '/' ke '/login'
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Tampilkan Halaman Login (login.blade.php)
Route::get('/login', function () {
    return view('login');
})->name('login');

// 3. Tampilkan Halaman Dashboard (dashboard.blade.php)
Route::get('/dashboard', function () {
    return view('dashboard');
});

// ================= AUTHENTICATED ROUTES =================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Katalog Buku (Read: Semua Role)
    Route::get('/buku', [BukuController::class, 'index']);
    Route::get('/buku/{id}', [BukuController::class, 'show']);

    // Kategori Buku (Read: Semua Role)
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);

    // Transaksi Peminjaman (General / Anggota)
    Route::get('/peminjaman/history', [PeminjamanController::class, 'history']);
    Route::post('/peminjaman/pinjam', [PeminjamanController::class, 'pinjam']);
    Route::post('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan']);

    // ================= KHUSUS ADMIN =================
    Route::middleware('role:admin')->group(function () {
        // Kelola Data Buku
        Route::post('/buku', [BukuController::class, 'store']);
        Route::put('/buku/{id}', [BukuController::class, 'update']);
        Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

        // Kelola Kategori
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        // Kelola & Lihat Daftar User / Anggota
        Route::get('/users', [AuthController::class, 'indexUsers']);
    });

    // ================= ADMIN & PETUGAS =================
    Route::middleware('role:admin,petugas')->group(function () {
        // Terima / Konfirmasi Pengembalian Buku & Pantau Semua Transaksi
        Route::post('/peminjaman/konfirmasi-kembali/{id}', [PeminjamanController::class, 'konfirmasiKembalikan']);
        Route::get('/peminjaman/semua', [PeminjamanController::class, 'indexSemuaPeminjaman']);
    });
});