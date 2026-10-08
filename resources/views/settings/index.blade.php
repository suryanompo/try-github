<x-app-layout title="Pengaturan Akun">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Pengaturan' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Pengaturan Akun & Keamanan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Perbarui profil identitas, kontak, serta kredensial kata sandi akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Summary Card -->
        <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs flex flex-col items-center text-center">
            <x-avatar :name="$user->name" size="xl" class="mb-4" />
            <h2 class="text-base font-bold text-slate-900">{{ $user->name }}</h2>
            <p class="text-xs text-slate-500">{{ $user->email }}</p>
            <div class="mt-3">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $user->role_label }}
                </span>
            </div>
            <div class="mt-6 pt-6 border-t border-slate-100 w-full text-left space-y-2.5 text-xs">
                <div class="flex items-center justify-between text-slate-500">
                    <span>Departemen:</span>
                    <span class="font-medium text-slate-800">{{ $user->department ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>No. Telepon:</span>
                    <span class="font-medium text-slate-800">{{ $user->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-500">
                    <span>Bergabung Sejak:</span>
                    <span class="font-medium text-slate-800">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Forms: Profile & Password -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">
                    Informasi Profil
                </h3>

                <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block font-semibold text-slate-700 mb-1">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                required
                                class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="email" class="block font-semibold text-slate-700 mb-1">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="department" class="block font-semibold text-slate-700 mb-1">Departemen / Unit</label>
                            <input
                                type="text"
                                id="department"
                                name="department"
                                value="{{ old('department', $user->department) }}"
                                class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="phone" class="block font-semibold text-slate-700 mb-1">Nomor Telepon</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-xs">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                <h3 class="text-sm font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">
                    Ganti Kata Sandi
                </h3>

                <form action="{{ route('settings.password') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block font-semibold text-slate-700 mb-1">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block font-semibold text-slate-700 mb-1">
                                Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-semibold text-slate-700 mb-1">
                                Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold shadow-xs">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
