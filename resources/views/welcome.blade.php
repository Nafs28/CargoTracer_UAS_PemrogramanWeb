<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F9F8F6] text-[#1C1917]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — Platform Visibilitas &amp; Pelacakan Kargo Global</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between bg-[#F9F8F6] text-[#1C1917] selection:bg-[#C2410C] selection:text-white">

    <!-- Navbar -->
    <nav class="border-b border-[#E2DFD7] bg-[#F9F8F6] sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="font-serif-editorial text-2xl font-semibold text-[#1C1917]">Cargo<span class="text-[#C2410C]">Trace</span></span>
                <span class="hidden sm:inline-block text-xs text-[#78716C] border-l border-[#E2DFD7] pl-3">Proyek UAS Pemrograman Web 2</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="#features" class="text-sm text-[#57534E] hover:text-[#1C1917] transition">Fitur</a>
                <a href="#demo-accounts" class="text-sm text-[#57534E] hover:text-[#1C1917] transition">Akun Demo</a>
                <a href="/app" class="px-4 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-sm font-semibold transition">
                    Buka Aplikasi →
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="py-20 sm:py-28 border-b border-[#E2DFD7]">
        <div class="max-w-6xl mx-auto px-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C] mb-4">
                Sistem Informasi Logistik &amp; Pelacakan Kargo
            </p>
            <h1 class="font-serif-editorial text-5xl sm:text-[56px] font-semibold text-[#1C1917] max-w-3xl leading-[1.1]">
                Visibilitas pengiriman kargo yang terukur, kronologis, dan transparan.
            </h1>
            <p class="mt-6 text-base sm:text-lg text-[#57534E] max-w-2xl">
                CargoTrace menyatukan pengelolaan order pengiriman, validasi status berbasis state machine, kalkulasi keterlambatan otomatis, dan pemetaan rute pelayaran dalam satu aplikasi web.
            </p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="/app" class="px-6 py-3 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-sm font-semibold transition">
                    Masuk ke Workspace →
                </a>
                <a href="#demo-accounts" class="px-6 py-3 rounded-lg bg-[#F0EEE9] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-[#1C1917] text-sm font-semibold transition">
                    Lihat Akun Demo
                </a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-20 border-b border-[#E2DFD7]">
        <div class="max-w-6xl mx-auto px-6">
            <div class="mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Arsitektur &amp; Modul</p>
                <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-2">Tiga Pilar Utama Sistem</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-7 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
                    <span class="text-xs font-mono text-[#78716C]">01 / ETA ENGINE</span>
                    <h3 class="text-lg font-semibold text-[#1C1917] mt-2 mb-3">Kalkulasi Delay Deterministik</h3>
                    <p class="text-sm text-[#57534E]">
                        Membandingkan jadwal target dengan perkiraan tiba menggunakan parameter threshold yang dapat dikonfigurasi di <code class="text-[#1C1917]">config/cargotrace.php</code>.
                    </p>
                </div>
                <div class="p-7 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
                    <span class="text-xs font-mono text-[#78716C]">02 / GIS &amp; TELEMETRI</span>
                    <h3 class="text-lg font-semibold text-[#1C1917] mt-2 mb-3">Peta Rute &amp; Cuaca Pelabuhan</h3>
                    <p class="text-sm text-[#57534E]">
                        Visualisasi koordinat pelabuhan asal, titik transit, dan tujuan pada peta Leaflet.js beserta integrasi API cuaca Open-Meteo dan geocoding Nominatim.
                    </p>
                </div>
                <div class="p-7 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
                    <span class="text-xs font-mono text-[#78716C]">03 / KEAMANAN RBAC</span>
                    <h3 class="text-lg font-semibold text-[#1C1917] mt-2 mb-3">State Machine &amp; Isolasi Peran</h3>
                    <p class="text-sm text-[#57534E]">
                        Menjaga urutan status pengiriman serta memisahkan hak akses Admin, Operator Logistik, dan Pelanggan melalui Laravel Middleware dan Policy.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Accounts Section -->
    <section id="demo-accounts" class="py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="mb-10">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Pengujian Aplikasi</p>
                <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-2">Akun Demo Siap Pakai</h2>
                <p class="text-sm text-[#57534E] mt-2">Seluruh akun menggunakan kata sandi: <code class="px-2 py-0.5 rounded bg-[#F0EEE9] border border-[#E2DFD7] text-[#1C1917]">password</code></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#C2410C]">Administrator</span>
                        <p class="font-mono text-sm font-semibold text-[#1C1917] mt-2">admin@cargotrace.com</p>
                        <p class="text-xs text-[#57534E] mt-2">Akses penuh seluruh master data, pengguna, audit log, override status, dan kargo.</p>
                    </div>
                    <a href="/app?email=admin@cargotrace.com" class="mt-5 inline-block text-xs font-semibold text-[#C2410C] hover:underline">Masuk sebagai Admin →</a>
                </div>
                <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#C2410C]">Logistics Operator</span>
                        <p class="font-mono text-sm font-semibold text-[#1C1917] mt-2">operator@cargotrace.com</p>
                        <p class="text-xs text-[#57534E] mt-2">Mengelola transisi status pengiriman, mencatat milestone tracking, dan menyelesaikan kendala.</p>
                    </div>
                    <a href="/app?email=operator@cargotrace.com" class="mt-5 inline-block text-xs font-semibold text-[#C2410C] hover:underline">Masuk sebagai Operator →</a>
                </div>
                <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#C2410C]">Customer</span>
                        <p class="font-mono text-sm font-semibold text-[#1C1917] mt-2">customer@cargotrace.com</p>
                        <p class="text-xs text-[#57534E] mt-2">Terisolasi secara ketat: hanya dapat memantau pengiriman milik akun sendiri.</p>
                    </div>
                    <a href="/app?email=customer@cargotrace.com" class="mt-5 inline-block text-xs font-semibold text-[#C2410C] hover:underline">Masuk sebagai Pelanggan →</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-[#E2DFD7] py-8 bg-[#F0EEE9]">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between text-xs text-[#57534E] gap-2">
            <span>CargoTrace — UAS Pemrograman Web 2</span>
            <span>Laravel • MySQL/SQLite • Sanctum • Leaflet.js • Chart.js</span>
        </div>
    </footer>
</body>
</html>
