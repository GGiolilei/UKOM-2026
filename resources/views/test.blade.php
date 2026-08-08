<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacapedia - Digital Library Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 font-sans min-h-screen text-slate-800" x-data="bacapediaApp()">

    <!-- ================= 1. SPLASH SCREEN ================= -->
    <div x-show="showSplash" 
         x-transition:leave="transition ease-in duration-500 transform opacity-100 scale-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-indigo-900 text-white">
        <div class="relative flex items-center justify-center">
            <div class="w-24 h-24 border-4 border-indigo-400 border-t-indigo-200 rounded-full animate-spin"></div>
            <div class="absolute text-2xl font-black tracking-widest">BP</div>
        </div>
        <h1 class="text-3xl font-bold mt-6 tracking-wide">BACAPEDIA</h1>
        <p class="text-indigo-300 text-sm mt-1">Sistem Manajemen Perpustakaan Digital</p>
    </div>

    <!-- ================= 2. MAIN APP CONTAINER ================= -->
    <div x-cloak x-show="!showSplash" class="min-h-screen flex flex-col">

        <!-- NAVBAR -->
        <header class="bg-indigo-700 text-white shadow-lg sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="bg-white text-indigo-700 px-3 py-1 rounded-lg font-black tracking-wider text-sm shadow">BP</span>
                    <h1 class="text-xl font-bold tracking-tight">Bacapedia</h1>
                </div>

                <template x-if="token">
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm font-semibold" x-text="user.nama"></p>
                            <span :class="{
                                'bg-purple-500': user.role === 'admin',
                                'bg-blue-500': user.role === 'petugas',
                                'bg-emerald-500': user.role === 'anggota'
                            }" class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider text-white" x-text="user.role"></span>
                        </div>
                        <button @click="logout()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg transition font-medium">Logout</button>
                    </div>
                </template>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 space-y-6">

            <!-- ================= VIEW LOGIN (JIKA BELUM LOGIN) ================= -->
            <div x-show="!token" class="max-w-md mx-auto my-12 bg-white p-8 rounded-2xl shadow-md border border-slate-200 space-y-6">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-slate-800">Masuk ke Bacapedia</h2>
                    <p class="text-xs text-slate-500 mt-1">Gunakan akun di bawah untuk pengujian role</p>
                </div>

                <!-- Quick Presets -->
                <div class="grid grid-cols-3 gap-2">
                    <button @click="fillLogin('admin@bacapedia.id')" class="bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs py-2 rounded-lg font-semibold border border-purple-200 transition">Admin</button>
                    <button @click="fillLogin('petugas@bacapedia.id')" class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs py-2 rounded-lg font-semibold border border-blue-200 transition">Petugas</button>
                    <button @click="fillLogin('budi@gmail.com')" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs py-2 rounded-lg font-semibold border border-emerald-200 transition">Anggota</button>
                </div>

                <form @submit.prevent="login()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                        <input x-model="loginForm.email" type="email" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                        <input x-model="loginForm.password" type="password" required class="w-full border border-slate-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition shadow">Login</button>
                </form>
            </div>

            <!-- ================= DASHBOARD MULTI-ROLE ================= -->
            <template x-if="token">
                <div class="space-y-6">

                    <!-- ================= 1. ADMIN DASHBOARD ================= -->
                    <template x-if="user.role === 'admin'">
                        <div class="space-y-6">
                            <div class="bg-purple-900 text-white p-6 rounded-2xl shadow-md">
                                <h2 class="text-2xl font-bold">Dashboard Administrator</h2>
                                <p class="text-purple-200 text-sm mt-1">Kelola data katalog buku, kategori, dan pantau seluruh transaksi peminjaman.</p>
                            </div>

                            <!-- Panel Tambah Buku Baru -->
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                                <h3 class="font-bold text-slate-800 border-b pb-2 flex justify-between items-center">
                                    <span>+ Tambah Buku Baru</span>
                                </h3>
                                <form @submit.prevent="addBook()" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <input x-model="bookForm.judul" placeholder="Judul Buku" required class="border p-2 text-sm rounded-lg">
                                    <input x-model="bookForm.penulis" placeholder="Penulis" required class="border p-2 text-sm rounded-lg">
                                    <input x-model="bookForm.penerbit" placeholder="Penerbit" required class="border p-2 text-sm rounded-lg">
                                    
                                    <select x-model="bookForm.kategori_id" required class="border p-2 text-sm rounded-lg">
                                        <option value="">-- Pilih Kategori --</option>
                                        <template x-for="k in kategoriList" :key="k.id">
                                            <option :value="k.id" x-text="k.nama_kategori"></option>
                                        </template>
                                    </select>
                                    
                                    <input x-model="bookForm.stok" type="number" placeholder="Stok" min="1" required class="border p-2 text-sm rounded-lg">
                                    <input x-model="bookForm.tahun_terbit" type="number" placeholder="Tahun Terbit" required class="border p-2 text-sm rounded-lg">
                                    
                                    <button type="submit" class="md:col-span-3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2 rounded-lg transition">Simpan Buku</button>
                                </form>
                            </div>

                            <!-- Tabel Semua Peminjaman (Admin Monitor) -->
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                                <h3 class="font-bold text-slate-800 border-b pb-2">Pantau Seluruh Transaksi Peminjaman</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-100 text-slate-600 uppercase">
                                            <tr>
                                                <th class="p-3">Peminjam</th>
                                                <th class="p-3">Buku</th>
                                                <th class="p-3">Tgl Pinjam</th>
                                                <th class="p-3">Tgl Tempo</th>
                                                <th class="p-3">Status</th>
                                                <th class="p-3">Denda</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            <template x-for="p in historyList" :key="p.id">
                                                <tr class="hover:bg-slate-50">
                                                    <td class="p-3 font-semibold" x-text="p.user ? p.user.nama : '-'"></td>
                                                    <td class="p-3" x-text="p.buku ? p.buku.judul : '-'"></td>
                                                    <td class="p-3" x-text="p.tanggal_pinjam"></td>
                                                    <td class="p-3" x-text="p.tanggal_jatuh_tempo"></td>
                                                    <td class="p-3">
                                                        <span :class="p.status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="px-2 py-0.5 rounded font-semibold" x-text="p.status"></span>
                                                    </td>
                                                    <td class="p-3 font-bold text-red-600" x-text="'Rp ' + Number(p.denda).toLocaleString('id-ID')"></td>
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
                            <div class="bg-blue-900 text-white p-6 rounded-2xl shadow-md">
                                <h2 class="text-2xl font-bold">Dashboard Petugas</h2>
                                <p class="text-blue-200 text-sm mt-1">Proses pengembalian buku anggota dan cek transaksi peminjaman aktif.</p>
                            </div>

                            <!-- Tabel Pengembalian Buku -->
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                                <h3 class="font-bold text-slate-800 border-b pb-2">Proses Pengembalian Buku Anggota</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-100 text-slate-600 uppercase">
                                            <tr>
                                                <th class="p-3">Nama Anggota</th>
                                                <th class="p-3">Judul Buku</th>
                                                <th class="p-3">Tgl Jatuh Tempo</th>
                                                <th class="p-3">Status</th>
                                                <th class="p-3">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            <template x-for="p in historyList" :key="p.id">
                                                <tr class="hover:bg-slate-50">
                                                    <td class="p-3 font-semibold" x-text="p.user ? p.user.nama : '-'"></td>
                                                    <td class="p-3" x-text="p.buku ? p.buku.judul : '-'"></td>
                                                    <td class="p-3" x-text="p.tanggal_jatuh_tempo"></td>
                                                    <td class="p-3">
                                                        <span :class="p.status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="px-2 py-0.5 rounded font-semibold" x-text="p.status"></span>
                                                    </td>
                                                    <td class="p-3">
                                                        <template x-if="p.status === 'dipinjam'">
                                                            <button @click="returnBook(p.id)" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs transition font-semibold">Terima Pengembalian</button>
                                                        </template>
                                                        <template x-if="p.status === 'dikembalikan'">
                                                            <span class="text-slate-400 italic">Selesai</span>
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
                            <div class="bg-emerald-800 text-white p-6 rounded-2xl shadow-md">
                                <h2 class="text-2xl font-bold">Katalog & Peminjaman Saya</h2>
                                <p class="text-emerald-200 text-sm mt-1">Cari buku favoritmu dan ajukan peminjaman langsung secara online.</p>
                            </div>

                            <!-- Riwayat Peminjaman Pribadi -->
                            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                                <h3 class="font-bold text-slate-800 border-b pb-2">Buku yang Sedang / Pernah Saya Pinjam</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <template x-for="p in historyList" :key="p.id">
                                        <div class="border p-4 rounded-xl space-y-2 flex justify-between items-center">
                                            <div>
                                                <h4 class="font-bold text-sm text-slate-800" x-text="p.buku ? p.buku.judul : '-'"></h4>
                                                <p class="text-xs text-slate-500" x-text="'Tgl Jatuh Tempo: ' + p.tanggal_jatuh_tempo"></p>
                                                <span :class="p.status === 'dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold" x-text="p.status.toUpperCase()"></span>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-slate-400">Denda Late</p>
                                                <p class="text-sm font-bold text-red-600" x-text="'Rp ' + Number(p.denda).toLocaleString('id-ID')"></p>
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
                                <div class="border rounded-xl p-4 flex flex-col justify-between hover:shadow-md transition">
                                    <div class="space-y-1">
                                        <span class="text-[10px] bg-slate-100 px-2 py-0.5 rounded font-semibold text-slate-600" x-text="b.kategori ? b.kategori.nama_kategori : 'Umum'"></span>
                                        <h4 class="font-bold text-slate-800 text-base" x-text="b.judul"></h4>
                                        <p class="text-xs text-slate-500" x-text="'Penulis: ' + b.penulis"></p>
                                        <p class="text-xs text-slate-500" x-text="'Tahun: ' + b.tahun_terbit"></p>
                                    </div>

                                    <div class="mt-4 pt-3 border-t flex justify-between items-center">
                                        <span class="text-xs font-semibold" :class="b.stok > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="'Stok: ' + b.stok"></span>
                                        
                                        <!-- Tombol hanya muncul jika Anggota -->
                                        <template x-if="user.role === 'anggota'">
                                            <button @click="borrowBook(b.id)" :disabled="b.stok <= 0" :class="b.stok > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-300 text-slate-500 cursor-not-allowed'" class="text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                Pinjam
                                            </button>
                                        </template>

                                        <!-- Tombol Hapus Buku (Admin Only) -->
                                        <template x-if="user.role === 'admin'">
                                            <button @click="deleteBook(b.id)" class="bg-red-100 hover:bg-red-200 text-red-600 text-xs px-2.5 py-1 rounded-lg font-semibold transition">
                                                Hapus
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </template>

        </main>
    </div>

    <!-- APP SCRIPT -->
    <script>
        function bacapediaApp() {
            return {
                showSplash: true,
                token: localStorage.getItem('bacapedia_token') || '',
                user: JSON.parse(localStorage.getItem('bacapedia_user') || '{}'),
                loginForm: { email: '', password: '' },
                bookForm: { judul: '', penulis: '', penerbit: '', kategori_id: '', stok: '', tahun_terbit: '' },
                bookList: [],
                historyList: [],
                kategoriList: [],

                init() {
                    // Tampilkan Splash Screen selama 1.5 detik
                    setTimeout(() => {
                        this.showSplash = false;
                    }, 1500);

                    if (this.token) {
                        this.loadDashboardData();
                    }
                },

                fillLogin(email) {
                    this.loginForm.email = email;
                    this.loginForm.password = 'password123';
                },

                async login() {
                    try {
                        const res = await fetch('/api/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(this.loginForm)
                        });
                        const data = await res.json();

                        if (res.ok) {
                            this.token = data.token;
                            this.user = data.user;
                            localStorage.setItem('bacapedia_token', data.token);
                            localStorage.setItem('bacapedia_user', JSON.stringify(data.user));
                            this.loadDashboardData();
                        } else {
                            alert(data.message || 'Login gagal');
                        }
                    } catch (err) {
                        alert('Gagal terhubung ke server');
                    }
                },

                logout() {
                    this.token = '';
                    this.user = {};
                    localStorage.clear();
                },

                async loadDashboardData() {
                    this.fetchBooks();
                    this.fetchHistory();
                    if (this.user.role === 'admin') {
                        this.fetchKategori();
                    }
                },

                async fetchBooks() {
                    const res = await fetch('/api/buku', {
                        headers: { 'Authorization': `Bearer ${this.token}` }
                    });
                    if(res.ok) this.bookList = await res.json();
                },

                async fetchHistory() {
                    const res = await fetch('/api/peminjaman/history', {
                        headers: { 'Authorization': `Bearer ${this.token}` }
                    });
                    if(res.ok) this.historyList = await res.json();
                },

                async fetchKategori() {
                    const res = await fetch('/api/kategori', {
                        headers: { 'Authorization': `Bearer ${this.token}` }
                    });
                    if(res.ok) this.kategoriList = await res.json();
                },

                async borrowBook(bukuId) {
                    const res = await fetch('/api/peminjaman/pinjam', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${this.token}`
                        },
                        body: JSON.stringify({ buku_id: bukuId })
                    });
                    const data = await res.json();
                    alert(data.message);
                    if (res.ok) this.loadDashboardData();
                },

                async returnBook(id) {
                    const res = await fetch(`/api/peminjaman/kembalikan/${id}`, {
                        method: 'POST',
                        headers: { 'Authorization': `Bearer ${this.token}` }
                    });
                    const data = await res.json();
                    alert(data.message + (data.denda > 0 ? `\nDenda Keterlambatan: Rp${data.denda}` : ''));
                    if (res.ok) this.loadDashboardData();
                },

                async addBook() {
                    const res = await fetch('/api/buku', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${this.token}`
                        },
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
                    if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) return;
                    const res = await fetch(`/api/buku/${id}`, {
                        method: 'DELETE',
                        headers: { 'Authorization': `Bearer ${this.token}` }
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