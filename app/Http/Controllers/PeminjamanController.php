<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function pinjam(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:buku,id'
        ]);

        $user = $request->user();
        $buku = Buku::findOrFail($request->buku_id);

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

        // Aturan 3: Tenggat waktu 7 hari
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => Carbon::now()->toDateString(),
            'tanggal_jatuh_tempo' => Carbon::now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
            'denda' => 0
        ]);

        return response()->json(['message' => 'Peminjaman berhasil', 'data' => $peminjaman], 201);
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::find($id);

        if (!$peminjaman) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }

        if ($peminjaman->status === 'dikembalikan') {
            return response()->json(['message' => 'Buku sudah dikembalikan sebelumnya'], 400);
        }

        $tglKembali = Carbon::now();
        $tglJatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo);
        $denda = 0;

        // Aturan 4: Denda Rp 2.000 / hari jika terlambat
        if ($tglKembali->gt($tglJatuhTempo)) {
            $lateDays = $tglKembali->diffInDays($tglJatuhTempo);
            $denda = $lateDays * 2000;
        }

        $peminjaman->update([
            'tanggal_kembali' => $tglKembali->toDateString(),
            'status' => 'dikembalikan',
            'denda' => $denda
        ]);

        // Restock Buku
        Buku::where('id', $peminjaman->buku_id)->increment('stok');

        return response()->json([
            'message' => 'Pengembalian berhasil dicatat',
            'denda' => $denda,
            'data' => $peminjaman
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'petugas'])) {
            return response()->json(Peminjaman::with(['user', 'buku'])->get());
        }

        return response()->json(Peminjaman::where('user_id', $user->id)->with('buku')->get());
    }
}