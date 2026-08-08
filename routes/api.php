<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;

// ================= PUBLIC ROUTES =================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ================= AUTHENTICATED ROUTES =================
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Buku (Dapat diakses Semua Role)
    Route::get('/buku', [BukuController::class, 'index']);
    Route::get('/buku/{id}', [BukuController::class, 'show']);

    // Kategori (Dapat diakses Semua Role)
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);

    // Transaksi Peminjaman General
    Route::get('/peminjaman/history', [PeminjamanController::class, 'history']);
    Route::post('/peminjaman/pinjam', [PeminjamanController::class, 'pinjam']);
    Route::post('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan']);

    // ================= KHUSUS ADMIN =================
    Route::middleware('role:admin')->group(function () {
        // Kelola Buku
        Route::post('/buku', [BukuController::class, 'store']);
        Route::put('/buku/{id}', [BukuController::class, 'update']);
        Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

        // Kelola Kategori
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        // Kelola Users/Anggota
        Route::get('/users', [AuthController::class, 'indexUsers']);
    });

    // ================= ADMIN & PETUGAS =================
    Route::middleware('role:admin,petugas')->group(function () {
        // Terima Buku / Konfirmasi Pengembalian & Lihat Semua Peminjaman
        Route::post('/peminjaman/konfirmasi-kembali/{id}', [PeminjamanController::class, 'konfirmasiKembalikan']);
        Route::get('/peminjaman/semua', [PeminjamanController::class, 'indexSemuaPeminjaman']);
    });
});