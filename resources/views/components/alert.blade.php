@props([
    'type' => 'info', // success, error, warning, info
    'dismissible' => true,
])

@php
    $style = match ($type) {
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'error', 'danger' => 'bg-rose-50 text-rose-800 border-rose-200',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-blue-50 text-blue-800 border-blue-200',
    };
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="flex items-center justify-between p-4 mb-4 text-sm rounded-lg border {{ $style }}"
    role="alert"
>
    <div class="flex items-center gap-3">
        @if($type === 'success')
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        @elseif($type === 'error' || $type === 'danger')
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        @else
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @endif
        <div class="font-medium">
            {{ $slot }}
        </div>
    </div>
    @if($dismissible)
        <button @click="show = false" type="button" class="text-current opacity-60 hover:opacity-100 p-1.5 rounded-md focus:ring-2 focus:ring-slate-300">
            <span class="sr-only">Tutup</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
