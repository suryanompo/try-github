<x-app-layout title="Log Aktivitas">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Aktivitas' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Log Aktivitas Sistem</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Audit trail lengkap seluruh peristiwa dan pembaruan kegiatan di platform.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-2xs mb-6">
        <form action="{{ route('activities.index') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari deskripsi aktivitas..."
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

            <!-- User Filter -->
            <select name="user_id" class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700">
                <option value="">Semua User</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg">
                Terapkan
            </button>
            @if(request()->hasAny(['search', 'project_id', 'user_id', 'action', 'object_type']))
                <a href="{{ route('activities.index') }}" class="px-2.5 py-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Activities Feed Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
        <div class="divide-y divide-slate-100">
            @forelse($activities as $act)
                <div class="py-4 first:pt-0 last:pb-0 flex items-start gap-4">
                    <x-avatar :name="$act->user?->name ?? 'System'" size="md" class="mt-0.5" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-900">{{ $act->user?->name ?? 'Sistem' }}</span>
                            <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-700 mt-1 leading-relaxed">
                            {{ $act->description }}
                        </p>
                        @if($act->project)
                            <div class="mt-2 flex items-center gap-2">
                                <a href="{{ route('projects.show', $act->project_id) }}" class="inline-flex items-center gap-1.5 text-[11px] font-mono font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md hover:bg-indigo-100 transition-colors">
                                    <i data-lucide="folder-kanban" class="w-3 h-3"></i>
                                    {{ $act->project->code }}: {{ $act->project->name }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8">
                    <x-empty-state
                        icon="activity"
                        title="Belum Ada Log Aktivitas"
                        description="Aktivitas akan tercatat secara otomatis saat Anda atau tim melakukan perubahan pada sistem."
                    />
                </div>
            @endforelse
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100">
            {{ $activities->links() }}
        </div>
    </div>
</x-app-layout>
