<x-app-layout :title="$project->name">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Proyek' => route('projects.index'), $project->code => null]" />
    </x-slot:breadcrumb>

    <!-- Project Header Banner (Section 10) -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="space-y-2">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="text-xs font-mono font-semibold px-2.5 py-0.5 rounded bg-slate-100 text-slate-700">
                        {{ $project->code }}
                    </span>
                    @php
                        $statusType = match($project->status) {
                            'completed' => 'success',
                            'in_progress' => 'info',
                            'overdue' => 'danger',
                            'on_hold' => 'warning',
                            default => 'neutral',
                        };
                        $prioType = match($project->priority) {
                            'critical' => 'danger',
                            'high' => 'warning',
                            'medium' => 'info',
                            'low' => 'neutral',
                            default => 'neutral',
                        };
                    @endphp
                    <x-badge :type="$statusType" size="md">
                        {{ $project->status_label }}
                    </x-badge>
                    <x-badge :type="$prioType" size="md">
                        Prioritas {{ $project->priority_label }}
                    </x-badge>
                    @if($project->is_overdue)
                        <span class="text-xs font-semibold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded flex items-center gap-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            Terlambat {{ abs($project->days_remaining) }} Hari
                        </span>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                    {{ $project->name }}
                </h1>

                @if($project->client)
                    <p class="text-xs text-slate-500 flex items-center gap-1.5">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-400"></i>
                        Klien / Pemilik Proyek: <strong class="text-slate-700">{{ $project->client }}</strong>
                    </p>
                @endif
            </div>

            <!-- Right: Actions & Metadata -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 shrink-0">
                <div class="flex items-center gap-3 pr-4 sm:border-r border-slate-200">
                    <x-avatar :name="$project->manager?->name ?? 'Belum Ada'" size="md" />
                    <div>
                        <span class="block text-[11px] text-slate-400 font-medium">Penanggung Jawab</span>
                        <span class="text-xs font-bold text-slate-800">{{ $project->manager?->name ?? 'Belum Ada' }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @can('update', $project)
                        <a
                            href="{{ route('projects.edit', $project) }}"
                            class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 rounded-lg border border-slate-200 transition-colors flex items-center gap-1.5 shadow-2xs"
                        >
                            <i data-lucide="edit-3" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Ubah</span>
                        </a>
                    @endcan
                    @can('create', [\App\Models\Task::class, $project])
                        <button
                            type="button"
                            @click="$dispatch('open-modal', 'modal-add-task')"
                            class="px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors flex items-center gap-1.5"
                        >
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Tambah Task</span>
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Big Minimalist Progress Indicator -->
        <div class="mt-6 pt-6 border-t border-slate-100">
            <div class="flex items-center justify-between text-xs mb-2">
                <div class="flex items-center gap-3">
                    <span class="font-semibold text-slate-700">Progres Keseluruhan</span>
                    <span class="text-slate-400">|</span>
                    <span class="text-slate-500">
                        {{ $doneTasks }} dari {{ $totalTasks }} task diselesaikan
                    </span>
                    <span class="text-slate-400">|</span>
                    <span class="text-slate-500">
                        {{ $milestonesCompleted }} dari {{ $milestonesTotal }} milestone tercapai
                    </span>
                </div>
                <span class="text-sm font-bold text-indigo-600">{{ $project->progress }}%</span>
            </div>
            <x-progress-bar :value="$project->progress" size="lg" />
        </div>
    </div>

    <!-- Navigation Tabs (Section 10) -->
    <div class="border-b border-slate-200 mb-6">
        <nav class="flex space-x-1 sm:space-x-4 overflow-x-auto text-xs font-semibold" aria-label="Tabs">
            <a
                href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'overview']) }}"
                class="py-3 px-3.5 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'overview' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}"
            >
                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                Overview
            </a>
            <a
                href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'tasks', 'task_view' => $taskView]) }}"
                class="py-3 px-3.5 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'tasks' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}"
            >
                <i data-lucide="check-square" class="w-4 h-4"></i>
                Tasks
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'tasks' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $totalTasks }}
                </span>
            </a>
            <a
                href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'milestones']) }}"
                class="py-3 px-3.5 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'milestones' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}"
            >
                <i data-lucide="flag" class="w-4 h-4"></i>
                Milestones
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $activeTab === 'milestones' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $milestonesTotal }}
                </span>
            </a>
            <a
                href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'timeline']) }}"
                class="py-3 px-3.5 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'timeline' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}"
            >
                <i data-lucide="calendar" class="w-4 h-4"></i>
                Timeline
            </a>
            <a
                href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'activity']) }}"
                class="py-3 px-3.5 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'activity' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}"
            >
                <i data-lucide="activity" class="w-4 h-4"></i>
                Aktivitas
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600">
                    {{ $project->activities->count() }}
                </span>
            </a>
            <a
                href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'files']) }}"
                class="py-3 px-3.5 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'files' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300' }}"
            >
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Dokumen & File
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600">
                    {{ $project->files->count() }}
                </span>
            </a>
        </nav>
    </div>

    <!-- TAB 1: OVERVIEW -->
    @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Description & Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Deskripsi Card -->
                <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Deskripsi Ruang Lingkup Proyek</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                        {{ $project->description ?: 'Belum ada deskripsi yang ditambahkan untuk proyek ini.' }}
                    </p>
                </div>

                <!-- Catatan / Hambatan Card -->
                @if($project->notes)
                    <div class="bg-amber-50/50 border border-amber-200/80 rounded-xl p-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-2 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                            Catatan Khusus & Mitigasi
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $project->notes }}
                        </p>
                    </div>
                @endif

                <!-- Mini Tasks Summary -->
                <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-900">Task Teratas</h3>
                        <a href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'tasks']) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Kelola Semua Task &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($project->tasks->take(4) as $t)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition-colors text-xs">
                                <div class="flex items-center gap-3 min-w-0">
                                    <form action="{{ route('tasks.status', $t) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $t->status === 'done' ? 'todo' : 'done' }}">
                                        <button type="submit" class="w-4.5 h-4.5 rounded border flex items-center justify-center transition-colors {{ $t->status === 'done' ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 hover:border-indigo-500' }}">
                                            @if($t->status === 'done')
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                            @endif
                                        </button>
                                    </form>
                                    <div class="min-w-0">
                                        <p class="font-medium text-slate-800 truncate {{ $t->status === 'done' ? 'line-through text-slate-400' : '' }}">
                                            {{ $t->title }}
                                        </p>
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5">
                                            <span>PIC: {{ $t->assignee?->name ?? 'Belum Ditugaskan' }}</span>
                                            @if($t->due_date)
                                                <span>&bull;</span>
                                                <span class="{{ $t->is_overdue ? 'text-rose-500 font-semibold' : '' }}">
                                                    Deadline: {{ $t->due_date->format('d M') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <x-badge :type="match($t->priority) { 'critical'=>'danger', 'high'=>'warning', 'medium'=>'info', default=>'neutral' }" size="sm">
                                    {{ $t->priority_label }}
                                </x-badge>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">Belum ada task pada proyek ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Key Metadata & Timelines -->
            <div class="space-y-6">
                <!-- Info Card -->
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-2xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2.5">
                        Detail Informasi Proyek
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Tanggal Mulai</span>
                            <span class="font-semibold text-slate-700">{{ $project->start_date->format('d F Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Target Selesai</span>
                            <span class="font-semibold {{ $project->is_overdue ? 'text-rose-600' : 'text-slate-700' }}">
                                {{ $project->deadline->format('d F Y') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Sisa Waktu</span>
                            @if($project->status === 'completed')
                                <span class="font-semibold text-emerald-600">Proyek Selesai</span>
                            @elseif($project->days_remaining < 0)
                                <span class="font-semibold text-rose-600">Lewat {{ abs($project->days_remaining) }} Hari</span>
                            @else
                                <span class="font-semibold text-slate-700">{{ $project->days_remaining }} Hari lagi</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Alokasi Anggaran</span>
                            <span class="font-bold text-slate-900">{{ $project->formatted_budget }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Status</span>
                            <x-badge :type="$statusType" size="sm">{{ $project->status_label }}</x-badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Prioritas</span>
                            <x-badge :type="$prioType" size="sm">{{ $project->priority_label }}</x-badge>
                        </div>
                    </div>
                </div>

                <!-- Next Milestones Widget -->
                <div class="bg-white rounded-xl border border-slate-200/80 p-5 shadow-2xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 mb-3">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                            Tahapan Milestone
                        </h3>
                        <a href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'milestones']) }}" class="text-[11px] font-semibold text-indigo-600">
                            Semua &rarr;
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse($project->milestones->take(3) as $m)
                            <div class="flex items-start gap-2.5 text-xs">
                                <div class="mt-0.5">
                                    @if($m->is_completed)
                                        <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <i data-lucide="check" class="w-2.5 h-2.5 stroke-[3]"></i>
                                        </div>
                                    @else
                                        <div class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-slate-800 truncate {{ $m->is_completed ? 'line-through text-slate-400' : '' }}">
                                        {{ $m->name }}
                                    </p>
                                    <span class="text-[10px] text-slate-400">{{ $m->target_date->format('d M Y') }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-2">Belum ada milestone.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 2: TASKS (List & Kanban Views) -->
    @if($activeTab === 'tasks')
        <div class="space-y-4">
            <!-- Tasks Toolbar -->
            <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500">Tampilan Task:</span>
                    <div class="flex items-center border border-slate-200 rounded-lg p-0.5 bg-slate-50">
                        <a
                            href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'tasks', 'task_view' => 'list']) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors flex items-center gap-1.5 {{ $taskView === 'list' ? 'bg-white shadow-2xs text-indigo-600' : 'text-slate-500 hover:text-slate-900' }}"
                        >
                            <i data-lucide="list" class="w-3.5 h-3.5"></i>
                            <span>List View</span>
                        </a>
                        <a
                            href="{{ route('projects.show', ['project' => $project->id, 'tab' => 'tasks', 'task_view' => 'kanban']) }}"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition-colors flex items-center gap-1.5 {{ $taskView === 'kanban' ? 'bg-white shadow-2xs text-indigo-600' : 'text-slate-500 hover:text-slate-900' }}"
                        >
                            <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                            <span>Kanban Board</span>
                        </a>
                    </div>
                </div>

                @can('create', [\App\Models\Task::class, $project])
                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'modal-add-task')"
                        class="px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors flex items-center justify-center gap-1.5"
                    >
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Task</span>
                    </button>
                @endcan
            </div>

            <!-- KANBAN VIEW (Section 11) -->
            @if($taskView === 'kanban')
                @php
                    $columns = [
                        'todo' => ['title' => 'To Do', 'color' => 'slate', 'tasks' => $project->tasks->where('status', 'todo')],
                        'in_progress' => ['title' => 'In Progress', 'color' => 'blue', 'tasks' => $project->tasks->where('status', 'in_progress')],
                        'review' => ['title' => 'Under Review', 'color' => 'amber', 'tasks' => $project->tasks->where('status', 'review')],
                        'done' => ['title' => 'Done', 'color' => 'emerald', 'tasks' => $project->tasks->where('status', 'done')],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
                    @foreach($columns as $colKey => $colData)
                        <div class="bg-slate-100/70 border border-slate-200/80 rounded-xl p-3 flex flex-col min-h-[400px]">
                            <!-- Column Header -->
                            <div class="flex items-center justify-between pb-3 px-1 border-b border-slate-200/60 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ match($colKey) { 'done'=>'bg-emerald-500', 'in_progress'=>'bg-blue-500', 'review'=>'bg-amber-500', default=>'bg-slate-400' } }}"></span>
                                    <h4 class="text-xs font-bold text-slate-800">{{ $colData['title'] }}</h4>
                                </div>
                                <span class="text-[11px] font-semibold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                                    {{ $colData['tasks']->count() }}
                                </span>
                            </div>

                            <!-- Cards List -->
                            <div class="space-y-3 flex-1">
                                @forelse($colData['tasks'] as $task)
                                    <div class="bg-white rounded-lg border border-slate-200/80 p-3.5 shadow-2xs hover:shadow-xs transition-shadow">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <x-badge :type="match($task->priority) { 'critical'=>'danger', 'high'=>'warning', 'medium'=>'info', default=>'neutral' }" size="sm">
                                                {{ $task->priority_label }}
                                            </x-badge>

                                            <!-- Move Status Dropdown -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" type="button" class="text-slate-400 hover:text-slate-700 p-1">
                                                    <i data-lucide="more-horizontal" class="w-3.5 h-3.5"></i>
                                                </button>
                                                <div
                                                    x-show="open"
                                                    @click.outside="open = false"
                                                    class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-30 text-xs"
                                                    x-cloak
                                                >
                                                    <span class="block px-3 py-1 text-[10px] uppercase font-bold text-slate-400">Pindah Status:</span>
                                                    @foreach(['todo' => 'To Do', 'in_progress' => 'In Progress', 'review' => 'Review', 'done' => 'Done'] as $stKey => $stLabel)
                                                        @if($stKey !== $task->status)
                                                            <form action="{{ route('tasks.status', $task) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="{{ $stKey }}">
                                                                <button type="submit" class="w-full text-left px-3 py-1 text-slate-700 hover:bg-slate-50">
                                                                    &rarr; {{ $stLabel }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endforeach
                                                    <div class="border-t border-slate-100 my-1"></div>
                                                    @can('delete', $task)
                                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Hapus task ini?')" class="w-full text-left px-3 py-1 text-rose-600 hover:bg-rose-50">
                                                                Hapus Task
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="text-xs font-bold text-slate-900 leading-snug">
                                            {{ $task->title }}
                                        </h5>

                                        @if($task->description)
                                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                                {{ $task->description }}
                                            </p>
                                        @endif

                                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                            <div class="flex items-center gap-1.5" title="Ditugaskan kepada: {{ $task->assignee?->name ?? 'Belum Ada' }}">
                                                <x-avatar :name="$task->assignee?->name ?? 'Belum Ada'" size="sm" />
                                                <span class="text-slate-600 truncate max-w-[80px]">
                                                    {{ $task->assignee?->name ?? '-' }}
                                                </span>
                                            </div>

                                            @if($task->due_date)
                                                <span class="{{ $task->is_overdue ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                                    {{ $task->due_date->format('d M') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="h-24 flex items-center justify-center border border-dashed border-slate-200 rounded-lg text-slate-400 text-xs">
                                        Kosong
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- LIST VIEW (Section 11) -->
                <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3 px-5 w-10"></th>
                                    <th class="py-3 px-5">Judul Task</th>
                                    <th class="py-3 px-5">Assignee</th>
                                    <th class="py-3 px-5">Prioritas</th>
                                    <th class="py-3 px-5">Status</th>
                                    <th class="py-3 px-5">Batas Waktu</th>
                                    <th class="py-3 px-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($project->tasks as $t)
                                    <tr class="hover:bg-slate-50/60 transition-colors {{ $t->status === 'done' ? 'bg-slate-50/30' : '' }}">
                                        <td class="py-3.5 px-5">
                                            <form action="{{ route('tasks.status', $t) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $t->status === 'done' ? 'todo' : 'done' }}">
                                                <button type="submit" class="w-4 h-4 rounded border flex items-center justify-center transition-colors {{ $t->status === 'done' ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 hover:border-indigo-500' }}">
                                                    @if($t->status === 'done')
                                                        <i data-lucide="check" class="w-3 h-3"></i>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td class="py-3.5 px-5">
                                            <span class="font-semibold text-slate-900 block {{ $t->status === 'done' ? 'line-through text-slate-400' : '' }}">
                                                {{ $t->title }}
                                            </span>
                                            @if($t->description)
                                                <span class="text-[11px] text-slate-500 block truncate max-w-sm">{{ $t->description }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <x-avatar :name="$t->assignee?->name ?? 'Belum Ditugaskan'" size="sm" />
                                                <span class="text-slate-700 font-medium">{{ $t->assignee?->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <x-badge :type="match($t->priority) { 'critical'=>'danger', 'high'=>'warning', 'medium'=>'info', default=>'neutral' }" size="sm">
                                                {{ $t->priority_label }}
                                            </x-badge>
                                        </td>
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <!-- Fast Status Dropdown -->
                                            <form action="{{ route('tasks.status', $t) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <select
                                                    name="status"
                                                    onchange="this.form.submit()"
                                                    class="text-xs bg-slate-50 border border-slate-200 rounded-md px-2 py-1 text-slate-700 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                                >
                                                    <option value="todo" {{ $t->status === 'todo' ? 'selected' : '' }}>To Do</option>
                                                    <option value="in_progress" {{ $t->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="review" {{ $t->status === 'review' ? 'selected' : '' }}>Under Review</option>
                                                    <option value="done" {{ $t->status === 'done' ? 'selected' : '' }}>Done</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="py-3.5 px-5 whitespace-nowrap">
                                            <span class="{{ $t->is_overdue ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                                {{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                            @can('delete', $t)
                                                <form action="{{ route('tasks.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Hapus task ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8">
                                            <x-empty-state
                                                title="Belum Ada Task"
                                                description="Buat task pertama Anda untuk memecah proyek ini ke dalam kegiatan terukur."
                                            />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 3: MILESTONES (Section 12) -->
    @if($activeTab === 'milestones')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tahapan Milestone Proyek</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Target pencapaian besar dalam siklus hidup proyek.</p>
                </div>
                @can('create', [\App\Models\Milestone::class, $project])
                    <button
                        type="button"
                        @click="$dispatch('open-modal', 'modal-add-milestone')"
                        class="px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition-colors flex items-center gap-1.5"
                    >
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah Milestone</span>
                    </button>
                @endcan
            </div>

            <!-- Milestone Timeline Visual (Section 12) -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($project->milestones as $m)
                        <div class="relative group">
                            <!-- Milestone Dot Indicator -->
                            <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 rounded-full flex items-center justify-center {{ $m->is_completed ? 'bg-emerald-600 text-white shadow-xs' : ($m->status === 'in_progress' ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white border-2 border-slate-300 text-slate-400') }}">
                                @if($m->is_completed)
                                    <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                                @elseif($m->status === 'in_progress')
                                    <i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                @endif
                            </div>

                            <!-- Milestone Card Content -->
                            <div class="bg-slate-50/60 border rounded-xl p-4 sm:p-5 transition-all {{ $m->is_completed ? 'border-emerald-200/70 bg-emerald-50/20' : 'border-slate-200/80' }}">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="text-sm font-bold {{ $m->is_completed ? 'text-slate-800' : 'text-slate-900' }}">
                                                {{ $m->name }}
                                            </h4>
                                            <x-badge :type="match($m->status) { 'completed'=>'success', 'in_progress'=>'info', default=>'neutral' }" size="sm">
                                                {{ $m->status_label }}
                                            </x-badge>
                                        </div>
                                        <p class="text-xs text-slate-500 leading-relaxed max-w-2xl">
                                            {{ $m->description ?: 'Tidak ada keterangan tambahan.' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="text-right">
                                            <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Target Tanggal</span>
                                            <span class="text-xs font-semibold text-slate-700">{{ $m->target_date->format('d M Y') }}</span>
                                        </div>

                                        <!-- Toggle Button -->
                                        @can('update', $m)
                                            <form action="{{ route('milestones.toggle', $m) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors flex items-center gap-1.5 {{ $m->is_completed ? 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' : 'bg-emerald-600 text-white border-transparent hover:bg-emerald-700' }}"
                                                >
                                                    <i data-lucide="{{ $m->is_completed ? 'rotate-ccw' : 'check' }}" class="w-3.5 h-3.5"></i>
                                                    <span>{{ $m->is_completed ? 'Batal Selesai' : 'Tandai Selesai' }}</span>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('delete', $m)
                                            <form action="{{ route('milestones.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus milestone ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-empty-state
                                title="Belum Ada Milestone"
                                description="Bagi proyek Anda menjadi tahapan penting seperti Analisis, Desain, UAT, dan Peluncuran."
                            />
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 4: TIMELINE (Section 13) -->
    @if($activeTab === 'timeline')
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-slate-900">Timeline & Kronologi Proyek</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Urutan jadwal proyek mulai dari kickoff hingga target penyelesaian akhir.</p>
                </div>

                <!-- Timeline Bar Visual -->
                <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-indigo-100">
                    <!-- Start Point -->
                    <div class="relative">
                        <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center">
                            <i data-lucide="play" class="w-3 h-3 fill-current"></i>
                        </div>
                        <div class="p-3 bg-indigo-50/50 rounded-lg border border-indigo-100">
                            <span class="text-xs font-bold text-indigo-900">Kickoff Proyek</span>
                            <span class="text-xs text-indigo-600 ml-2">{{ $project->start_date->format('d F Y') }}</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Awal resmi pengerjaan proyek dimulai.</p>
                        </div>
                    </div>

                    <!-- Milestones in Timeline -->
                    @foreach($project->milestones as $m)
                        <div class="relative">
                            <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 rounded-full {{ $m->is_completed ? 'bg-emerald-500' : 'bg-slate-400' }} text-white flex items-center justify-center">
                                <i data-lucide="flag" class="w-3 h-3"></i>
                            </div>
                            <div class="p-3 bg-white rounded-lg border border-slate-200 flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-slate-800">{{ $m->name }}</span>
                                    <span class="text-[11px] text-slate-500 ml-2">Target: {{ $m->target_date->format('d M Y') }}</span>
                                </div>
                                <x-badge :type="$m->is_completed ? 'success' : 'neutral'" size="sm">
                                    {{ $m->status_label }}
                                </x-badge>
                            </div>
                        </div>
                    @endforeach

                    <!-- Deadline End Point -->
                    <div class="relative">
                        <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 rounded-full {{ $project->is_overdue ? 'bg-rose-600' : 'bg-indigo-900' }} text-white flex items-center justify-center">
                            <i data-lucide="target" class="w-3.5 h-3.5"></i>
                        </div>
                        <div class="p-3 rounded-lg border {{ $project->is_overdue ? 'bg-rose-50 border-rose-200' : 'bg-slate-50 border-slate-200' }}">
                            <span class="text-xs font-bold {{ $project->is_overdue ? 'text-rose-900' : 'text-slate-900' }}">
                                Target Deadline Final
                            </span>
                            <span class="text-xs font-semibold ml-2 {{ $project->is_overdue ? 'text-rose-700' : 'text-slate-700' }}">
                                {{ $project->deadline->format('d F Y') }}
                            </span>
                            @if($project->is_overdue)
                                <p class="text-[11px] text-rose-600 mt-0.5 font-medium">Perhatian: Proyek melewati batas waktu awal yang direncanakan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- TAB 5: ACTIVITY (Section 14) -->
    @if($activeTab === 'activity')
        <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
            <h3 class="text-sm font-bold text-slate-900 mb-4">Riwayat Aktivitas Proyek</h3>

            <div class="space-y-4">
                @forelse($project->activities as $act)
                    <div class="flex items-start gap-3 pb-4 border-b border-slate-100 last:border-b-0 text-xs">
                        <x-avatar :name="$act->user?->name ?? 'Sistem'" size="sm" class="mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 leading-snug">
                                {{ $act->description }}
                            </p>
                            <span class="text-[10px] text-slate-400 mt-1 block">
                                {{ $act->created_at->diffForHumans() }} ({{ $act->created_at->format('d M Y, H:i') }})
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada catatan aktivitas pada proyek ini.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 6: FILES -->
    @if($activeTab === 'files')
        <div class="space-y-6">
            <!-- Upload Box -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Unggah Dokumen Proyek</h3>
                <form action="{{ route('projects.files.store', $project) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @csrf
                    <input
                        type="file"
                        name="document"
                        required
                        class="text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                    >
                    <input
                        type="text"
                        name="name"
                        placeholder="Nama tampilan dokumen (opsional)"
                        class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 flex-1"
                    >
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors shrink-0 flex items-center justify-center gap-1.5"
                    >
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span>Unggah</span>
                    </button>
                </form>
            </div>

            <!-- Files List -->
            <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Berkas & Lampiran Terkait</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($project->files as $file)
                        <div class="py-3 flex items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                    <i data-lucide="file" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">{{ $file->name }}</p>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $file->formatted_size }} &bull; Diunggah oleh {{ $file->user?->name ?? 'User' }} &bull; {{ $file->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form action="{{ route('projects.files.destroy', [$project, $file]) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Belum ada file dokumen yang diunggah.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: TAMBAH TASK -->
    <x-modal name="modal-add-task" title="Tambah Task Baru">
        <form action="{{ route('tasks.store', $project) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label for="task_title" class="block font-semibold text-slate-700 mb-1">
                    Judul Task <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="task_title"
                    name="title"
                    required
                    placeholder="Contoh: Integrasi API Payment Gateway"
                    class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="task_desc" class="block font-semibold text-slate-700 mb-1">Deskripsi Kegiatan</label>
                <textarea
                    id="task_desc"
                    name="description"
                    rows="2"
                    placeholder="Keterangan detail ruang lingkup task..."
                    class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="task_assigned" class="block font-semibold text-slate-700 mb-1">Assignee (PIC)</label>
                    <select id="task_assigned" name="assigned_to" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                        <option value="">-- Tanpa Assignee --</option>
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="task_priority" class="block font-semibold text-slate-700 mb-1">Prioritas</label>
                    <select id="task_priority" name="priority" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                        <option value="low">Rendah (Low)</option>
                        <option value="medium" selected>Sedang (Medium)</option>
                        <option value="high">Tinggi (High)</option>
                        <option value="critical">Kritis (Critical)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="task_start" class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="task_start" name="start_date" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                </div>
                <div>
                    <label for="task_due" class="block font-semibold text-slate-700 mb-1">Batas Waktu</label>
                    <input type="date" id="task_due" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                </div>
            </div>

            <div>
                <label for="task_status" class="block font-semibold text-slate-700 mb-1">Status Awal</label>
                <select id="task_status" name="status" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                    <option value="todo" selected>To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="review">Under Review</option>
                    <option value="done">Done</option>
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" @click="$dispatch('close-modal', 'modal-add-task')" class="px-3.5 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 font-semibold text-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-xs">
                    Simpan Task
                </button>
            </div>
        </form>
    </x-modal>

    <!-- MODAL: TAMBAH MILESTONE -->
    <x-modal name="modal-add-milestone" title="Tambah Milestone Baru">
        <form action="{{ route('milestones.store', $project) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label for="milestone_name" class="block font-semibold text-slate-700 mb-1">
                    Nama Milestone <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="milestone_name"
                    name="name"
                    required
                    placeholder="Contoh: Deployment & BAP Serah Terima"
                    class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="milestone_desc" class="block font-semibold text-slate-700 mb-1">Deskripsi Capaian</label>
                <textarea
                    id="milestone_desc"
                    name="description"
                    rows="2"
                    placeholder="Objektif yang harus dipenuhi pada milestone ini..."
                    class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="milestone_date" class="block font-semibold text-slate-700 mb-1">Target Tanggal <span class="text-rose-500">*</span></label>
                    <input type="date" id="milestone_date" name="target_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                </div>
                <div>
                    <label for="milestone_status" class="block font-semibold text-slate-700 mb-1">Status Awal</label>
                    <select id="milestone_status" name="status" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-2">
                        <option value="pending" selected>Pending</option>
                        <option value="in_progress">Sedang Berjalan</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" @click="$dispatch('close-modal', 'modal-add-milestone')" class="px-3.5 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 font-semibold text-slate-700">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-xs">
                    Simpan Milestone
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
