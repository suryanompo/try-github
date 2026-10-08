@props([
    'items' => [], // ['Dashboard' => '/dashboard', 'Proyek' => null]
])

<nav class="flex items-center text-xs font-medium text-slate-500" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1.5 md:space-x-2">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center hover:text-slate-800 transition-colors">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 mr-1.5 text-slate-400"></i>
                Dashboard
            </a>
        </li>
        @foreach($items as $label => $url)
            <li>
                <div class="flex items-center">
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 mx-1"></i>
                    @if($url && ! $loop->last)
                        <a href="{{ $url }}" class="hover:text-slate-800 transition-colors">{{ $label }}</a>
                    @else
                        <span class="text-slate-800 font-semibold">{{ $label }}</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
