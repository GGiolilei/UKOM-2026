<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Default Users
        User::create([
            'nama' => 'Admin Bacapedia',
            'email' => 'admin@bacapedia.id',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        User::create([
            'nama' => 'Petugas Perpustakaan',
            'email' => 'petugas@bacapedia.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas'
        ]);

        User::create([
            'nama' => 'Budi Anggota',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'anggota'
        ]);

        // Default Kategori
        $fiksi = Kategori::create(['nama_kategori' => 'Fiksi']);
        $teknologi = Kategori::create(['nama_kategori' => 'Teknologi']);

        // Default Buku
        Buku::create([
            'judul' => 'Pemrograman Laravel untuk Pemula',
            'penulis' => 'John Doe',
            'penerbit' => 'Informatika',
            'kategori_id' => $teknologi->id,
            'stok' => 5,
            'tahun_terbit' => 2024
        ]);

        Buku::create([
            'judul' => 'Laskar Pelangi',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'kategori_id' => $fiksi->id,
            'stok' => 2,
            'tahun_terbit' => 2005
        ]);
    }
}