<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bacapedia - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 font-sans min-h-screen flex items-center justify-center p-4" x-data="loginApp()">

    <!-- Splash Screen -->
    <div x-cloak x-show="showSplash" 
         x-transition:leave="transition ease-in duration-300 transform opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-indigo-900 text-white">
        <div class="relative flex items-center justify-center">
            <div class="w-20 h-20 border-4 border-indigo-400 border-t-indigo-200 rounded-full animate-spin"></div>
            <div class="absolute text-xl font-black">BP</div>
        </div>
        <h1 class="text-2xl font-bold mt-4 tracking-wide">BACAPEDIA</h1>
        <p class="text-indigo-300 text-xs mt-1">Sistem Perpustakaan Digital</p>
    </div>

    <!-- Login Card -->
    <div x-cloak x-show="!showSplash" class="w-full max-w-md bg-white p-8 rounded-2xl shadow-md border border-slate-200 space-y-6">
        <div class="text-center">
            <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg font-black tracking-wider text-sm inline-block mb-2">BP</span>
            <h2 class="text-2xl font-bold text-slate-800">Masuk Bacapedia</h2>
            <p class="text-xs text-slate-500 mt-1">Pilih role untuk mengisi kredensial otomatis:</p>
        </div>

        <!-- Quick Presets -->
        <div class="grid grid-cols-3 gap-2">
            <button type="button" @click="fill('admin@bacapedia.id')" class="bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs py-2 rounded-lg font-semibold border border-purple-200 transition">Admin</button>
            <button type="button" @click="fill('petugas@bacapedia.id')" class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs py-2 rounded-lg font-semibold border border-blue-200 transition">Petugas</button>
            <button type="button" @click="fill('budi@gmail.com')" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs py-2 rounded-lg font-semibold border border-emerald-200 transition">Anggota</button>
        </div>

        <form @submit.prevent="doLogin()" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                <input x-model="form.email" type="email" required class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                <input x-model="form.password" type="password" required class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <!-- Tombol submit dengan status loading -->
            <button type="submit" 
                    :disabled="isLoading" 
                    :class="isLoading ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                    class="w-full text-white font-semibold py-2.5 rounded-lg text-sm transition shadow flex justify-center items-center gap-2">
                <template x-if="isLoading">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="isLoading ? 'Memproses...' : 'Masuk Aplikasi'"></span>
            </button>
        </form>
    </div>

    <script>
        function loginApp() {
            return {
                showSplash: true,
                isLoading: false,
                form: { email: '', password: '' },

                init() {
                    // Sembunyikan Splash Screen setelah jeda singkat
                    setTimeout(() => { this.showSplash = false; }, 1000);

                    // Redirect otomatis jika token sudah tersimpan
                    if (localStorage.getItem('bacapedia_token')) {
                        window.location.href = '/dashboard';
                    }
                },

                fill(email) {
                    this.form.email = email;
                    this.form.password = 'password123';
                },

                async doLogin() {
                    if (this.isLoading) return;
                    this.isLoading = true;

                    try {
                        const res = await fetch('/api/login', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'Accept': 'application/json' 
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await res.json();

                        if (res.ok) {
                            localStorage.setItem('bacapedia_token', data.token);
                            localStorage.setItem('bacapedia_user', JSON.stringify(data.user));
                            window.location.href = '/dashboard';
                        } else {
                            alert(data.message || 'Login gagal, periksa email & password.');
                        }
                    } catch (err) {
                        alert('Gagal terhubung ke API Server. Pastikan backend Laravel berjalan.');
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>