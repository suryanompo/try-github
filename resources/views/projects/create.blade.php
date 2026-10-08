<x-app-layout title="Tambah Proyek Baru">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Proyek' => route('projects.index'), 'Tambah Proyek' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Tambah Proyek Baru</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi informasi dasar, alokasi PIC, jadwal, serta target proyek di bawah ini.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <form action="{{ route('projects.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- 2-Column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Proyek -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Proyek <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Pengembangan Portal Informasi Publik"
                        required
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('name') border-rose-500 @enderror"
                    >
                    @error('name')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Proyek -->
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kode Proyek <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        value="{{ old('code', $suggestedCode) }}"
                        required
                        placeholder="PRJ-2026-001"
                        class="w-full text-sm font-mono px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('code') border-rose-500 @enderror"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Identitas unik proyek untuk referensi dokumen.</p>
                    @error('code')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Client / Instansi -->
                <div>
                    <label for="client" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Klien / Instansi
                    </label>
                    <input
                        type="text"
                        name="client"
                        id="client"
                        value="{{ old('client') }}"
                        placeholder="Contoh: Dinas Komunikasi dan Informatika"
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Nama organisasi atau pemangku kepentingan eksternal.</p>
                </div>

                <!-- Penanggung Jawab (PIC) -->
                <div>
                    <label for="manager_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Penanggung Jawab (PIC / Manager) <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="manager_id"
                        id="manager_id"
                        required
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('manager_id') border-rose-500 @enderror"
                    >
                        <option value="">-- Pilih PIC Proyek --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id', auth()->id()) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} ({{ $manager->role_label }})
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Anggaran / Budget -->
                <div>
                    <label for="budget" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Anggaran Proyek (Rp)
                    </label>
                    <input
                        type="number"
                        name="budget"
                        id="budget"
                        value="{{ old('budget', 0) }}"
                        min="0"
                        step="1000"
                        placeholder="100000000"
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all"
                    >
                    <p class="text-[11px] text-slate-400 mt-1">Nilai alokasi anggaran proyek (opsional).</p>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tanggal Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        value="{{ old('start_date', date('Y-m-d')) }}"
                        required
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('start_date') border-rose-500 @enderror"
                    >
                    @error('start_date')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Batas Waktu (Deadline) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="deadline"
                        id="deadline"
                        value="{{ old('deadline', date('Y-m-d', strtotime('+30 days'))) }}"
                        required
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all @error('deadline') border-rose-500 @enderror"
                    >
                    @error('deadline')
                        <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prioritas -->
                <div>
                    <label for="priority" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Tingkat Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="priority"
                        id="priority"
                        required
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all"
                    >
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                        <option value="critical" {{ old('priority') === 'critical' ? 'selected' : '' }}>Kritis (Critical)</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Status Awal <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="status"
                        id="status"
                        required
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all"
                    >
                        <option value="not_started" {{ old('status', 'not_started') === 'not_started' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>Ditunda</option>
                        <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="overdue" {{ old('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>

                <!-- Progress Slider / Input -->
                <div class="md:col-span-2" x-data="{ prog: {{ old('progress', 0) }} }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="progress" class="text-xs font-semibold text-slate-700">
                            Estimasi Capaian Progress (%) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-xs font-bold text-indigo-600" x-text="prog + '%'"></span>
                    </div>
                    <input
                        type="range"
                        name="progress"
                        id="progress"
                        min="0"
                        max="100"
                        x-model="prog"
                        class="w-full accent-indigo-600 cursor-pointer"
                    >
                    <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                        <span>0%</span>
                        <span>25%</span>
                        <span>50%</span>
                        <span>75%</span>
                        <span>100%</span>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Deskripsi Ruang Lingkup Proyek
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        placeholder="Jelaskan tujuan umum, objektif, dan ruang lingkup dari proyek ini..."
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all leading-relaxed"
                    >{{ old('description') }}</textarea>
                </div>

                <!-- Catatan Tambahan -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Catatan Khusus / Hambatan
                    </label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="2"
                        placeholder="Catatan tambahan teknis, dependencies pihak ketiga, atau pertimbangan khusus..."
                        class="w-full text-sm px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all leading-relaxed"
                    >{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a
                    href="{{ route('projects.index') }}"
                    class="px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-lg border border-slate-200 transition-colors"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors flex items-center gap-2"
                >
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Simpan Proyek</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
