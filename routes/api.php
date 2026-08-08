<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;

/*
|--------------------------------------------------------------------------
| Public Web Routes (Tampilan Blade & Akses Awal)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Public API Routes (Autentikasi)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| Authenticated API Routes (Perlu Token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth & User Profile ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- Katalog Buku (Read Only: Semua Role) ---
    Route::get('/buku', [BukuController::class, 'index']);
    Route::get('/buku/{id}', [BukuController::class, 'show']);

    // --- Kategori Buku (Read Only: Semua Role) ---
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);

    // --- Transaksi Peminjaman (Umum / Anggota) ---
    // [Aturan #1, #2, #3, #5] Pengajuan pinjam & Riwayat peminjaman user
    Route::get('/peminjaman/history', [PeminjamanController::class, 'history']);
    Route::post('/peminjaman/pinjam', [PeminjamanController::class, 'pinjam']);
    Route::post('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan']);

    /*
    |--------------------------------------------------------------------------
    | Khusus Role: ADMIN & PETUGAS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas')->group(function () {
        // [Aturan #4, #5] Konfirmasi pengembalian & hitung denda otomatis oleh petugas/admin
        Route::post('/peminjaman/konfirmasi-kembali/{id}', [PeminjamanController::class, 'konfirmasiKembali']);
        
        // Monitoring seluruh transaksi peminjaman anggota
        Route::get('/peminjaman/semua', [PeminjamanController::class, 'indexSemuaPeminjaman']);
    });

    /*
    |--------------------------------------------------------------------------
    | Khusus Role: ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // Management Buku (Tambah & Edit)
        Route::post('/buku', [BukuController::class, 'store']);
        Route::put('/buku/{id}', [BukuController::class, 'update']);

        // Management Kategori (Tambah & Edit)
        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);

        // [Aturan #7] HANYA Admin yang dapat menghapus data buku & kategori
        Route::delete('/buku/{id}', [BukuController::class, 'destroy']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        // Management User / Anggota
        Route::get('/users', [AuthController::class, 'indexUsers']);
    });

});