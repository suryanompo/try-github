<x-app-layout title="Manajemen Task">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Task' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Manajemen Task & Kegiatan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau dan kelola seluruh task dari semua proyek dalam satu tempat.</p>
        </div>

        <div class="flex items-center border border-slate-200 rounded-lg p-0.5 bg-white shadow-2xs">
            <a
                href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'kanban'])) }}"
                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors flex items-center gap-1.5 {{ $viewMode === 'kanban' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:text-slate-900' }}"
            >
                <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                <span>Kanban Board</span>
            </a>
            <a
                href="{{ route('tasks.index', array_merge(request()->query(), ['view' => 'list'])) }}"
                class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors flex items-center gap-1.5 {{ $viewMode === 'list' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:text-slate-900' }}"
            >
                <i data-lucide="list" class="w-3.5 h-3.5"></i>
                <span>List View</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs mb-6">
        <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <input type="hidden" name="view" value="{{ $viewMode }}">

            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul task atau deskripsi..."
                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                >
            </div>

            <!-- Proyek Filter -->
            <select name="project_id" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700">
                <option value="">Semua Proyek</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ mb_strimwidth($p->name, 0, 25, '...') }}</option>
                @endforeach
            </select>

            <!-- Priority Filter -->
            <select name="priority" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700">
                <option value="">Semua Prioritas</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritis</option>
            </select>

            <!-- Assignee Filter -->
            <select name="assigned_to" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700">
                <option value="">Semua PIC Assignee</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <!-- Actions -->
            <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg">
                Terapkan
            </button>
            @if(request()->hasAny(['search', 'project_id', 'priority', 'assigned_to', 'status']))
                <a href="{{ route('tasks.index', ['view' => $viewMode]) }}" class="px-2.5 py-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- KANBAN VIEW -->
    @if($viewMode === 'kanban')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
            @php
                $columns = [
                    'todo' => ['title' => 'To Do', 'color' => 'slate', 'items' => $tasksByStatus['todo']],
                    'in_progress' => ['title' => 'In Progress', 'color' => 'blue', 'items' => $tasksByStatus['in_progress']],
                    'review' => ['title' => 'Under Review', 'color' => 'amber', 'items' => $tasksByStatus['review']],
                    'done' => ['title' => 'Done', 'color' => 'emerald', 'items' => $tasksByStatus['done']],
                ];
            @endphp

            @foreach($columns as $colKey => $colData)
                <div class="bg-slate-100/70 border border-slate-200/80 rounded-xl p-3 flex flex-col min-h-[450px]">
                    <div class="flex items-center justify-between pb-3 px-1 border-b border-slate-200/60 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ match($colKey) { 'done'=>'bg-emerald-500', 'in_progress'=>'bg-blue-500', 'review'=>'bg-amber-500', default=>'bg-slate-400' } }}"></span>
                            <h4 class="text-xs font-bold text-slate-800">{{ $colData['title'] }}</h4>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                            {{ $colData['items']->count() }}
                        </span>
                    </div>

                    <div class="space-y-3 flex-1">
                        @forelse($colData['items'] as $task)
                            <div class="bg-white rounded-lg border border-slate-200/80 p-3.5 shadow-2xs hover:shadow-xs transition-shadow">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <span class="text-[10px] font-mono text-slate-400 truncate max-w-[120px]">
                                        {{ $task->project->code }}
                                    </span>
                                    <x-badge :type="match($task->priority) { 'critical'=>'danger', 'high'=>'warning', 'medium'=>'info', default=>'neutral' }" size="sm">
                                        {{ $task->priority_label }}
                                    </x-badge>
                                </div>

                                <h5 class="text-xs font-bold text-slate-900 leading-snug">
                                    {{ $task->title }}
                                </h5>

                                <p class="text-[10px] text-slate-400 mt-1 truncate">
                                    <a href="{{ route('projects.show', $task->project_id) }}" class="hover:underline text-slate-500">
                                        {{ $task->project->name }}
                                    </a>
                                </p>

                                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                    <div class="flex items-center gap-1.5" title="{{ $task->assignee?->name ?? 'Belum Ditugaskan' }}">
                                        <x-avatar :name="$task->assignee?->name ?? 'Belum Ditugaskan'" size="sm" />
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
                                Tidak ada task
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- LIST VIEW -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">Proyek</th>
                            <th class="py-3 px-5">Judul Task</th>
                            <th class="py-3 px-5">Assignee</th>
                            <th class="py-3 px-5">Prioritas</th>
                            <th class="py-3 px-5">Status</th>
                            <th class="py-3 px-5">Batas Waktu</th>
                            <th class="py-3 px-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($paginatedTasks as $t)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <a href="{{ route('projects.show', $t->project_id) }}" class="font-mono text-indigo-600 hover:underline">
                                        {{ $t->project->code }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-5">
                                    <span class="font-semibold text-slate-900 block {{ $t->status === 'done' ? 'line-through text-slate-400' : '' }}">
                                        {{ $t->title }}
                                    </span>
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
                                    <x-badge :type="match($t->status) { 'done'=>'success', 'in_progress'=>'info', 'review'=>'warning', default=>'neutral' }" size="sm">
                                        {{ $t->status_label }}
                                    </x-badge>
                                </td>
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="{{ $t->is_overdue ? 'text-rose-600 font-semibold' : 'text-slate-600' }}">
                                        {{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <a href="{{ route('projects.show', ['project' => $t->project_id, 'tab' => 'tasks']) }}" class="text-indigo-600 font-semibold hover:underline">
                                        Buka Proyek &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8">
                                    <x-empty-state
                                        title="Tidak Ada Task Ditemukan"
                                        description="Tidak ada task yang sesuai dengan filter pencarian."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($paginatedTasks)
            <div>
                {{ $paginatedTasks->links() }}
            </div>
        @endif
    @endif
</x-app-layout>
