<x-app-layout title="Daftar Proyek">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Proyek' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Manajemen Proyek</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola dan pantau seluruh portofolio proyek organisasi secara real-time.</p>
        </div>
        @can('manage-projects')
            <a
                href="{{ route('projects.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-xs transition-colors shrink-0"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Proyek</span>
            </a>
        @endcan
    </div>

    <!-- Quick Status Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 mb-6 overflow-x-auto text-xs font-medium">
        <a
            href="{{ route('projects.index', array_merge(request()->except(['page', 'preset']), ['preset' => null])) }}"
            class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap {{ !request('preset') && !request('status') ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>Semua Proyek</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ !request('preset') && !request('status') ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>
        <a
            href="{{ route('projects.index', array_merge(request()->except(['page']), ['preset' => 'active'])) }}"
            class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap {{ request('preset') === 'active' ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>Proyek Aktif</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('preset') === 'active' ? 'bg-indigo-700 text-white' : 'bg-blue-100 text-blue-700' }}">{{ $counts['active'] }}</span>
        </a>
        <a
            href="{{ route('projects.index', array_merge(request()->except(['page']), ['preset' => 'completed'])) }}"
            class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap {{ request('preset') === 'completed' ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>Proyek Selesai</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('preset') === 'completed' ? 'bg-indigo-700 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $counts['completed'] }}</span>
        </a>
        <a
            href="{{ route('projects.index', array_merge(request()->except(['page']), ['preset' => 'overdue'])) }}"
            class="px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 whitespace-nowrap {{ request('preset') === 'overdue' ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>Proyek Terlambat</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('preset') === 'overdue' ? 'bg-indigo-700 text-white' : 'bg-rose-100 text-rose-700' }}">{{ $counts['overdue'] }}</span>
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs mb-6" x-data="{ advanced: {{ request('manager_id') || request('date_from') || request('date_to') ? 'true' : 'false' }} }">
        <form action="{{ route('projects.index') }}" method="GET" class="space-y-3">
            @if(request('preset'))
                <input type="hidden" name="preset" value="{{ request('preset') }}">
            @endif
            <input type="hidden" name="view" value="{{ $viewMode }}">

            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari berdasarkan nama proyek, kode, klien, atau PIC..."
                        class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white"
                    >
                </div>

                <!-- Status Select -->
                <select
                    name="status"
                    class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white"
                >
                    <option value="">Semua Status</option>
                    <option value="not_started" {{ request('status') === 'not_started' ? 'selected' : '' }}>Belum Dimulai</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                    <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>Ditunda</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                </select>

                <!-- Priority Select -->
                <select
                    name="priority"
                    class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white"
                >
                    <option value="">Semua Prioritas</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritis</option>
                </select>

                <!-- Sorting Select -->
                <select
                    name="sort"
                    class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white"
                >
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Urut: Terbaru</option>
                    <option value="deadline" {{ request('sort') === 'deadline' ? 'selected' : '' }}>Urut: Deadline</option>
                    <option value="progress" {{ request('sort') === 'progress' ? 'selected' : '' }}>Urut: Progress</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Urut: Nama</option>
                    <option value="budget" {{ request('sort') === 'budget' ? 'selected' : '' }}>Urut: Anggaran</option>
                </select>

                <!-- Filter & Reset Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg transition-colors">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'status', 'priority', 'sort', 'manager_id', 'date_from', 'date_to', 'preset']))
                        <a href="{{ route('projects.index') }}" class="px-2.5 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium rounded-lg hover:bg-slate-100 transition-colors">
                            Reset
                        </a>
                    @endif
                    <!-- Toggle Advanced Filters -->
                    <button
                        @click="advanced = !advanced"
                        type="button"
                        class="p-2 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition-colors"
                        title="Filter Lanjutan"
                    >
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </button>

                    <!-- View Switcher (Table vs Grid) -->
                    <div class="flex items-center border border-slate-200 rounded-lg p-0.5 bg-slate-50 ml-auto">
                        <a
                            href="{{ route('projects.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                            class="p-1.5 rounded-md transition-colors {{ $viewMode === 'table' ? 'bg-white shadow-2xs text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}"
                            title="Tampilan Tabel"
                        >
                            <i data-lucide="list" class="w-3.5 h-3.5"></i>
                        </a>
                        <a
                            href="{{ route('projects.index', array_merge(request()->query(), ['view' => 'grid'])) }}"
                            class="p-1.5 rounded-md transition-colors {{ $viewMode === 'grid' ? 'bg-white shadow-2xs text-indigo-600' : 'text-slate-400 hover:text-slate-700' }}"
                            title="Tampilan Grid"
                        >
                            <i data-lucide="grid" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters Drawer -->
            <div x-show="advanced" x-cloak class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
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
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Deadline Hingga:</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5">
                </div>
            </div>
        </form>
    </div>

    <!-- Content: Table View or Grid View -->
    @if($projects->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200/80 p-8 shadow-2xs">
            <x-empty-state
                title="Tidak Ada Proyek Ditemukan"
                description="Tidak ada proyek yang sesuai dengan kriteria pencarian atau filter yang dipilih."
                :action-url="route('projects.create')"
                action-label="Tambah Proyek Baru"
            />
        </div>
    @elseif($viewMode === 'grid')
        <!-- GRID VIEW -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-6">
            @foreach($projects as $project)
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-2xs hover:shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-mono font-medium text-slate-400">{{ $project->code }}</span>
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
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 hover:text-indigo-600 transition-colors line-clamp-2">
                            <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                        </h3>

                        @if($project->client)
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 truncate">
                                <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                {{ $project->client }}
                            </p>
                        @endif

                        <p class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed">
                            {{ $project->description ?? 'Tidak ada deskripsi rinci.' }}
                        </p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="text-slate-500 font-medium">Progress</span>
                            <span class="font-bold text-slate-800">{{ $project->progress }}%</span>
                        </div>
                        <x-progress-bar :value="$project->progress" size="sm" />

                        <div class="mt-4 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <x-avatar :name="$project->manager?->name ?? 'Belum Ditentukan'" size="sm" />
                                <span class="text-slate-700 font-medium truncate max-w-[100px]">
                                    {{ $project->manager?->name ?? '-' }}
                                </span>
                            </div>
                            <span class="{{ $project->is_overdue ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                                {{ $project->deadline->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- TABLE VIEW -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">Kode</th>
                            <th class="py-3 px-5">Nama Proyek</th>
                            <th class="py-3 px-5">PIC</th>
                            <th class="py-3 px-5 w-36">Progress</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5">Prioritas</th>
                            <th class="py-3 px-5">Deadline</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($projects as $project)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-mono text-slate-500 font-medium whitespace-nowrap">
                                    {{ $project->code }}
                                </td>
                                <td class="py-3.5 px-5">
                                    <a href="{{ route('projects.show', $project) }}" class="font-semibold text-slate-900 hover:text-indigo-600 transition-colors block max-w-sm truncate">
                                        {{ $project->name }}
                                    </a>
                                    @if($project->client)
                                        <span class="text-[11px] text-slate-400 block truncate">{{ $project->client }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
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
                                <td class="py-3.5 px-5 whitespace-nowrap">
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
                                <td class="py-3.5 px-5 whitespace-nowrap">
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
                                <td class="py-3.5 px-5 text-right whitespace-nowrap" x-data="{ openDelete: false }">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('projects.show', $project) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-md transition-colors" title="Lihat">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        @can('update', $project)
                                            <a href="{{ route('projects.edit', $project) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-slate-100 rounded-md transition-colors" title="Ubah">
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $project)
                                            <button
                                                type="button"
                                                @click="$dispatch('open-modal', 'delete-project-{{ $project->id }}')"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-md transition-colors"
                                                title="Hapus"
                                            >
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>

                                            <!-- Delete Confirmation Modal (Section 21) -->
                                            <x-modal name="delete-project-{{ $project->id }}" title="Konfirmasi Hapus Proyek">
                                                <div class="text-left space-y-3">
                                                    <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-2">
                                                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                                                    </div>
                                                    <p class="text-xs sm:text-sm text-slate-700 text-center">
                                                        Apakah Anda yakin ingin menghapus proyek <strong>"{{ $project->name }}"</strong>?
                                                    </p>
                                                    <p class="text-xs text-slate-500 text-center">
                                                        Tindakan ini tidak dapat dibatalkan. Seluruh task, milestone, dan aktivitas terkait akan terhapus secara permanen.
                                                    </p>
                                                    <div class="pt-4 flex items-center justify-end gap-2.5">
                                                        <button
                                                            type="button"
                                                            @click="$dispatch('close-modal', 'delete-project-{{ $project->id }}')"
                                                            class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 rounded-lg border border-slate-200 transition-colors"
                                                        >
                                                            Batal
                                                        </button>
                                                        <form action="{{ route('projects.destroy', $project) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs transition-colors"
                                                            >
                                                                Hapus Proyek
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </x-modal>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</x-app-layout>
