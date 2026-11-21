<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — Platform Visibilitas & Pelacakan Kargo Global</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between selection:bg-blue-600 selection:text-white">
    <!-- Navbar -->
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center shadow-lg shadow-blue-500/20 text-white font-black text-xl">
                    🚢
                </div>
                <div>
                    <span class="text-xl font-bold tracking-tight text-white">Cargo<span class="text-blue-500">Trace</span></span>
                    <span class="hidden md:inline-block ml-2 px-2 py-0.5 text-xs font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full">Proyek UAS Pemrograman Web 2</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="#demo-accounts" class="text-sm text-slate-400 hover:text-white transition">Akun Demo</a>
                <a href="/app" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-0.5">
                    Buka Aplikasi →
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative overflow-hidden py-24 sm:py-32">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(45rem_50rem_at_top,theme(colors.blue.900),theme(colors.slate.950))] opacity-40"></div>
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-blue-500/20 bg-blue-500/10 text-blue-400 text-xs font-semibold mb-8">
                <span class="flex h-2 w-2 rounded-full bg-blue-400 animate-ping"></span>
                Beyond CRUD: Mesin Visibilitas Logistik End-to-End
            </div>
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white max-w-4xl mx-auto leading-tight">
                Pelacakan Kargo Maritim & Darat <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-teal-300">Cerdas & Real-Time</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto font-normal">
                Kalkulasi keterlambatan deterministik otomatis, riwayat perjalanan append-only, pemetaan GIS Leaflet.js, dan alur eskalasi penanganan kendala kargo.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="/app" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-semibold shadow-xl shadow-blue-500/25 transition transform hover:-translate-y-0.5">
                    Masuk ke Workspace
                </a>
                <a href="#features" class="px-8 py-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white font-semibold transition">
                    Jelajahi Fitur Utama
                </a>
            </div>
        </div>
    </header>

    <!-- Features Grid -->
    <section id="features" class="py-20 border-t border-slate-800/80 bg-slate-900/30">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-xs uppercase tracking-widest text-blue-400 font-bold mb-3">Rekayasa Perangkat Lunak</h2>
                <p class="text-3xl font-bold text-white tracking-tight">Modul Operasional Utama</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-blue-500/50 transition duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">
                        ⏱️
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Delay Engine Deterministik</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Kalkulasi otomatis selisih waktu kedatangan aktual versus perkiraan (\(\Delta t = \text{ETA} - \text{Expected}\)) yang dikategorikan dalam tingkat keparahan RENDAH, SEDANG, dan TINGGI.
                    </p>
                </div>
                <!-- Card 2 -->
                <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/50 transition duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">
                        🗺️
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Peta GIS Interaktif Leaflet.js</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Visualisasi rute polyline koordinat nyata yang menghubungkan pelabuhan asal, titik koordinat laut tengah jalan, hingga pelabuhan tujuan dengan integrasi cuaca Open-Meteo.
                    </p>
                </div>
                <!-- Card 3 -->
                <div class="p-8 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-teal-500/50 transition duration-300 group">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">
                        🛡️
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">State Machine & Isolasi Role (RBAC)</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Validasi transisi status berurutan (CREATED &rarr; BOOKED &rarr; PICKED_UP &rarr; IN_TRANSIT &rarr; DELIVERED) dengan pembatasan hak akses ketat antara Admin, Operator, dan Pelanggan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Accounts Section -->
    <section id="demo-accounts" class="py-20 border-t border-slate-800/80">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-xs uppercase tracking-widest text-blue-400 font-bold mb-3">Pengujian & Evaluasi Dosen</h2>
                <p class="text-3xl font-bold text-white tracking-tight">Kredensial Akun Demo Siap Pakai</p>
                <p class="text-sm text-slate-400 mt-2">Seluruh password akun demo adalah: <span class="font-mono text-cyan-400 bg-slate-900 px-2 py-0.5 rounded border border-slate-800">password</span></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Admin Card -->
                <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-purple-500/10 text-purple-400 border border-purple-500/20">Administrator</span>
                        <span class="text-xs text-slate-500">Akses Penuh Sistem</span>
                    </div>
                    <p class="text-sm text-slate-300 font-medium mb-1">System Administrator</p>
                    <p class="text-xs font-mono text-cyan-400 mb-4 select-all">admin@cargotrace.com</p>
                    <a href="/app?email=admin@cargotrace.com" class="block w-full py-2 text-center text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition">
                        Masuk sebagai Admin
                    </a>
                </div>
                <!-- Operator Card -->
                <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20">Operator Logistik</span>
                        <span class="text-xs text-slate-500">Operasional Armada</span>
                    </div>
                    <p class="text-sm text-slate-300 font-medium mb-1">Operations Officer</p>
                    <p class="text-xs font-mono text-cyan-400 mb-4 select-all">operator@cargotrace.com</p>
                    <a href="/app?email=operator@cargotrace.com" class="block w-full py-2 text-center text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition">
                        Masuk sebagai Operator
                    </a>
                </div>
                <!-- Customer Card -->
                <div class="p-6 rounded-2xl bg-slate-900/90 border border-slate-800">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Pelanggan / Customer</span>
                        <span class="text-xs text-slate-500">Tenant Terisolasi</span>
                    </div>
                    <p class="text-sm text-slate-300 font-medium mb-1">PT Global Logistik Nusantara</p>
                    <p class="text-xs font-mono text-cyan-400 mb-4 select-all">customer@cargotrace.com</p>
                    <a href="/app?email=customer@cargotrace.com" class="block w-full py-2 text-center text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition">
                        Masuk sebagai Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 text-center text-xs text-slate-500">
        <p>CargoTrace &copy; 2026. Proyek Akhir UAS Pemrograman Web 2. Dibangun dengan Arsitektur Service Layer Bersih.</p>
    </footer>
</body>
</html>
