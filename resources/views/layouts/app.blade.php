<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard Monitoring Proyek' }} - ProTrack</title>

    <!-- Google Fonts Inter / Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-slate-800 antialiased bg-slate-50 flex" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden"
        x-cloak
    ></div>

    <!-- Sidebar -->
    <aside
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full lg:translate-x-0': !sidebarOpen,
            'lg:w-64': !sidebarCollapsed,
            'lg:w-20': sidebarCollapsed
        }"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col transition-all duration-200 ease-in-out shrink-0"
    >
        <!-- Brand Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-xs shrink-0">
                    <i data-lucide="folder-kanban" class="w-5 h-5"></i>
                </div>
                <div x-show="!sidebarCollapsed" class="transition-opacity duration-200">
                    <span class="font-bold text-slate-900 text-base tracking-tight">ProTrack</span>
                    <span class="block text-[10px] uppercase font-semibold tracking-wider text-indigo-600 -mt-1">Monitoring</span>
                </div>
            </a>
            <!-- Collapse Button (Desktop) -->
            <button
                @click="sidebarCollapsed = !sidebarCollapsed"
                type="button"
                class="hidden lg:flex p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
            >
                <i :data-lucide="sidebarCollapsed ? 'chevron-right' : 'chevron-left'" class="w-4 h-4"></i>
            </button>
            <!-- Close Button (Mobile) -->
            <button @click="sidebarOpen = false" type="button" class="lg:hidden p-1.5 rounded-md text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto overflow-x-hidden">
            <!-- Dashboard -->
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Dashboard' : ''"
            >
                <i data-lucide="layout-dashboard" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
            </a>

            <!-- Proyek with Accordion -->
            <div x-data="{ open: {{ request()->routeIs('projects.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    type="button"
                    class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('projects.*') ? 'bg-indigo-50/60 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    :title="sidebarCollapsed ? 'Proyek' : ''"
                >
                    <div class="flex items-center gap-3">
                        <i data-lucide="folder-kanban" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('projects.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Proyek</span>
                    </div>
                    <i x-show="!sidebarCollapsed" data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open && !sidebarCollapsed" class="pl-9 pr-2 space-y-1 text-xs">
                    <a href="{{ route('projects.index') }}" class="block py-1.5 px-2 rounded-md transition-colors {{ request()->routeIs('projects.index') && !request('preset') ? 'text-indigo-600 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
                        Semua Proyek
                    </a>
                    <a href="{{ route('projects.index', ['preset' => 'active']) }}" class="block py-1.5 px-2 rounded-md transition-colors {{ request('preset') === 'active' ? 'text-indigo-600 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
                        Proyek Aktif
                    </a>
                    <a href="{{ route('projects.index', ['preset' => 'completed']) }}" class="block py-1.5 px-2 rounded-md transition-colors {{ request('preset') === 'completed' ? 'text-indigo-600 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
                        Proyek Selesai
                    </a>
                    <a href="{{ route('projects.index', ['preset' => 'overdue']) }}" class="block py-1.5 px-2 rounded-md transition-colors {{ request('preset') === 'overdue' ? 'text-indigo-600 font-semibold' : 'text-slate-500 hover:text-slate-900' }}">
                        Proyek Terlambat
                    </a>
                </div>
            </div>

            <!-- Task -->
            <a
                href="{{ route('tasks.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('tasks.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Task' : ''"
            >
                <i data-lucide="check-square" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('tasks.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Task</span>
            </a>

            <!-- Milestone -->
            <a
                href="{{ route('milestones.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('milestones.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Milestone' : ''"
            >
                <i data-lucide="flag" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('milestones.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Milestone</span>
            </a>

            <!-- Laporan -->
            @can('view-reports')
            <a
                href="{{ route('reports.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Laporan' : ''"
            >
                <i data-lucide="bar-chart-3" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('reports.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Laporan</span>
            </a>
            @endcan

            <!-- Aktivitas -->
            <a
                href="{{ route('activities.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('activities.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Aktivitas' : ''"
            >
                <i data-lucide="activity" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('activities.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Aktivitas</span>
            </a>

            @can('admin')
            <!-- Users Management -->
            <a
                href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Pengguna' : ''"
            >
                <i data-lucide="users" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('users.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Pengguna & Tim</span>
            </a>
            @endcan

            <!-- Pengaturan -->
            <a
                href="{{ route('settings.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                :title="sidebarCollapsed ? 'Pengaturan' : ''"
            >
                <i data-lucide="settings" class="w-4.5 h-4.5 shrink-0 {{ request()->routeIs('settings.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                <span x-show="!sidebarCollapsed" class="truncate">Pengaturan</span>
            </a>
        </nav>

        <!-- User Profile Footer -->
        <div class="p-3 border-t border-slate-100 shrink-0">
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors">
                <x-avatar :name="auth()->user()->name" size="sm" />
                <div x-show="!sidebarCollapsed" class="flex-1 min-w-0 overflow-hidden">
                    <p class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->role_label }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" x-show="!sidebarCollapsed">
                    @csrf
                    <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors" title="Keluar">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div
        :class="{
            'lg:pl-64': !sidebarCollapsed,
            'lg:pl-20': sidebarCollapsed
        }"
        class="flex-1 flex flex-col min-w-0 transition-all duration-200 ease-in-out"
    >
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between gap-4">
            <!-- Left: Toggle Mobile Sidebar & Breadcrumbs -->
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = true" type="button" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    {{ $breadcrumb ?? '' }}
                </div>
            </div>

            <!-- Right: Search, Notifications, User Dropdown -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Search Box -->
                <form action="{{ route('projects.index') }}" method="GET" class="hidden md:block relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    <input
                        type="text"
                        name="search"
                        placeholder="Cari proyek, kode, atau PIC..."
                        class="w-64 pl-9 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white transition-all"
                    >
                </form>

                <!-- Notifications Dropdown -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    @php
                        $unreadCount = auth()->user()->unreadNotifications->count();
                        $recentNotifications = auth()->user()->notifications()->take(5)->get();
                    @endphp
                    <button
                        @click="open = !open"
                        type="button"
                        class="relative p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
                        title="Notifikasi"
                    >
                        <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Panel -->
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-50 overflow-hidden"
                        x-cloak
                    >
                        <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-semibold text-slate-800">Notifikasi</h4>
                                @if($unreadCount > 0)
                                    <span class="text-[10px] bg-rose-50 text-rose-600 font-semibold px-1.5 py-0.5 rounded-full">
                                        {{ $unreadCount }} Baru
                                    </span>
                                @endif
                            </div>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.readAll') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-indigo-600 hover:text-indigo-800 font-medium">
                                        Tandai dibaca
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            @forelse($recentNotifications as $notif)
                                @php
                                    $data = $notif->data;
                                    $type = $data['type'] ?? 'info';
                                    $color = match ($type) {
                                        'danger' => 'text-rose-500 bg-rose-50',
                                        'warning' => 'text-amber-500 bg-amber-50',
                                        'success' => 'text-emerald-500 bg-emerald-50',
                                        default => 'text-blue-500 bg-blue-50',
                                    };
                                @endphp
                                <div class="p-3.5 hover:bg-slate-50 transition-colors {{ $notif->read_at ? 'opacity-70' : 'bg-indigo-50/20' }}">
                                    <a href="{{ $data['url'] ?? '#' }}" class="flex items-start gap-3">
                                        <div class="p-1.5 rounded-md {{ $color }} shrink-0 mt-0.5">
                                            <i data-lucide="{{ $data['icon'] ?? 'bell' }}" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $data['title'] ?? 'Pemberitahuan' }}</p>
                                            <p class="text-xs text-slate-600 mt-0.5 leading-snug line-clamp-2">{{ $data['message'] ?? '' }}</p>
                                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                    </a>
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-slate-400">
                                    <i data-lucide="check-check" class="w-6 h-6 mx-auto mb-1 text-slate-300"></i>
                                    Tidak ada notifikasi baru
                                </div>
                            @endforelse
                        </div>

                        <div class="px-4 py-2 border-t border-slate-100 text-center bg-slate-50">
                            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                Lihat Semua Notifikasi &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                    <button
                        @click="open = !open"
                        type="button"
                        class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 transition-colors"
                    >
                        <x-avatar :name="auth()->user()->name" size="sm" />
                        <span class="hidden sm:block text-xs font-semibold text-slate-700 max-w-[120px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </button>

                    <!-- Dropdown -->
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-50"
                        x-cloak
                    >
                        <div class="px-4 py-2 border-b border-slate-100">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-1 text-[10px] font-semibold text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded">
                                {{ auth()->user()->role_label }}
                            </span>
                        </div>

                        <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                            Pengaturan Profil
                        </a>
                        <a href="{{ route('activities.index', ['user_id' => auth()->id()]) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50">
                            <i data-lucide="history" class="w-3.5 h-3.5 text-slate-400"></i>
                            Aktivitas Saya
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 text-left">
                                <i data-lucide="log-out" class="w-3.5 h-3.5 text-rose-500"></i>
                                Keluar Sistem
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif

            @if(session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif

            @if(session('info'))
                <x-alert type="info">{{ session('info') }}</x-alert>
            @endif

            @if($errors->any())
                <x-alert type="error">
                    <div class="font-semibold mb-1">Terdapat beberapa kesalahan input:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>
