@props([
    'type' => 'default', // success, danger, warning, info, neutral, purple
    'size' => 'md',      // sm, md, lg
])

@php
    $typeClasses = match ($type) {
        'success', 'completed', 'done', 'low' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
        'danger', 'overdue', 'critical' => 'bg-rose-50 text-rose-700 border-rose-200/80',
        'warning', 'high', 'review', 'on_hold' => 'bg-amber-50 text-amber-700 border-amber-200/80',
        'info', 'in_progress', 'medium' => 'bg-blue-50 text-blue-700 border-blue-200/80',
        'purple' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
        default => 'bg-slate-50 text-slate-700 border-slate-200/80',
    };

    $sizeClasses = match ($size) {
        'sm' => 'text-[11px] px-2 py-0.5',
        'lg' => 'text-sm px-3 py-1',
        default => 'text-xs px-2.5 py-0.5',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-md border {$typeClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
