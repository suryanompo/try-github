@props([
    'name' => 'User',
    'size' => 'md', // sm, md, lg, xl
])

@php
    $words = explode(' ', trim($name));
    $initials = count($words) >= 2
        ? mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1))
        : mb_strtoupper(mb_substr($name, 0, 2));

    $sizeClasses = match ($size) {
        'sm' => 'w-7 h-7 text-xs',
        'lg' => 'w-11 h-11 text-base',
        'xl' => 'w-14 h-14 text-lg font-semibold',
        default => 'w-9 h-9 text-sm font-medium',
    };

    // Deterministic pleasant color background based on initials
    $colors = [
        'bg-indigo-100 text-indigo-700',
        'bg-blue-100 text-blue-700',
        'bg-cyan-100 text-cyan-700',
        'bg-emerald-100 text-emerald-700',
        'bg-violet-100 text-violet-700',
        'bg-slate-100 text-slate-700',
    ];
    $colorIndex = abs(crc32($name)) % count($colors);
    $colorClass = $colors[$colorIndex];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full shrink-0 font-medium select-none {$sizeClasses} {$colorClass}"]) }} title="{{ $name }}">
    {{ $initials }}
</div>
