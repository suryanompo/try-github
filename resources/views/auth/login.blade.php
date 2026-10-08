<x-guest-layout title="Masuk ke Sistem">
    <div class="w-full max-w-md">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white font-bold text-2xl shadow-sm mb-3">
                <i data-lucide="folder-kanban" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">ProTrack</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem Dashboard Monitoring Proyek Terpadu</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Selamat Datang</h2>
                <p class="text-xs text-slate-500 mt-1">Silakan masukkan akun Anda untuk mengakses sistem monitoring.</p>
            </div>

            @if(session('info'))
                <x-alert type="info">{{ session('info') }}</x-alert>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4.5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', 'admin@protrack.id') }}"
                            required
                            autofocus
                            class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                            placeholder="nama@organisasi.id"
                        >
                    </div>
                    @error('email')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                    </div>
                    <div class="relative" x-data="{ show: false }">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                        <input
                            :type="show ? 'text' : 'password'"
                            id="password"
                            name="password"
                            value="password"
                            required
                            class="w-full pl-9 pr-10 py-2.5 text-sm bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-0.5"
                        >
                            <i :data-lucide="show ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            checked
                        >
                        <span class="text-xs text-slate-600">Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold rounded-lg shadow-xs transition-colors flex items-center justify-center gap-2"
                    >
                        <span>Masuk ke Dashboard</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2.5">Akun Demo (Klik untuk isi cepat):</p>
                <div class="space-y-1.5" x-data>
                    <button
                        type="button"
                        @click="document.getElementById('email').value='admin@protrack.id'; document.getElementById('password').value='password';"
                        class="w-full text-left px-2.5 py-1.5 rounded-md hover:bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600 transition-colors"
                    >
                        <span><strong class="text-slate-900">Admin:</strong> admin@protrack.id</span>
                        <span class="text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded font-medium">Full Access</span>
                    </button>
                    <button
                        type="button"
                        @click="document.getElementById('email').value='budi.santoso@protrack.id'; document.getElementById('password').value='password';"
                        class="w-full text-left px-2.5 py-1.5 rounded-md hover:bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600 transition-colors"
                    >
                        <span><strong class="text-slate-900">Project Manager:</strong> budi.santoso@protrack.id</span>
                        <span class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded font-medium">PM Role</span>
                    </button>
                    <button
                        type="button"
                        @click="document.getElementById('email').value='ahmad.fauzi@protrack.id'; document.getElementById('password').value='password';"
                        class="w-full text-left px-2.5 py-1.5 rounded-md hover:bg-slate-50 border border-slate-100 flex items-center justify-between text-xs text-slate-600 transition-colors"
                    >
                        <span><strong class="text-slate-900">Member:</strong> ahmad.fauzi@protrack.id</span>
                        <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-medium">Developer</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Notice -->
        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; {{ date('Y') }} ProTrack. Sistem Monitoring Proyek Terpadu.
        </p>
    </div>
</x-guest-layout>
