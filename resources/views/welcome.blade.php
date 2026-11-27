<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F9F8F6] text-[#1C1917] scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — Platform Visibilitas &amp; Pelacakan Kargo Global</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Leaflet.js untuk Background Peta Interaktif -->
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
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Animasi masuk halus (Reveal) */
        .reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1), transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Blending Peta Latar Belakang ke Warna Warm Alabaster (#F9F8F6) */
        #hero-bg-map {
            filter: saturate(0.75) sepia(0.12) contrast(1.02);
            opacity: 1;
        }
        .map-blend-overlay {
            pointer-events: none;
            background:
                linear-gradient(to right, #F9F8F6 0%, rgba(249,248,246,0.88) 16%, rgba(249,248,246,0) 38%),
                linear-gradient(to bottom, #F9F8F6 0%, rgba(249,248,246,0) 10%, rgba(249,248,246,0) 88%, #F9F8F6 100%);
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-[#F9F8F6] text-[#1C1917] selection:bg-[#C2410C] selection:text-white">

    <!-- Navbar -->
    <nav class="border-b border-[#E2DFD7] bg-[#F9F8F6]/95 sticky top-0 z-50">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-12 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="font-serif-editorial text-2xl font-semibold text-[#1C1917]">Cargo<span class="text-[#C2410C]">Trace</span></span>
                <span class="hidden sm:inline-block text-xs text-[#78716C] border-l border-[#E2DFD7] pl-3">Proyek UAS Pemrograman Website</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="#features" class="text-sm text-[#57534E] hover:text-[#1C1917] transition">Fitur</a>
                <a href="#demo-accounts" class="text-sm text-[#57534E] hover:text-[#1C1917] transition">Akses Peran</a>
                <a href="/app" class="px-4 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-sm font-semibold transition transform hover:-translate-y-0.5">
                    Buka Aplikasi →
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section dengan Background Map di Sebelah Kanan -->
    <header class="relative overflow-hidden py-10 sm:py-16 border-b border-[#E2DFD7]">
        <!-- Background Map (Sebelah Kanan, Jelas & Interaktif) -->
        <div class="hidden md:block absolute top-0 right-0 bottom-0 w-[55%] z-0 overflow-hidden">
            <div id="hero-bg-map" class="w-full h-full"></div>
            <div class="map-blend-overlay absolute inset-0 z-10"></div>
            <!-- Info Rute Aktif di Sudut Kanan Bawah Peta -->
            <div id="hero-route-info" class="absolute bottom-4 right-5 z-20 px-3.5 py-2 rounded-lg bg-[#F9F8F6]/95 border border-[#E2DFD7] text-xs text-[#1C1917] max-w-xs">
                <span class="font-semibold text-[#C2410C] block" id="hero-route-title">Jakarta → Rotterdam</span>
                <span class="text-[#57534E]" id="hero-route-desc">Via Singapura &amp; Dubai • 4 Pelabuhan</span>
            </div>
        </div>

        <div class="relative z-20 max-w-screen-2xl mx-auto px-6 lg:px-12">
            <p class="reveal is-visible text-xs font-semibold uppercase tracking-widest text-[#C2410C] mb-4">
                Sistem Pelacakan &amp; Pemantauan Kargo
            </p>
            <h1 class="reveal is-visible font-serif-editorial text-5xl sm:text-[56px] font-semibold text-[#1C1917] max-w-2xl leading-[1.12]">
                Pantau perjalanan kargo Anda dengan jelas, urut, dan akurat.
            </h1>
            <p class="reveal is-visible mt-6 text-base sm:text-lg text-[#44403C] max-w-xl leading-relaxed">
                CargoTrace membantu tim logistik dan pelanggan melihat posisi barang terkini, riwayat pengiriman lengkap, deteksi keterlambatan otomatis, dan peta rute pelayaran dalam satu tempat.
            </p>

            <!-- Tombol CTA Utama -->
            <div class="reveal is-visible mt-9 flex flex-wrap gap-4">
                <a href="/app" class="px-6 py-3 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-sm font-semibold transition transform hover:-translate-y-0.5">
                    Masuk ke Workspace →
                </a>
                <a href="#features" class="px-6 py-3 rounded-lg bg-[#F0EEE9] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-[#1C1917] text-sm font-semibold transition">
                    Pelajari Fitur
                </a>
            </div>

            <!-- Pemilih Rute Interaktif (Menggerakkan Peta Background Secara Halus) -->
            <div class="reveal is-visible mt-9 hidden md:block text-xs max-w-xl">
                <span class="block text-[#57534E] font-medium mb-2">Sorot Rute Peta:</span>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="window.focusHeroRoute(0)" class="hero-route-btn px-3 py-1.5 rounded-md bg-[#C2410C] text-white font-medium transition">
                        Jakarta → Rotterdam
                    </button>
                    <button type="button" onclick="window.focusHeroRoute(1)" class="hero-route-btn px-3 py-1.5 rounded-md bg-[#F0EEE9] border border-[#E2DFD7] text-[#1C1917] hover:bg-[#E7E4DC] font-medium transition">
                        Belawan → Bitung
                    </button>
                    <button type="button" onclick="window.focusHeroRoute(2)" class="hero-route-btn px-3 py-1.5 rounded-md bg-[#F0EEE9] border border-[#E2DFD7] text-[#1C1917] hover:bg-[#E7E4DC] font-medium transition">
                        Surabaya → Busan
                    </button>
                    <button type="button" onclick="window.focusHeroRoute(3)" class="hero-route-btn px-3 py-1.5 rounded-md bg-[#F0EEE9] border border-[#E2DFD7] text-[#1C1917] hover:bg-[#E7E4DC] font-medium transition">
                        Singapura → Los Angeles
                    </button>
                    <button type="button" onclick="window.focusHeroRoute(4)" class="hero-route-btn px-3 py-1.5 rounded-md bg-[#F0EEE9] border border-[#E2DFD7] text-[#1C1917] hover:bg-[#E7E4DC] font-medium transition">
                        Koridor Nusantara
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-20 border-b border-[#E2DFD7]">
        <div class="max-w-6xl mx-auto px-6">
            <div class="reveal mb-12">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Arsitektur &amp; Modul</p>
                <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-2">Tiga Pilar Utama Sistem</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="reveal p-7 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] hover:border-[#C2410C]/60 hover:-translate-y-1 transition duration-300">
                    <span class="text-xs font-semibold text-[#C2410C]">01 / ESTIMASI &amp; JADWAL</span>
                    <h3 class="text-lg font-semibold text-[#1C1917] mt-2 mb-3">Deteksi Keterlambatan Otomatis</h3>
                    <p class="text-sm text-[#44403C] leading-relaxed">
                        Sistem otomatis membandingkan jadwal rencana tiba dengan perkiraan tiba terbaru, lalu memberi tanda tingkat keterlambatan (Ringan, Sedang, atau Tinggi).
                    </p>
                </div>
                <div class="reveal p-7 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] hover:border-[#C2410C]/60 hover:-translate-y-1 transition duration-300">
                    <span class="text-xs font-semibold text-[#C2410C]">02 / PETA &amp; CUACA</span>
                    <h3 class="text-lg font-semibold text-[#1C1917] mt-2 mb-3">Peta Rute &amp; Info Cuaca Pelabuhan</h3>
                    <p class="text-sm text-[#44403C] leading-relaxed">
                        Menampilkan titik pelabuhan asal, lokasi transit, hingga tujuan pada peta interaktif beserta kondisi cuaca pelabuhan dan berita pasar global.
                    </p>
                </div>
                <div class="reveal p-7 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] hover:border-[#C2410C]/60 hover:-translate-y-1 transition duration-300">
                    <span class="text-xs font-semibold text-[#C2410C]">03 / HAK AKSES</span>
                    <h3 class="text-lg font-semibold text-[#1C1917] mt-2 mb-3">Pembagian Peran yang Aman</h3>
                    <p class="text-sm text-[#44403C] leading-relaxed">
                        Setiap akun memiliki batas akses masing-masing: Admin mengelola seluruh sistem, Operator memperbarui perjalanan, dan Pelanggan hanya melihat kargo miliknya.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Role Access Cards Section -->
    <section id="demo-accounts" class="py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="reveal p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] hover:border-[#C2410C]/60 hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#C2410C]">Administrator</span>
                        <p class="font-mono text-sm font-semibold text-[#1C1917] mt-2">admin@cargotrace.com</p>
                        <p class="text-xs text-[#57534E] mt-2">Akses penuh seluruh master data, pengguna, audit log, override status, dan kargo.</p>
                    </div>
                    <a href="/app?email=admin@cargotrace.com" class="mt-5 inline-block text-xs font-semibold text-[#C2410C] hover:underline">Masuk sebagai Admin →</a>
                </div>
                <div class="reveal p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] hover:border-[#C2410C]/60 hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#C2410C]">Logistics Operator</span>
                        <p class="font-mono text-sm font-semibold text-[#1C1917] mt-2">operator@cargotrace.com</p>
                        <p class="text-xs text-[#57534E] mt-2">Mengelola transisi status pengiriman, mencatat milestone tracking, dan menyelesaikan kendala.</p>
                    </div>
                    <a href="/app?email=operator@cargotrace.com" class="mt-5 inline-block text-xs font-semibold text-[#C2410C] hover:underline">Masuk sebagai Operator →</a>
                </div>
                <div class="reveal p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] hover:border-[#C2410C]/60 hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
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
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-12 flex flex-col sm:flex-row items-center justify-between text-xs text-[#57534E] gap-2">
            <span>CargoTrace — Proyek UAS Pemrograman Website</span>
            <span>M. Daffa Annafis | 250180071</span>
            <span>Laravel • MySQL/SQLite • Sanctum • Leaflet.js • Chart.js</span>
        </div>
    </footer>

    <script>
        // 1. Scroll Reveal Animation
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.12 });

            document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

            // 2. Hero Background Interactive Map (Leaflet.js + OpenStreetMap)
            const mapEl = document.getElementById('hero-bg-map');
            if (!mapEl || typeof L === 'undefined') return;

            const heroMap = L.map('hero-bg-map', {
                zoomControl: false,
                attributionControl: false,
                scrollWheelZoom: false,
            }).setView([10, 85], 3);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(heroMap);

            const routeDefs = [
                {
                    title: 'Jakarta → Rotterdam',
                    desc: 'Via Singapura & Dubai • 4 Pelabuhan',
                    points: [
                        { name: 'Tanjung Priok (IDJKT)', latlng: [-6.1018, 106.8833] },
                        { name: 'Singapore (SGSIN)', latlng: [1.2644, 103.8220] },
                        { name: 'Jebel Ali, Dubai (AEJEA)', latlng: [25.0113, 55.0617] },
                        { name: 'Rotterdam (NLRTM)', latlng: [51.9244, 4.4777] },
                    ],
                },
                {
                    title: 'Belawan → Bitung',
                    desc: 'Via Semarang & Makassar • 4 Pelabuhan Indonesia',
                    points: [
                        { name: 'Pelabuhan Belawan (IDBLW)', latlng: [3.7892, 98.7150] },
                        { name: 'Tanjung Emas (IDSRG)', latlng: [-6.9465, 110.4283] },
                        { name: 'Pelabuhan Makassar (IDMAK)', latlng: [-5.1195, 119.4090] },
                        { name: 'Pelabuhan Bitung (IDBIT)', latlng: [1.4402, 125.1915] },
                    ],
                },
                {
                    title: 'Surabaya → Busan',
                    desc: 'Via Shanghai • Asia Timur',
                    points: [
                        { name: 'Tanjung Perak (IDSUB)', latlng: [-7.2023, 112.7303] },
                        { name: 'Shanghai (CNSHA)', latlng: [31.2304, 121.4737] },
                        { name: 'Busan (KRPUS)', latlng: [35.1028, 129.0403] },
                    ],
                },
                {
                    title: 'Singapura → Los Angeles',
                    desc: 'Via Busan • Rute Lintas Pasifik',
                    points: [
                        { name: 'Singapore (SGSIN)', latlng: [1.2644, 103.8220] },
                        { name: 'Busan (KRPUS)', latlng: [35.1028, 129.0403] },
                        { name: 'Los Angeles (USLAX)', latlng: [33.7432, -118.2673] },
                    ],
                },
                {
                    title: 'Koridor Nusantara',
                    desc: 'Jakarta → Semarang → Surabaya → Makassar',
                    points: [
                        { name: 'Tanjung Priok (IDJKT)', latlng: [-6.1018, 106.8833] },
                        { name: 'Tanjung Emas (IDSRG)', latlng: [-6.9465, 110.4283] },
                        { name: 'Tanjung Perak (IDSUB)', latlng: [-7.2023, 112.7303] },
                        { name: 'Pelabuhan Makassar (IDMAK)', latlng: [-5.1195, 119.4090] },
                    ],
                },
            ];

            const polylines = [];
            routeDefs.forEach((route, idx) => {
                const latlngs = route.points.map((p) => p.latlng);
                const line = L.polyline(latlngs, {
                    color: idx === 0 ? '#C2410C' : '#78716C',
                    weight: idx === 0 ? 3 : 1.5,
                    dashArray: idx === 0 ? null : '5, 5',
                    opacity: idx === 0 ? 0.95 : 0.55,
                }).addTo(heroMap);
                polylines.push(line);

                route.points.forEach((pt) => {
                    L.circleMarker(pt.latlng, {
                        radius: 5,
                        color: '#C2410C',
                        fillColor: '#C2410C',
                        fillOpacity: 0.9,
                    }).bindPopup(`<b>${pt.name}</b>`).addTo(heroMap);
                });
            });

            heroMap.fitBounds(routeDefs[0].points.map((p) => p.latlng), { padding: [55, 55] });

            window.focusHeroRoute = function (index) {
                document.querySelectorAll('.hero-route-btn').forEach((btn, i) => {
                    btn.className = i === index
                        ? 'hero-route-btn px-3 py-1.5 rounded-md bg-[#C2410C] text-white font-medium transition'
                        : 'hero-route-btn px-3 py-1.5 rounded-md bg-[#F0EEE9] border border-[#E2DFD7] text-[#1C1917] hover:bg-[#E7E4DC] font-medium transition';
                });
                polylines.forEach((line, i) => {
                    line.setStyle({
                        color: i === index ? '#C2410C' : '#78716C',
                        weight: i === index ? 3.5 : 1.5,
                        dashArray: i === index ? null : '5, 5',
                        opacity: i === index ? 0.95 : 0.45,
                    });
                });
                const selected = routeDefs[index];
                if (selected) {
                    document.getElementById('hero-route-title').textContent = selected.title;
                    document.getElementById('hero-route-desc').textContent = selected.desc;
                    heroMap.flyToBounds(selected.points.map((p) => p.latlng), { padding: [55, 55], duration: 1.1 });
                }
            };
        });
    </script>
</body>
</html>
