<x-app-layout title="Milestone Proyek">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Milestone' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Milestone & Target Tahapan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Daftar tahapan kunci dan target pencapaian strategis lintas proyek.</p>
        </div>

        <!-- Filter by Project -->
        <form action="{{ route('milestones.index') }}" method="GET" class="flex items-center gap-2">
            <select name="project_id" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-700 shadow-2xs">
                <option value="">Semua Proyek</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>
                        {{ $p->code }} - {{ mb_strimwidth($p->name, 0, 30, '...') }}
                    </option>
                @endforeach
            </select>
            @if($projectId)
                <a href="{{ route('milestones.index') }}" class="text-xs text-slate-500 hover:text-slate-800 p-2 hover:bg-slate-100 rounded-lg">Reset</a>
            @endif
        </form>
    </div>

    <!-- Milestones List -->
    <div class="bg-white rounded-xl border border-slate-200/80 p-6 shadow-2xs">
        <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            @forelse($milestones as $m)
                <div class="relative group">
                    <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 rounded-full flex items-center justify-center {{ $m->is_completed ? 'bg-emerald-600 text-white shadow-xs' : ($m->status === 'in_progress' ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white border-2 border-slate-300 text-slate-400') }}">
                        @if($m->is_completed)
                            <i data-lucide="check" class="w-3.5 h-3.5 stroke-[3]"></i>
                        @elseif($m->status === 'in_progress')
                            <i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i>
                        @else
                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                        @endif
                    </div>

                    <div class="bg-slate-50/70 border rounded-xl p-4 sm:p-5 transition-all {{ $m->is_completed ? 'border-emerald-200/80 bg-emerald-50/15' : 'border-slate-200/80' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <a href="{{ route('projects.show', $m->project_id) }}" class="text-[11px] font-mono text-indigo-600 hover:underline">
                                        {{ $m->project->code }}
                                    </a>
                                    <span class="text-slate-300">&bull;</span>
                                    <h3 class="text-sm font-bold text-slate-900">
                                        {{ $m->name }}
                                    </h3>
                                    <x-badge :type="match($m->status) { 'completed'=>'success', 'in_progress'=>'info', default=>'neutral' }" size="sm">
                                        {{ $m->status_label }}
                                    </x-badge>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed max-w-2xl">
                                    {{ $m->description ?: 'Tidak ada deskripsi milestone.' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right">
                                    <span class="block text-[10px] text-slate-400 uppercase font-bold tracking-wider">Target Tanggal</span>
                                    <span class="text-xs font-semibold text-slate-700">{{ $m->target_date->format('d M Y') }}</span>
                                </div>

                                @can('update', $m)
                                    <form action="{{ route('milestones.toggle', $m) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border transition-colors flex items-center gap-1.5 {{ $m->is_completed ? 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100' : 'bg-emerald-600 text-white border-transparent hover:bg-emerald-700' }}"
                                        >
                                            <i data-lucide="{{ $m->is_completed ? 'rotate-ccw' : 'check' }}" class="w-3.5 h-3.5"></i>
                                            <span>{{ $m->is_completed ? 'Batal Selesai' : 'Selesai' }}</span>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8">
                    <x-empty-state
                        title="Tidak Ada Milestone"
                        description="Belum ada milestone yang terdaftar untuk filter ini."
                    />
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $milestones->links() }}
        </div>
    </div>
</x-app-layout>
