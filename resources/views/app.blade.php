<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F9F8F6] text-[#1C1917]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — Platform Visibilitas &amp; Pelacakan Kargo</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Editorial Typography: Newsreader (Serif) + Plus Jakarta Sans (Sans) + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Leaflet.js (Peta GIS) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F9F8F6;
            color: #1C1917;
            font-size: 16px;
            line-height: 1.65;
        }
        .font-serif-editorial {
            font-family: 'Newsreader', Georgia, serif;
            letter-spacing: -0.02em;
        }
        code, pre, .font-mono { font-family: 'JetBrains Mono', monospace; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #F0EEE9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #D6D3CB; border-radius: 3px; }
        .leaflet-container { z-index: 10 !important; border-radius: 0.5rem; }
    </style>
</head>
<body class="h-full bg-[#F9F8F6] text-[#1C1917] antialiased flex flex-col selection:bg-[#C2410C] selection:text-white">

    <!-- Notifikasi Toast -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <!-- 1. Halaman Autentikasi (Login / Register) -->
    @include('partials.auth')

    <!-- 2. Workspace Utama -->
    <div id="main-section" class="min-h-full flex flex-col md:flex-row flex-1 hidden">
        @include('partials.sidebar')

        <main class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-10 bg-[#F9F8F6]">
            @include('partials.views.dashboard')
            @include('partials.views.shipments')
            @include('partials.views.map')
            @include('partials.views.exceptions')
            @include('partials.views.news')
            @include('partials.views.carriers')
            @include('partials.views.locations')
            @include('partials.views.users')
            @include('partials.views.audit')
        </main>
    </div>

    <!-- 3. Modals -->
    @include('partials.modals')

    <!-- 4. Modular Vanilla JS -->
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/shipments.js') }}"></script>
    <script src="{{ asset('js/map.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{ asset('js/news.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
