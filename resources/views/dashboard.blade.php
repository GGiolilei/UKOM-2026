<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacapedia - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 font-sans min-h-screen text-slate-800" x-data="dashboardApp()">

    <!-- Header Navbar -->
    <header class="bg-indigo-700 text-white shadow sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="bg-white text-indigo-700 px-3 py-1 rounded-lg font-black text-sm">BP</span>
                <h1 class="text-xl font-bold">Bacapedia Dashboard</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold" x-text="user.nama"></p>
                    <span :class="{
                        'bg-purple-500': user.role === 'admin',
                        'bg-blue-500': user.role === 'petugas',
                        'bg-emerald-500': user.role === 'anggota'
                    }" class="text-[10px] px-2 py-0.5 rounded font-bold uppercase text-white" x-text="user.role"></span>
                </div>
                <button @click="logout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg transition font-medium">Logout</button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">

        <!-- ================= 1. ADMIN DASHBOARD ================= -->
        <template x-if="user.role === 'admin'">
            <div class="space-y-6">
                <div class="bg-purple-900 text-white p-6 rounded-2xl shadow">
                    <h2 class="text-xl font-bold">Panel Administrator</h2>
                    <p class="text-purple-200 text-xs mt-1">Kelola katalog buku, kategori, dan pantau seluruh peminjaman.</p>
                </div>

                <!-- Form Tambah Buku -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-2">+ Tambah Buku Baru</h3>
                    <form @submit.prevent="addBook()" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input x-model="bookForm.judul" placeholder="Judul Buku" required class="border p-2 text-xs rounded-lg">
                        <input x-model="bookForm.penulis" placeholder="Penulis" required class="border p-2 text-xs rounded-lg">
                        <input x-model="bookForm.penerbit" placeholder="Penerbit" required class="border p-2 text-xs rounded-lg">
                        <select x-model="bookForm.kategori_id" required class="border p-2 text-xs rounded-lg">
                            <option value="">-- Pilih Kategori --</option>
                            <template x-for="k in kategoriList" :key="k.id">
                                <option :value="k.id" x-text="k.nama_kategori"></option>
                            </template>
                        </select>
                        <input x-model="bookForm.stok" type="number" placeholder="Stok" min="1" required class="border p-2 text-xs rounded-lg">
                        <input x-model="bookForm.tahun_terbit" type="number" placeholder="Tahun Terbit" required class="border p-2 text-xs rounded-lg">
                        <button type="submit" class="md:col-span-3 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold py-2 rounded-lg">Simpan Buku</button>
                    </form>
                </div>

                <!-- Tabel Seluruh Peminjaman -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-2">Semua Transaksi Peminjaman User</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 uppercase">
                                <tr>
                                    <th class="p-2">Nama Peminjam</th>
                                    <th class="p-2">Buku</th>
                                    <th class="p-2">Tgl Pinjam</th>
                                    <th class="p-2">Jatuh Tempo</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2">Denda</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-for="p in historyList" :key="p.id">
                                    <tr>
                                        <td class="p-2 font-semibold" x-text="p.user ? p.user.nama : '-'"></td>
                                        <td class="p-2" x-text="p.buku ? p.buku.judul : '-'"></td>
                                        <td class="p-2" x-text="p.tanggal_pinjam"></td>
                                        <td class="p-2" x-text="p.tanggal_jatuh_tempo"></td>
                                        <td class="p-2">
                                            <span :class="p.status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="px-2 py-0.5 rounded font-bold" x-text="p.status"></span>
                                        </td>
                                        <td class="p-2 font-bold text-red-600" x-text="'Rp ' + Number(p.denda).toLocaleString('id-ID')"></td>
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
            <div class="space-y-6">
                <div class="bg-blue-900 text-white p-6 rounded-2xl shadow">
                    <h2 class="text-xl font-bold">Panel Petugas Perpustakaan</h2>
                    <p class="text-blue-200 text-xs mt-1">Proses penerimaan pengembalian buku dan denda keterlambatan.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-2">Daftar Peminjaman Aktif (Terima Buku)</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 uppercase">
                                <tr>
                                    <th class="p-2">Nama Anggota</th>
                                    <th class="p-2">Judul Buku</th>
                                    <th class="p-2">Jatuh Tempo</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <template x-for="p in historyList" :key="p.id">
                                    <tr>
                                        <td class="p-2 font-semibold" x-text="p.user ? p.user.nama : '-'"></td>
                                        <td class="p-2" x-text="p.buku ? p.buku.judul : '-'"></td>
                                        <td class="p-2" x-text="p.tanggal_jatuh_tempo"></td>
                                        <td class="p-2">
                                            <span :class="p.status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="px-2 py-0.5 rounded font-bold" x-text="p.status"></span>
                                        </td>
                                        <td class="p-2">
                                            <template x-if="p.status === 'dipinjam'">
                                                <button @click="returnBook(p.id)" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold">Proses Kembalikan</button>
                                            </template>
                                            <template x-if="p.status === 'dikembalikan'">
                                                <span class="text-slate-400 italic">Dikembalikan</span>
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
            <div class="space-y-6">
                <div class="bg-emerald-800 text-white p-6 rounded-2xl shadow">
                    <h2 class="text-xl font-bold">Katalog & Peminjaman Saya</h2>
                    <p class="text-emerald-200 text-xs mt-1">Cari dan pinjam buku secara online.</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-2">Riwayat Peminjaman Saya</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <template x-for="p in historyList" :key="p.id">
                            <div class="border p-3 rounded-xl flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold text-xs text-slate-800" x-text="p.buku ? p.buku.judul : '-'"></h4>
                                    <p class="text-[10px] text-slate-500" x-text="'Jatuh Tempo: ' + p.tanggal_jatuh_tempo"></p>
                                    <span :class="p.status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold" x-text="p.status"></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-400">Denda</p>
                                    <p class="text-xs font-bold text-red-600" x-text="'Rp ' + Number(p.denda).toLocaleString('id-ID')"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- ================= KATALOG BUKU (SEMUA ROLE) ================= -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
            <h3 class="font-bold text-slate-800 border-b pb-2">Katalog Seluruh Buku</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <template x-for="b in bookList" :key="b.id">
                    <div class="border rounded-xl p-4 flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded font-semibold text-slate-600" x-text="b.kategori ? b.kategori.nama_kategori : 'Umum'"></span>
                            <h4 class="font-bold text-slate-800 text-sm" x-text="b.judul"></h4>
                            <p class="text-xs text-slate-500" x-text="'Penulis: ' + b.penulis"></p>
                        </div>

                        <div class="mt-4 pt-3 border-t flex justify-between items-center">
                            <span class="text-xs font-semibold" :class="b.stok > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="'Stok: ' + b.stok"></span>
                            
                            <template x-if="user.role === 'anggota'">
                                <button @click="borrowBook(b.id)" :disabled="b.stok <= 0" :class="b.stok > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-300 text-slate-500 cursor-not-allowed'" class="text-xs px-3 py-1.5 rounded-lg font-semibold">
                                    Pinjam
                                </button>
                            </template>

                            <template x-if="user.role === 'admin'">
                                <button @click="deleteBook(b.id)" class="bg-red-100 hover:bg-red-200 text-red-600 text-xs px-2.5 py-1 rounded-lg font-semibold">Hapus</button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
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
                    alert(data.message + (data.denda > 0 ? `\nDenda Keterlambatan: Rp${data.denda}` : ''));
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
                }
            }
        }
    </script>
</body>
</html>