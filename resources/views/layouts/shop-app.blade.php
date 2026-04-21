<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace client') — FBK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    @include('shop.partials.nav')

    @if(session('success'))
        <div class="container mx-auto px-4 pt-4">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mx-auto px-4 pt-4">
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">{{ session('error') }}</div>
        </div>
    @endif
    @if(session('info'))
        <div class="container mx-auto px-4 pt-4">
            <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 text-sm">{{ session('info') }}</div>
        </div>
    @endif

    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>
</body>
</html>
