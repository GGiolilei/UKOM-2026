<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * 1. Tampilkan semua daftar buku (dengan fitur pencarian & filter kategori)
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        // Fitur Pencarian (Judul, Penulis, Penerbit)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // Fitur Filter Kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        return response()->json($query->latest()->get());
    }

    /**
     * 2. Tambah Data Buku Baru (Khusus Admin)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'        => 'required|string|max:255',
            'penulis'      => 'required|string|max:255',
            'penerbit'     => 'required|string|max:255',
            'kategori_id'  => 'required|exists:kategori,id',
            'stok'         => 'required|integer|min:0',
            'tahun_terbit' => 'required|integer|digits:4|max:' . date('Y')
        ]);

        $buku = Buku::create($data);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data'    => $buku->load('kategori')
        ], 201);
    }

    /**
     * 3. Detail Buku Berdasarkan ID
     */
    public function show($id)
    {
        $buku = Buku::with('kategori')->find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json($buku);
    }

    /**
     * 4. Update Data Buku (Khusus Admin)
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'judul'        => 'sometimes|string|max:255',
            'penulis'      => 'sometimes|string|max:255',
            'penerbit'     => 'sometimes|string|max:255',
            'kategori_id'  => 'sometimes|exists:kategori,id',
            'stok'         => 'sometimes|integer|min:0',
            'tahun_terbit' => 'sometimes|integer|digits:4|max:' . date('Y')
        ]);

        $buku->update($data);

        return response()->json([
            'message' => 'Data buku berhasil diperbarui',
            'data'    => $buku->load('kategori')
        ]);
    }

    /**
     * 5. Hapus Data Buku (Khusus Admin)
     */
    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        // Mencegah penghapusan jika buku sedang dipinjam
        if (method_exists($buku, 'peminjaman') && $buku->peminjaman()->where('status', 'dipinjam')->exists()) {
            return response()->json([
                'message' => 'Buku gagal dihapus karena sedang dipinjam oleh anggota!'
            ], 422);
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}