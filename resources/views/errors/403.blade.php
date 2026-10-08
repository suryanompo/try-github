<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 text-slate-800">
    <div class="max-w-md w-full text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="shield-alert" class="w-8 h-8"></i>
        </div>
        <span class="text-4xl font-extrabold text-amber-600">403</span>
        <h1 class="text-xl font-bold text-slate-900 mt-2">Akses Tidak Diizinkan</h1>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Anda tidak memiliki izin (authorization policy) untuk mengakses atau mengubah data ini.
        </p>
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700 transition-colors shadow-xs">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>
