<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * 1. Tampilkan semua kategori (dengan fitur pencarian)
     */
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('search')) {
            $query->where('nama_kategori', 'like', "%{$request->search}%");
        }

        return response()->json($query->latest()->get());
    }

    /**
     * 2. Tambah Kategori Baru (Khusus Admin)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori'
        ]);

        $kategori = Kategori::create($data);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $kategori
        ], 201);
    }

    /**
     * 3. Detail Kategori beserta Buku terkait
     */
    public function show($id)
    {
        $kategori = Kategori::with('buku')->find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json($kategori);
    }

    /**
     * 4. Update Kategori (Khusus Admin)
     */
    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori,nama_kategori,' . $id
        ]);

        $kategori->update($data);

        return response()->json([
            'message' => 'Kategori berhasil diperbarui',
            'data'    => $kategori
        ]);
    }

    /**
     * 5. Hapus Kategori (Khusus Admin)
     */
    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        // Mencegah penghapusan jika kategori masih memiliki buku terkait
        if (method_exists($kategori, 'buku') && $kategori->buku()->count() > 0) {
            return response()->json([
                'message' => 'Kategori gagal dihapus karena masih digunakan oleh data buku!'
            ], 422);
        }

        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}