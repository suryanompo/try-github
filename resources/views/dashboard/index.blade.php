<x-app-layout title="Dashboard Monitoring Proyek">
    <x-slot:breadcrumb>
        <x-breadcrumb />
    </x-slot:breadcrumb>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Selamat datang kembali, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Berikut ringkasan perkembangan proyek, status timeline, dan performa tim Anda.
            </p>
        </div>
        @can('manage-projects')
            <div class="flex items-center gap-3 shrink-0">
                <a
                    href="{{ route('projects.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-colors"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Proyek Baru</span>
                </a>
            </div>
        @endcan
    </div>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
        <!-- Card 1: Total Proyek -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Proyek</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="folder-kanban" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold text-slate-900">{{ $totalProjects }}</span>
                <span class="text-xs text-slate-500">Proyek Terdaftar</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Total Anggaran</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($totalBudget, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Card 2: Proyek Aktif -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyek Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $activeProjects }}</span>
                <span class="text-xs text-slate-500">Sedang Berjalan</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Rata-rata Progress</span>
                <span class="font-semibold text-slate-700">{{ $averageProgress }}%</span>
            </div>
        </div>

        <!-- Card 3: Proyek Selesai -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyek Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold text-emerald-600">{{ $completedProjects }}</span>
                <span class="text-xs text-slate-500">Diselesaikan</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Task Selesai</span>
                <span class="font-semibold text-slate-700">{{ $taskCompletionRate }}% dari total task</span>
            </div>
        </div>

        <!-- Card 4: Proyek Terlambat -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Proyek Terlambat</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold text-rose-600">{{ $overdueProjects }}</span>
                <span class="text-xs text-slate-500">Melewati Deadline</span>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Status Perhatian</span>
                <span class="font-semibold {{ $overdueProjects > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $overdueProjects > 0 ? 'Memerlukan Mitigasi' : 'Semua On-Track' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Section Perlu Perhatian (Section 7) -->
    @if($needingAttentionProjects->isNotEmpty())
        <div class="mb-8 bg-amber-50/60 border border-amber-200/90 rounded-xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-md bg-amber-100 text-amber-700 flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Perlu Perhatian Segera</h2>
                        <p class="text-xs text-slate-500">Proyek yang telah melewati tenggat, mendekati deadline (&le; 7 hari), atau memiliki task overdue.</p>
                    </div>
                </div>
                <span class="text-xs font-semibold bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full">
                    {{ $needingAttentionProjects->count() }} Proyek
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($needingAttentionProjects as $item)
                    @php
                        $isOverdue = $item->is_overdue;
                        $daysLeft = $item->days_remaining;
                    @endphp
                    <div class="bg-white rounded-lg border border-amber-200/70 p-4 shadow-2xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-[11px] font-mono font-medium text-slate-500">{{ $item->code }}</span>
                                @if($isOverdue)
                                    <x-badge type="danger" size="sm">Terlambat</x-badge>
                                @else
                                    <x-badge type="warning" size="sm">Sisa {{ $daysLeft }} Hari</x-badge>
                                @endif
                            </div>
                            <h3 class="text-xs font-bold text-slate-800 mt-1 line-clamp-1 hover:text-indigo-600">
                                <a href="{{ route('projects.show', $item) }}">{{ $item->name }}</a>
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                <i data-lucide="user" class="w-3 h-3 text-slate-400"></i>
                                PIC: {{ $item->manager?->name ?? 'Belum Ditentukan' }}
                            </p>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="text-slate-500 font-medium">Progress</span>
                                <span class="font-bold text-slate-700">{{ $item->progress }}%</span>
                            </div>
                            <x-progress-bar :value="$item->progress" size="sm" :color="$isOverdue ? 'rose' : 'amber'" />
                            <div class="mt-2.5 flex items-center justify-between text-[11px]">
                                <span class="text-slate-400">Deadline: {{ $item->deadline->format('d M Y') }}</span>
                                <a href="{{ route('projects.show', $item) }}" class="text-indigo-600 font-semibold hover:underline flex items-center gap-0.5">
                                    Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Visual Analytics / Charts Section (Section 5) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart 1: Progress Proyek Bar Chart (2 columns span) -->
        <div class="lg:col-span-2 bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Progress Proyek Berjalan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tingkat capaian persentase penyelesaian proyek aktif</p>
                </div>
                <a href="{{ route('projects.index', ['preset' => 'active']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Semua Proyek &rarr;
                </a>
            </div>
            <div class="relative h-64">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Status Proyek Donut Chart (1 column) -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col">
            <div class="mb-4">
                <h2 class="text-sm font-bold text-slate-900">Distribusi Status Proyek</h2>
                <p class="text-xs text-slate-500 mt-0.5">Komposisi seluruh proyek dalam sistem</p>
            </div>
            <div class="relative flex-1 flex items-center justify-center min-h-[200px]">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2 text-[11px] text-slate-600">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                    <span>Berjalan: <strong>{{ $statusCounts['Berjalan'] }}</strong></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Selesai: <strong>{{ $statusCounts['Selesai'] }}</strong></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                    <span>Terlambat: <strong>{{ $statusCounts['Terlambat'] }}</strong></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span>
                    <span>Belum Mulai: <strong>{{ $statusCounts['Belum Dimulai'] }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 3 & Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Monthly Trend Line Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Tren Pertumbuhan & Penyelesaian</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Proyek baru vs proyek yang diselesaikan 6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-slate-600">
                        <span class="w-3 h-0.5 bg-indigo-600"></span> Dibuat
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-600">
                        <span class="w-3 h-0.5 bg-emerald-600"></span> Selesai
                    </span>
                </div>
            </div>
            <div class="relative h-60">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities Feed (1 col) -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Aktivitas Terkini</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Log perubahan kegiatan tim</p>
                </div>
                <a href="{{ route('activities.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Semua &rarr;
                </a>
            </div>

            <div class="space-y-3.5 flex-1 overflow-y-auto max-h-60 pr-1">
                @forelse($recentActivities as $act)
                    <div class="flex items-start gap-2.5 text-xs">
                        <x-avatar :name="$act->user?->name ?? 'System'" size="sm" class="mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 leading-snug line-clamp-2">
                                {{ $act->description }}
                            </p>
                            <span class="text-[10px] text-slate-400 mt-0.5 block">
                                {{ $act->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-400">
                        Belum ada catatan aktivitas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Projects Table (Section 6) -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden mb-8">
        <div class="px-6 py-4.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Daftar Proyek Terbaru</h2>
                <p class="text-xs text-slate-500 mt-0.5">5 proyek yang baru saja diperbarui atau dibuat</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                    Lihat Semua Proyek <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-5">Kode</th>
                        <th class="py-3 px-5">Nama Proyek</th>
                        <th class="py-3 px-5">PIC</th>
                        <th class="py-3 px-5 w-40">Progress</th>
                        <th class="py-3 px-5">Status</th>
                        <th class="py-3 px-5">Prioritas</th>
                        <th class="py-3 px-5">Deadline</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentProjects as $project)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-5 font-mono text-slate-500 font-medium">
                                {{ $project->code }}
                            </td>
                            <td class="py-3.5 px-5">
                                <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors block max-w-xs truncate">
                                    {{ $project->name }}
                                </a>
                                @if($project->client)
                                    <span class="text-[11px] text-slate-400 block truncate">{{ $project->client }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2">
                                    <x-avatar :name="$project->manager?->name ?? 'Belum Ada'" size="sm" />
                                    <span class="text-slate-700 font-medium truncate max-w-[120px]">
                                        {{ $project->manager?->name ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <x-progress-bar :value="$project->progress" size="sm" />
                                    </div>
                                    <span class="text-[11px] font-semibold text-slate-700 w-8 text-right">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-5">
                                @php
                                    $statusType = match($project->status) {
                                        'completed' => 'success',
                                        'in_progress' => 'info',
                                        'overdue' => 'danger',
                                        'on_hold' => 'warning',
                                        default => 'neutral',
                                    };
                                @endphp
                                <x-badge :type="$statusType" size="sm">
                                    {{ $project->status_label }}
                                </x-badge>
                            </td>
                            <td class="py-3.5 px-5">
                                @php
                                    $prioType = match($project->priority) {
                                        'critical' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        'low' => 'neutral',
                                        default => 'neutral',
                                    };
                                @endphp
                                <x-badge :type="$prioType" size="sm">
                                    {{ $project->priority_label }}
                                </x-badge>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="{{ $project->is_overdue ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                    {{ $project->deadline->format('d M Y') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('projects.show', $project) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-slate-100 rounded-md transition-colors" title="Ubah Proyek">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8">
                                <x-empty-state
                                    title="Belum Ada Proyek"
                                    description="Buat proyek pertama Anda untuk mulai memonitor perkembangan timeline dan task."
                                    :action-url="route('projects.create')"
                                    action-label="Tambah Proyek"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js Scripts Initialization -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Progress Bar Chart
            const progressCtx = document.getElementById('progressChart')?.getContext('2d');
            if (progressCtx) {
                new Chart(progressCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($barChartLabels) !!},
                        datasets: [{
                            label: 'Progress (%)',
                            data: {!! json_encode($barChartData) !!},
                            backgroundColor: '#4f46e5',
                            borderRadius: 6,
                            barThickness: 24,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => `Capaian: ${ctx.parsed.y}%`
                                }
                            }
                        },
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    callback: (val) => `${val}%`,
                                    font: { size: 11 }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // 2. Status Donut Chart
            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Belum Dimulai', 'Berjalan', 'Selesai', 'Terlambat', 'Ditunda'],
                        datasets: [{
                            data: [
                                {{ $statusCounts['Belum Dimulai'] }},
                                {{ $statusCounts['Berjalan'] }},
                                {{ $statusCounts['Selesai'] }},
                                {{ $statusCounts['Terlambat'] }},
                                {{ $statusCounts['Ditunda'] }},
                            ],
                            backgroundColor: [
                                '#94a3b8', // slate
                                '#3b82f6', // blue
                                '#10b981', // emerald
                                '#ef4444', // rose
                                '#f59e0b', // amber
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            // 3. Monthly Trend Line Chart
            const trendCtx = document.getElementById('monthlyTrendChart')?.getContext('2d');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($monthlyLabels) !!},
                        datasets: [
                            {
                                label: 'Proyek Baru',
                                data: {!! json_encode($monthlyCreated) !!},
                                borderColor: '#4f46e5',
                                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                            },
                            {
                                label: 'Proyek Selesai',
                                data: {!! json_encode($monthlyCompleted) !!},
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, font: { size: 11 } },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
