@props([
    'value' => 0,
    'size' => 'md', // sm, md, lg
    'showLabel' => false,
    'color' => 'auto', // auto, indigo, emerald, rose, amber
])

@php
    $val = max(0, min(100, (int) $value));

    $colorClasses = match ($color) {
        'emerald' => 'bg-emerald-600',
        'rose' => 'bg-rose-600',
        'amber' => 'bg-amber-500',
        'indigo' => 'bg-indigo-600',
        default => $val === 100 ? 'bg-emerald-600' : ($val < 25 ? 'bg-amber-500' : 'bg-indigo-600'),
    };

    $heightClasses = match ($size) {
        'sm' => 'h-1.5',
        'lg' => 'h-3',
        default => 'h-2',
    };
@endphp

<div class="w-full">
    @if($showLabel)
        <div class="flex items-center justify-between text-xs mb-1">
            <span class="text-slate-500 font-medium">Progress</span>
            <span class="font-semibold text-slate-800">{{ $val }}%</span>
        </div>
    @endif
    <div class="w-full bg-slate-100 rounded-full overflow-hidden {{ $heightClasses }}">
        <div
            class="{{ $heightClasses }} rounded-full transition-all duration-500 ease-out {{ $colorClasses }}"
            style="width: {{ $val }}%"
            role="progressbar"
            aria-valuenow="{{ $val }}"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
</div>
