<x-app-layout title="Pusat Notifikasi">
    <x-slot:breadcrumb>
        <x-breadcrumb :items="['Notifikasi' => null]" />
    </x-slot:breadcrumb>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Pusat Notifikasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pemberitahuan tenggat waktu, penugasan kegiatan, dan status progres proyek Anda.</p>
        </div>

        @if(auth()->user()->unreadNotifications->isNotEmpty())
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold rounded-lg border border-slate-200 shadow-2xs transition-colors flex items-center gap-1.5"
                >
                    <i data-lucide="check-check" class="w-4 h-4 text-indigo-600"></i>
                    <span>Tandai Semua Telah Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Notification Cards -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs p-6">
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notif)
                @php
                    $data = $notif->data;
                    $type = $data['type'] ?? 'info';
                    $color = match ($type) {
                        'danger' => 'text-rose-600 bg-rose-50 border-rose-100',
                        'warning' => 'text-amber-600 bg-amber-50 border-amber-100',
                        'success' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                        default => 'text-blue-600 bg-blue-50 border-blue-100',
                    };
                @endphp
                <div class="py-4 first:pt-0 last:pb-0 flex items-start gap-4 transition-colors {{ ! $notif->read_at ? 'bg-indigo-50/20 -mx-6 px-6' : '' }}">
                    <div class="p-2.5 rounded-lg border {{ $color }} shrink-0 mt-0.5">
                        <i data-lucide="{{ $data['icon'] ?? 'bell' }}" class="w-4 h-4"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-xs sm:text-sm font-bold text-slate-900">
                                {{ $data['title'] ?? 'Pemberitahuan' }}
                            </h3>
                            <span class="text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $data['message'] ?? '' }}
                        </p>

                        <div class="mt-2.5 flex items-center gap-3">
                            @if(! empty($data['url']))
                                <a href="{{ $data['url'] }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                    Buka Halaman Terkait <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif

                            @if(! $notif->read_at)
                                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] text-slate-400">Sudah dibaca</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8">
                    <x-empty-state
                        icon="bell-off"
                        title="Tidak Ada Notifikasi"
                        description="Semua pemberitahuan telah dibaca atau belum ada notifikasi baru."
                    />
                </div>
            @endforelse
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
