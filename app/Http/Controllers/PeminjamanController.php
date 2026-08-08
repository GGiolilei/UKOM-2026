<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class PeminjamanController extends Controller
{
    /**
     * 1. Pengajuan Peminjaman Buku (Anggota)
     */
    public function pinjam(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id'
        ]);

        $user = $request->user();

        try {
            return DB::transaction(function () use ($user, $request) {
                // Lock row buku untuk menghindari race condition stok
                $buku = Buku::lockForUpdate()->findOrFail($request->buku_id);

                // Aturan 1: Stok tersedia
                if ($buku->stok <= 0) {
                    return response()->json(['message' => 'Stok buku habis!'], 422);
                }

                // Aturan 2: Maksimal 3 buku dipinjam bersamaan
                $activeLoans = Peminjaman::where('user_id', $user->id)
                    ->where('status', 'dipinjam')
                    ->count();

                if ($activeLoans >= 3) {
                    return response()->json(['message' => 'Batas maksimal peminjaman (3 buku) telah tercapai.'], 422);
                }

                // Kurangi Stok Buku
                $buku->decrement('stok');

                // Tenggat waktu 7 hari
                $tglPinjam = Carbon::now();
                $tglJatuhTempo = Carbon::now()->addDays(7);

                $peminjaman = Peminjaman::create([
                    'user_id'             => $user->id,
                    'buku_id'             => $buku->id,
                    'tanggal_pinjam'      => $tglPinjam->toDateString(),
                    'tanggal_jatuh_tempo' => $tglJatuhTempo->toDateString(),
                    'status'              => 'dipinjam',
                    'denda'               => 0
                ]);

                return response()->json([
                    'message' => 'Peminjaman berhasil dicatat',
                    'data'    => $peminjaman->load('buku')
                ], 201);
            });
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses peminjaman buku',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. Pengembalian Buku oleh Anggota
     */
    public function kembalikan(Request $request, $id)
    {
        $user = $request->user();
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }

        // Cek kepemilikan jika user adalah anggota biasa
        if ($user->role === 'anggota' && $peminjaman->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($peminjaman->status === 'dikembalikan') {
            return response()->json(['message' => 'Buku sudah dikembalikan sebelumnya'], 400);
        }

        return $this->prosesPengembalian($peminjaman);
    }

    /**
     * 3. Konfirmasi Pengembalian Buku oleh Admin/Petugas
     */
    public function konfirmasiKembali($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }

        if ($peminjaman->status === 'dikembalikan') {
            return response()->json(['message' => 'Buku sudah dikembalikan sebelumnya'], 400);
        }

        return $this->prosesPengembalian($peminjaman);
    }

    /**
     * Helper Method: Logika Pengembalian & Perhitungan Denda
     */
    private function prosesPengembalian($peminjaman)
    {
        try {
            return DB::transaction(function () use ($peminjaman) {
                $tglKembali = Carbon::now()->startOfDay();
                $tglJatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
                $denda = 0;

                // Hitung denda jika hari ini melewati tanggal jatuh tempo
                if ($tglKembali->gt($tglJatuhTempo)) {
                    $lateDays = (int) $tglJatuhTempo->diffInDays($tglKembali);
                    $denda = $lateDays * 2000; // Rp 2.000 / hari
                }

                // Update record peminjaman (menggunakan tanggal_pengembalian / tanggal_kembali)
                $peminjaman->update([
                    'tanggal_pengembalian' => $tglKembali->toDateString(),
                    'tanggal_kembali'      => $tglKembali->toDateString(), // fallback jika nama kolom berbeda
                    'status'               => 'dikembalikan',
                    'denda'                => $denda
                ]);

                // Restock Buku (+1 saat dikembalikan)
                Buku::where('id', $peminjaman->buku_id)->increment('stok');

                return response()->json([
                    'message' => 'Pengembalian berhasil dicatat',
                    'denda'   => $denda,
                    'data'    => $peminjaman->fresh(['user', 'buku'])
                ], 200);
            });
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses pengembalian buku',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 4. Riwayat Peminjaman User / Anggota
     */
    public function history(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'petugas'])) {
            return response()->json(Peminjaman::with(['user', 'buku'])->latest()->get());
        }

        return response()->json(
            Peminjaman::where('user_id', $user->id)
                ->with('buku')
                ->latest()
                ->get()
        );
    }

    /**
     * 5. Lihat Semua Transaksi Peminjaman (Admin & Petugas)
     */
    public function indexSemuaPeminjaman()
    {
        $peminjaman = Peminjaman::with(['user', 'buku'])->latest()->get();

        return response()->json($peminjaman);
    }
}