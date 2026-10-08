<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Gangguan Server</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 text-slate-800">
    <div class="max-w-md w-full text-center">
        <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="server-crash" class="w-8 h-8"></i>
        </div>
        <span class="text-4xl font-extrabold text-rose-600">500</span>
        <h1 class="text-xl font-bold text-slate-900 mt-2">Terjadi Gangguan pada Server</h1>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Sistem mengalami kendala saat memproses permintaan Anda. Silakan muat ulang halaman atau hubungi administrator sistem.
        </p>
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700 transition-colors shadow-xs">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
