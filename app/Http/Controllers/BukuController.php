<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        return response()->json(Buku::with('kategori')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string',
            'penulis' => 'required|string',
            'penerbit' => 'required|string',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'tahun_terbit' => 'required|integer'
        ]);

        return response()->json(Buku::create($data), 201);
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }
        return response()->json($buku);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'judul' => 'sometimes|string',
            'penulis' => 'sometimes|string',
            'penerbit' => 'sometimes|string',
            'kategori_id' => 'sometimes|exists:kategori,id',
            'stok' => 'sometimes|integer|min:0',
            'tahun_terbit' => 'sometimes|integer'
        ]);

        $buku->update($data);
        return response()->json($buku);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }
        $buku->delete();
        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}