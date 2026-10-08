@props([
    'icon' => 'folder-kanban',
    'title' => 'Belum Ada Data',
    'description' => 'Mulai dengan menambahkan data pertama Anda ke sistem.',
    'actionUrl' => null,
    'actionLabel' => null,
])

<div class="text-center py-12 px-4 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3.5">
        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
    </div>
    <h3 class="text-sm font-semibold text-slate-800">{{ $title }}</h3>
    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @if($actionUrl && $actionLabel)
        <div class="mt-4">
            <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-xs">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
