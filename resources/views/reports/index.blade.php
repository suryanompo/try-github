<x-app-layout title="Laporan & Analitik Proyek">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Laporan' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Laporan & Analitik Portofolio</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Evaluasi performa penyelesaian proyek, alokasi prioritas, dan beban kerja tim.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('reports.export', request()->query()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold rounded-lg border border-slate-200 shadow-2xs transition-colors"
            >
                <i data-lucide="download" class="w-4 h-4 text-slate-500"></i>
                <span>Ekspor CSV / Excel</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs mb-6">
        <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Status Proyek:</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5">
                    <option value="">Semua Status</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Belum Dimulai</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                    <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>Ditunda</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Tingkat Prioritas:</label>
                <select name="priority" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5">
                    <option value="">Semua Prioritas</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritis</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Penanggung Jawab (PIC):</label>
                <select name="manager_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5">
                    <option value="">Semua PIC</option>
                    @foreach($managers as $m)
                        <option value="{{ $m->id }}" {{ request('manager_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Deadline Dari:</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5">
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block font-semibold text-slate-600 mb-1">Deadline Sampai:</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5">
                </div>
                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Terfilter</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $totalProjects }}</span>
                <span class="text-xs text-slate-500">Proyek</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Rp {{ number_format($totalBudget, 0, ',', '.') }} alokasi</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rata-rata Capaian</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-indigo-600">{{ $averageProgress }}%</span>
                <span class="text-xs text-slate-500">Selesai</span>
            </div>
            <div class="mt-3">
                <x-progress-bar :value="$averageProgress" size="sm" />
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tingkat Selesai Task</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-emerald-600">{{ $taskCompletionRate }}%</span>
                <span class="text-xs text-slate-500">{{ $completedTasks }}/{{ $totalTasks }} task</span>
            </div>
            <div class="mt-3">
                <x-progress-bar :value="$taskCompletionRate" size="sm" color="emerald" />
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterlambatan</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-rose-600">{{ $overdueCount }}</span>
                <span class="text-xs text-slate-500">Proyek Overdue</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Perlu percepatan sprint</p>
        </div>
    </div>

    <!-- Analytics Breakdown Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Breakdown by Status -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Distribusi Berdasarkan Status</h3>
            <div class="space-y-3 text-xs">
                @foreach($statusBreakdown as $statusName => $count)
                    @php
                        $pct = $totalProjects > 0 ? round(($count / $totalProjects) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-slate-700">{{ $statusName }}</span>
                            <span class="text-slate-500"><strong>{{ $count }}</strong> ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Breakdown by Priority -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Distribusi Berdasarkan Prioritas</h3>
            <div class="space-y-3 text-xs">
                @foreach($priorityBreakdown as $prioName => $count)
                    @php
                        $pct = $totalProjects > 0 ? round(($count / $totalProjects) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-slate-700">{{ $prioName }}</span>
                            <span class="text-slate-500"><strong>{{ $count }}</strong> ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PIC Performance Table -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Performa & Alokasi Beban PIC (Project Manager)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Nama PIC</th>
                        <th class="py-3 px-5">Departemen</th>
                        <th class="py-3 px-5 text-center">Total Proyek</th>
                        <th class="py-3 px-5 text-center">Proyek Selesai</th>
                        <th class="py-3 px-5 text-center">Proyek Terlambat</th>
                        <th class="py-3 px-5 w-40">Rata-rata Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($picPerformance as $pic)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5 font-semibold text-slate-900">
                                {{ $pic['name'] }}
                            </td>
                            <td class="py-3.5 px-5 text-slate-500">
                                {{ $pic['department'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-slate-800">
                                {{ $pic['total_projects'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-emerald-600">
                                {{ $pic['completed'] }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold {{ $pic['overdue'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ $pic['overdue'] }}
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <x-progress-bar :value="$pic['avg_progress']" size="sm" />
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-700 w-8 text-right">{{ $pic['avg_progress'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada data performa PIC.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
