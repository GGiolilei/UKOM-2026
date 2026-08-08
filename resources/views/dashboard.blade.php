<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacapedia - Dashboard Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 font-sans min-h-screen text-slate-100 antialiased selection:bg-indigo-500 selection:text-white" x-data="dashboardApp()">

    <!-- Header Navbar -->
    <header class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center">
            <!-- Brand Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-violet-400 flex items-center justify-center font-black text-white text-base shadow-lg shadow-indigo-500/25 ring-1 ring-white/20">
                    BP
                </div>
                <div>
                    <h1 class="text-lg font-bold bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent leading-none">Bacapedia</h1>
                    <p class="text-[10px] text-slate-400 font-medium tracking-wide uppercase mt-0.5">Library Management Hub</p>
                </div>
            </div>

            <!-- Profile & Logout -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-3 bg-slate-800/60 border border-slate-700/50 rounded-full py-1 px-3.5">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-700 to-slate-800 border border-slate-600 flex items-center justify-center text-xs font-bold text-slate-200 uppercase" x-text="user.nama ? user.nama.charAt(0) : 'U'">
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-semibold text-slate-200 leading-tight" x-text="user.nama"></p>
                        <span :class="{
                            'from-purple-500 to-indigo-500 text-purple-100 border-purple-400/30': user.role === 'admin',
                            'from-blue-500 to-cyan-500 text-blue-100 border-blue-400/30': user.role === 'petugas',
                            'from-emerald-500 to-teal-500 text-emerald-100 border-emerald-400/30': user.role === 'anggota'
                        }" class="bg-gradient-to-r text-[9px] px-2 py-0.2 rounded-full font-bold uppercase border shadow-sm inline-block" x-text="user.role"></span>
                    </div>
                </div>

                <button @click="logout()" class="inline-flex items-center gap-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 border border-red-500/20 text-xs px-3.5 py-2 rounded-xl transition duration-200 font-semibold active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- ================= 1. ADMIN DASHBOARD ================= -->
        <template x-if="user.role === 'admin'">
            <div class="space-y-8">
                <!-- Hero Banner -->
                <div class="relative overflow-hidden bg-gradient-to-r from-purple-900/80 via-indigo-900/60 to-slate-900 border border-purple-500/20 p-6 sm:p-8 rounded-3xl shadow-2xl backdrop-blur-xl">
                    <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <div class="inline-flex items-center gap-2 bg-purple-500/10 border border-purple-500/20 px-3 py-1 rounded-full text-purple-300 text-xs font-semibold mb-3">
                                <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span> Panel Administrator
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Kelola Perpustakaan Terpadu</h2>
                            <p class="text-slate-300 text-sm mt-1 max-w-xl">Tambah entri buku baru, atur kategori katalog, dan pantau seluruh sirkulasi peminjaman user dalam satu tampilan.</p>
                        </div>
                        <button @click="showAddModal = !showAddModal" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-semibold text-xs px-5 py-3 rounded-xl shadow-lg shadow-purple-500/20 transition active:scale-95 flex items-center gap-2 border border-purple-400/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span x-text="showAddModal ? 'Tutup Form' : 'Tambah Buku Baru'"></span>
                        </button>
                    </div>
                </div>

                <!-- Form Tambah Buku -->
                <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-purple-500/30 p-6 rounded-2xl shadow-xl space-y-4">
                    <h3 class="font-bold text-slate-100 border-b border-slate-800 pb-3 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Formulir Tambah Buku Baru
                    </h3>
                    <form @submit.prevent="addBook()" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 mb-1 block">Judul Buku</label>
                            <input x-model="bookForm.judul" placeholder="Masukkan judul buku" required class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 placeholder-slate-600 transition">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 mb-1 block">Penulis</label>
                            <input x-model="bookForm.penulis" placeholder="Nama penulis" required class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 placeholder-slate-600 transition">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 mb-1 block">Penerbit</label>
                            <input x-model="bookForm.penerbit" placeholder="Nama penerbit" required class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 placeholder-slate-600 transition">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 mb-1 block">Kategori</label>
                            <select x-model="bookForm.kategori_id" required class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 transition">
                                <option value="">-- Pilih Kategori --</option>
                                <template x-for="k in kategoriList" :key="k.id">
                                    <option :value="k.id" x-text="k.nama_kategori"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 mb-1 block">Jumlah Stok</label>
                            <input x-model="bookForm.stok" type="number" placeholder="Jumlah stok" min="1" required class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 placeholder-slate-600 transition">
                        </div>
                        <div>
                            <label class="text-[11px] font-semibold text-slate-400 mb-1 block">Tahun Terbit</label>
                            <input x-model="bookForm.tahun_terbit" type="number" placeholder="Contoh: 2024" required class="w-full bg-slate-950 border border-slate-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 placeholder-slate-600 transition">
                        </div>
                        <div class="md:col-span-3 pt-2">
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs py-3 rounded-xl shadow-lg shadow-purple-600/20 transition active:scale-[0.99]">
                                Simpan Buku Baru
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabel Seluruh Peminjaman -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-100 text-base">Semua Transaksi Peminjaman</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Daftar riwayat lengkap seluruh pengajuan dan status peminjaman anggota.</p>
                        </div>
                        <input x-model="searchQuery" placeholder="Cari nama / buku..." class="bg-slate-950 border border-slate-800 text-xs px-3.5 py-2 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-purple-500 w-full sm:w-60">
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                                <tr>
                                    <th class="p-3.5 pl-5">Peminjam</th>
                                    <th class="p-3.5">Buku</th>
                                    <th class="p-3.5">Tgl Pinjam</th>
                                    <th class="p-3.5">Jatuh Tempo</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5 pr-5">Denda</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <template x-for="p in filteredHistory()" :key="p.id">
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="p-3.5 pl-5 font-semibold text-slate-200" x-text="p.user ? p.user.nama : '-'"></td>
                                        <td class="p-3.5 font-medium text-purple-300" x-text="p.buku ? p.buku.judul : '-'"></td>
                                        <td class="p-3.5 text-slate-400" x-text="p.tanggal_pinjam"></td>
                                        <td class="p-3.5 text-slate-400" x-text="p.tanggal_jatuh_tempo"></td>
                                        <td class="p-3.5">
                                            <span :class="p.status === 'dipinjam' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'" class="px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider" x-text="p.status"></span>
                                        </td>
                                        <td class="p-3.5 pr-5 font-bold" 
                                            :class="calculateDenda(p) > 0 ? 'text-red-400' : 'text-slate-500'" 
                                            x-text="'Rp ' + calculateDenda(p).toLocaleString('id-ID')">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>


        <!-- ================= 2. PETUGAS DASHBOARD ================= -->
        <template x-if="user.role === 'petugas'">
            <div class="space-y-8">
                <!-- Hero Banner -->
                <div class="relative overflow-hidden bg-gradient-to-r from-blue-900/80 via-indigo-900/60 to-slate-900 border border-blue-500/20 p-6 sm:p-8 rounded-3xl shadow-2xl backdrop-blur-xl">
                    <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 px-3 py-1 rounded-full text-blue-300 text-xs font-semibold mb-3">
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span> Panel Petugas
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Layanan Sirkulasi Perpustakaan</h2>
                        <p class="text-slate-300 text-sm mt-1 max-w-xl">Proses verifikasi pengembalian fisik buku dan kalkulasi otomatis denda keterlambatan anggota secara presisi.</p>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="font-bold text-slate-100 text-base">Daftar Peminjaman Aktif</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Kelola dan terima pengembalian buku yang sedang dipinjam anggota.</p>
                        </div>
                        <input x-model="searchQuery" placeholder="Cari nama / buku..." class="bg-slate-950 border border-slate-800 text-xs px-3.5 py-2 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500 w-full sm:w-60">
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                                <tr>
                                    <th class="p-3.5 pl-5">Nama Anggota</th>
                                    <th class="p-3.5">Judul Buku</th>
                                    <th class="p-3.5">Jatuh Tempo</th>
                                    <th class="p-3.5">Status</th>
                                    <th class="p-3.5">Perkiraan Denda</th>
                                    <th class="p-3.5 pr-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 text-slate-300">
                                <template x-for="p in filteredHistory()" :key="p.id">
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="p-3.5 pl-5 font-semibold text-slate-200" x-text="p.user ? p.user.nama : '-'"></td>
                                        <td class="p-3.5 font-medium text-blue-300" x-text="p.buku ? p.buku.judul : '-'"></td>
                                        <td class="p-3.5 text-slate-400" x-text="p.tanggal_jatuh_tempo"></td>
                                        <td class="p-3.5">
                                            <span :class="p.status === 'dipinjam' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'" class="px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase tracking-wider" x-text="p.status"></span>
                                        </td>
                                        <td class="p-3.5 font-bold" 
                                            :class="calculateDenda(p) > 0 ? 'text-red-400' : 'text-slate-500'" 
                                            x-text="'Rp ' + calculateDenda(p).toLocaleString('id-ID')">
                                        </td>
                                        <td class="p-3.5 pr-5 text-right">
                                            <template x-if="p.status === 'dipinjam'">
                                                <button @click="returnBook(p.id)" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-md shadow-blue-600/20 transition active:scale-95">Proses Kembalikan</button>
                                            </template>
                                            <template x-if="p.status === 'dikembalikan'">
                                                <span class="text-slate-500 italic text-[11px] font-medium">Selesai</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>


        <!-- ================= 3. ANGGOTA DASHBOARD ================= -->
        <template x-if="user.role === 'anggota'">
            <div class="space-y-8">
                <!-- Hero Banner -->
                <div class="relative overflow-hidden bg-gradient-to-r from-emerald-900/80 via-teal-900/60 to-slate-900 border border-emerald-500/20 p-6 sm:p-8 rounded-3xl shadow-2xl backdrop-blur-xl">
                    <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full text-emerald-300 text-xs font-semibold mb-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Ruang Anggota
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog & Koleksi Peminjaman</h2>
                        <p class="text-slate-300 text-sm mt-1 max-w-xl">Temukan buku favoritmu, ajukan peminjaman secara mandiri, dan pantau tenggat pengembalianmu.</p>
                    </div>
                </div>

                <!-- Riwayat Peminjaman Saya -->
                <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl space-y-4">
                    <h3 class="font-bold text-slate-100 border-b border-slate-800 pb-3 text-base flex items-center justify-between">
                        <span>Riwayat Peminjaman Saya</span>
                        <span class="text-xs font-normal text-slate-400" x-text="historyList.length + ' Item'"></span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="p in historyList" :key="p.id">
                            <div class="bg-slate-950 border border-slate-800/80 p-4 rounded-xl flex justify-between items-center hover:border-slate-700 transition">
                                <div class="space-y-1">
                                    <h4 class="font-bold text-sm text-slate-200" x-text="p.buku ? p.buku.judul : '-'"></h4>
                                    <p class="text-[11px] text-slate-400" x-text="'Jatuh Tempo: ' + p.tanggal_jatuh_tempo"></p>
                                    <span :class="p.status === 'dipinjam' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'" class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase border" x-text="p.status"></span>
                                </div>
                                <div class="text-right pl-4">
                                    <p class="text-[10px] text-slate-500 font-medium">Denda</p>
                                    <p class="text-xs font-bold" 
                                       :class="calculateDenda(p) > 0 ? 'text-red-400' : 'text-slate-400'" 
                                       x-text="'Rp ' + calculateDenda(p).toLocaleString('id-ID')"></p>
                                </div>
                            </div>
                        </template>
                        <template x-if="historyList.length === 0">
                            <p class="text-xs text-slate-500 italic col-span-2 py-4 text-center">Belum ada riwayat peminjaman.</p>
                        </template>
                    </div>
                </div>
            </div>
        </template>


        <!-- ================= KATALOG BUKU (SEMUA ROLE) ================= -->
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-800 pb-4">
                <div>
                    <h3 class="font-bold text-slate-100 text-lg">Katalog Seluruh Buku</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Jelajahi koleksi perpustakaan yang tersedia secara fleksibel.</p>
                </div>
                <!-- Controls Filter / Search -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <input x-model="bookSearch" placeholder="Cari judul / penulis..." class="bg-slate-950 border border-slate-800 text-xs px-3.5 py-2 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-full sm:w-64">
                </div>
            </div>

            <!-- Grid Cards Buku -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <template x-for="b in filteredBooks()" :key="b.id">
                    <div class="bg-slate-950 border border-slate-800 hover:border-slate-700/80 rounded-2xl p-4 flex flex-col justify-between group transition duration-200 hover:-translate-y-1 shadow-md hover:shadow-indigo-500/5">
                        <div class="space-y-3">
                            <div class="h-28 rounded-xl bg-gradient-to-br from-indigo-900/40 via-purple-900/20 to-slate-900 border border-slate-800 flex items-center justify-center relative overflow-hidden group-hover:border-indigo-500/30 transition">
                                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/10 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                                <svg class="w-10 h-10 text-slate-700 group-hover:text-indigo-400/60 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span class="absolute top-2 left-2 text-[9px] bg-slate-900/90 backdrop-blur border border-slate-700/50 px-2 py-0.5 rounded-full font-medium text-indigo-300" x-text="b.kategori ? b.kategori.nama_kategori : 'Umum'"></span>
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-100 text-sm line-clamp-1 group-hover:text-indigo-300 transition" x-text="b.judul"></h4>
                                <p class="text-xs text-slate-400 mt-0.5" x-text="'Penulis: ' + b.penulis"></p>
                                <p class="text-[11px] text-slate-500" x-text="'Penerbit: ' + b.penerbit + ' (' + b.tahun_terbit + ')'"></p>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-800/80 flex justify-between items-center">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md" :class="b.stok > 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20'" x-text="'Stok: ' + b.stok"></span>
                            
                            <template x-if="user.role === 'anggota'">
                                <button @click="borrowBook(b.id)" :disabled="b.stok <= 0" :class="b.stok > 0 ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-emerald-600/20' : 'bg-slate-800 text-slate-600 cursor-not-allowed shadow-none'" class="text-xs px-3.5 py-1.5 rounded-xl font-semibold transition active:scale-95 shadow-md">
                                    Pinjam
                                </button>
                            </template>

                            <template x-if="user.role === 'admin'">
                                <button @click="deleteBook(b.id)" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 text-xs px-3 py-1 rounded-xl font-semibold transition active:scale-95">Hapus</button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            <template x-if="filteredBooks().length === 0">
                <p class="text-xs text-slate-500 italic py-8 text-center">Buku tidak ditemukan.</p>
            </template>
        </div>

    </main>

    <script>
        function dashboardApp() {
            return {
                token: localStorage.getItem('bacapedia_token') || '',
                user: JSON.parse(localStorage.getItem('bacapedia_user') || '{}'),
                bookForm: { judul: '', penulis: '', penerbit: '', kategori_id: '', stok: '', tahun_terbit: '' },
                bookList: [],
                historyList: [],
                kategoriList: [],
                showAddModal: false,
                searchQuery: '',
                bookSearch: '',

                init() {
                    if (!this.token) {
                        window.location.href = '/login';
                        return;
                    }
                    this.loadData();
                },

                logout() {
                    localStorage.clear();
                    window.location.href = '/login';
                },

                async loadData() {
                    this.fetchBooks();
                    this.fetchHistory();
                    if (this.user.role === 'admin') this.fetchKategori();
                },

                async fetchBooks() {
                    const res = await fetch('/api/buku', { headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' } });
                    if (res.status === 401) return this.logout();
                    if (res.ok) this.bookList = await res.json();
                },

                async fetchHistory() {
                    const res = await fetch('/api/peminjaman/history', { headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' } });
                    if (res.ok) this.historyList = await res.json();
                },

                async fetchKategori() {
                    const res = await fetch('/api/kategori', { headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' } });
                    if (res.ok) this.kategoriList = await res.json();
                },

                // Calculates denda from DB if returned, or live estimate if overdue and active
                calculateDenda(peminjaman) {
                    if (!peminjaman) return 0;
                    
                    // If already returned, use value saved in DB
                    if (peminjaman.status === 'dikembalikan') {
                        return Number(peminjaman.denda || 0);
                    }

                    // For active loans ('dipinjam'), compute live fine if overdue
                    if (peminjaman.tanggal_jatuh_tempo) {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);

                        const dueDate = new Date(peminjaman.tanggal_jatuh_tempo);
                        dueDate.setHours(0, 0, 0, 0);

                        if (today > dueDate) {
                            const diffTime = today - dueDate;
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            const fineRatePerDay = 1000; // Adjust fine rate per day here
                            return diffDays * fineRatePerDay;
                        }
                    }

                    return Number(peminjaman.denda || 0);
                },

                async borrowBook(bukuId) {
                    const res = await fetch('/api/peminjaman/pinjam', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' },
                        body: JSON.stringify({ buku_id: bukuId })
                    });
                    const data = await res.json();
                    alert(data.message);
                    if (res.ok) this.loadData();
                },

                async returnBook(id) {
                    const res = await fetch(`/api/peminjaman/kembalikan/${id}`, {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    alert(data.message + (data.denda > 0 ? `\nDenda Keterlambatan: Rp ${Number(data.denda).toLocaleString('id-ID')}` : ''));
                    if (res.ok) this.loadData();
                },

                async addBook() {
                    const res = await fetch('/api/buku', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' },
                        body: JSON.stringify(this.bookForm)
                    });
                    const data = await res.json();
                    if (res.ok) {
                        alert('Buku berhasil ditambahkan!');
                        this.bookForm = { judul: '', penulis: '', penerbit: '', kategori_id: '', stok: '', tahun_terbit: '' };
                        this.showAddModal = false;
                        this.fetchBooks();
                    } else {
                        alert(data.message || 'Gagal menambah buku');
                    }
                },

                async deleteBook(id) {
                    if (!confirm('Hapus buku ini?')) return;
                    const res = await fetch(`/api/buku/${id}`, {
                        method: 'DELETE',
                        headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        alert('Buku berhasil dihapus');
                        this.fetchBooks();
                    }
                },

                filteredHistory() {
                    if (!this.searchQuery) return this.historyList;
                    const q = this.searchQuery.toLowerCase();
                    return this.historyList.filter(item => {
                        const nama = item.user ? item.user.nama.toLowerCase() : '';
                        const judul = item.buku ? item.buku.judul.toLowerCase() : '';
                        return nama.includes(q) || judul.includes(q);
                    });
                },

                filteredBooks() {
                    if (!this.bookSearch) return this.bookList;
                    const q = this.bookSearch.toLowerCase();
                    return this.bookList.filter(item => {
                        const judul = item.judul ? item.judul.toLowerCase() : '';
                        const penulis = item.penulis ? item.penulis.toLowerCase() : '';
                        return judul.includes(q) || penulis.includes(q);
                    });
                }
            };
        }
    </script>
</body>
</html>