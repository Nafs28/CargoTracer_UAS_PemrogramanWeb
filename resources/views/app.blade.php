<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — Platform Visibilitas & Pelacakan Kargo Global</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Leaflet.js (Peta GIS) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #0f172a; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #475569; }
        .leaflet-container { z-index: 10 !important; border-radius: 0.75rem; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-200 antialiased selection:bg-blue-600 selection:text-white flex flex-col">

    <!-- Kontainer Notifikasi Toast -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <!-- ========================================== -->
    <!-- 1. BAGIAN AUTENTIKASI (MASUK / LOGIN) -->
    <!-- ========================================== -->
    <div id="auth-section" class="min-h-full flex items-center justify-center p-6 relative">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(40rem_40rem_at_top,theme(colors.blue.900),theme(colors.slate.950))] opacity-30"></div>

        <div class="w-full max-w-md bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            <!-- Brand & Identitas -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white text-3xl shadow-lg shadow-blue-500/25 mb-4">
                    🚢
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Cargo<span class="text-blue-500">Trace</span></h1>
                <p class="text-xs text-slate-400 mt-1">Platform Visibilitas & Pelacakan Kargo Global</p>
            </div>

            <!-- FORM MASUK -->
            <form id="login-form" onsubmit="window.handleLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" id="login-email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="nama@perusahaan.com" value="admin@cargotrace.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Kata Sandi (Password)</label>
                    <input type="password" id="login-password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="••••••••" value="password">
                </div>
                <button type="submit" id="login-btn" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white text-sm font-semibold shadow-lg shadow-blue-600/30 transition transform active:scale-[0.98]">
                    Masuk ke Workspace
                </button>
            </form>

            <!-- Tombol Cepat Akun Demo -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
                <span class="text-xs text-slate-500 font-medium block mb-3">Pintasan Akun Demo (Otomatis Isi Data):</span>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="window.fillDemo('admin@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/20 hover:bg-purple-500/20 transition">Admin</button>
                    <button type="button" onclick="window.fillDemo('operator@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition">Operator</button>
                    <button type="button" onclick="window.fillDemo('customer@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition">Pelanggan</button>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="/" class="text-xs text-slate-400 hover:text-white transition">← Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. WORKSPACE UTAMA (SETELAH MASUK) -->
    <!-- ========================================== -->
    <div id="main-section" class="min-h-full flex flex-col md:flex-row flex-1 hidden">

        <!-- SIDEBAR NAVIGASI -->
        <aside class="w-full md:w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
            <div>
                <!-- Header Brand -->
                <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white text-lg shadow-md">
                            🚢
                        </div>
                        <div>
                            <span class="font-bold text-white tracking-tight">Cargo<span class="text-blue-500">Trace</span></span>
                            <span class="block text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Hub Logistik</span>
                        </div>
                    </div>
                </div>

                <!-- Profil Role Pengguna -->
                <div class="p-4 mx-3 my-4 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center gap-3">
                    <div id="user-avatar" class="w-10 h-10 rounded-lg bg-blue-600/20 border border-blue-500/30 flex items-center justify-center font-bold text-blue-400 text-sm">
                        AD
                    </div>
                    <div class="overflow-hidden">
                        <p id="user-name" class="text-xs font-bold text-white truncate">Administrator</p>
                        <span id="user-role-badge" class="inline-block px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded-md bg-purple-500/10 text-purple-400 border border-purple-500/20 mt-0.5">
                            admin
                        </span>
                    </div>
                </div>

                <!-- Menu Navigasi -->
                <nav class="px-3 space-y-1">
                    <button onclick="window.navigateTo('dashboard-view')" id="nav-dashboard" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">📊</span> Dasbor Analitik
                    </button>
                    <button onclick="window.navigateTo('shipments-view')" id="nav-shipments" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">📦</span> Direktori Kargo
                    </button>
                    <button onclick="window.navigateTo('map-view')" id="nav-map" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">🗺️</span> Peta GIS Maritim
                    </button>
                    <button onclick="window.navigateTo('exceptions-view')" id="nav-exceptions" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">⚠️</span> Eskalasi Kendala
                    </button>
                    <!-- Navigasi Khusus Administrator -->
                    <div id="admin-nav-group" class="pt-4 mt-4 border-t border-slate-800/80 space-y-1">
                        <span class="px-3 text-[10px] uppercase tracking-wider text-slate-500 font-bold block mb-1">Master Data</span>
                        <button onclick="window.navigateTo('carriers-view')" id="nav-carriers" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                            <span class="text-base">🏢</span> Armada Carrier
                        </button>
                        <button onclick="window.navigateTo('locations-view')" id="nav-locations" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                            <span class="text-base">⚓</span> Master Pelabuhan & Hub
                        </button>
                        <button onclick="window.navigateTo('audit-view')" id="nav-audit" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                            <span class="text-base">📜</span> Jejak Audit Sistem
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Tombol Keluar (Logout) -->
            <div class="p-4 border-t border-slate-800 flex items-center justify-between">
                <button onclick="window.handleLogout()" class="w-full flex items-center justify-center gap-2 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/10 rounded-lg transition border border-rose-500/20">
                    Keluar Sesi (Logout)
                </button>
            </div>
        </aside>

        <!-- VIEWPORT KONTEN UTAMA -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-8 bg-slate-950">

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 1: DASBOR ANALITIK OPERASIONAL -->
            <!-- ------------------------------------- -->
            <section id="dashboard-view" class="space-y-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Dasbor Eksekutif</h2>
                        <p class="text-xs text-slate-400 mt-1">Telemetri operasional real-time & kepatuhan SLA pengiriman kargo</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="window.loadDashboardData()" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition flex items-center gap-1.5">
                            <span>🔄</span> Segarkan Data
                        </button>
                        <button onclick="window.openNewShipmentModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-md shadow-blue-600/20">
                            <span>+</span> Buat Order Kargo
                        </button>
                    </div>
                </div>

                <!-- 4 Kartu Metrik KPI -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Volume Kargo -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>Total Volume Kargo</span>
                            <span class="p-2 rounded-lg bg-blue-500/10 text-blue-400 text-sm">📦</span>
                        </div>
                        <p id="kpi-total" class="text-3xl font-extrabold text-white">0</p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Seluruh order tercatat</span>
                    </div>
                    <!-- Dalam Perjalanan Aktif -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>Dalam Perjalanan</span>
                            <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400 text-sm">🚢</span>
                        </div>
                        <p id="kpi-intransit" class="text-3xl font-extrabold text-amber-400">0</p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Armada sedang berlayar</span>
                    </div>
                    <!-- Rasio Tepat Waktu -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>Rasio Tepat Waktu (SLA)</span>
                            <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 text-sm">⏱️</span>
                        </div>
                        <p id="kpi-ontime" class="text-3xl font-extrabold text-emerald-400">100%</p>
                        <span id="kpi-delayed-sub" class="text-[11px] text-slate-500 mt-1 block">0 kargo terlambat</span>
                    </div>
                    <!-- Kendala Terbuka -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>Kendala Terbuka</span>
                            <span class="p-2 rounded-lg bg-rose-500/10 text-rose-400 text-sm">⚠️</span>
                        </div>
                        <p id="kpi-exceptions" class="text-3xl font-extrabold text-rose-400">0</p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Butuh eskalasi operasional</span>
                    </div>
                </div>

                <!-- Baris Grafik Interaktif -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Distribusi Status -->
                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                        <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                            <span>Distribusi Status Kargo</span>
                            <span class="text-[10px] text-slate-400 font-normal">Siklus hidup pengiriman</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="chart-status"></canvas>
                        </div>
                    </div>

                    <!-- Distribusi Volume Armada -->
                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                        <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                            <span>Volume Per Armada (Carrier)</span>
                            <span class="text-[10px] text-slate-400 font-normal">Pangsa muatan armada</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="chart-carrier"></canvas>
                        </div>
                    </div>

                    <!-- Tren Keterlambatan 14 Hari -->
                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                        <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                            <span>Tren Pengiriman 14 Hari</span>
                            <span class="text-[10px] text-slate-400 font-normal">Tepat waktu vs Terlambat</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="chart-trend"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Alur Aktivitas Pelacakan Terkini -->
                <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                    <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                        <span>Milestone Pelacakan Terkini</span>
                        <span class="text-xs text-blue-400">Pencatatan Riwayat Append-Only</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">Waktu Kejadian</th>
                                    <th class="py-3 px-4">Nomor Kargo</th>
                                    <th class="py-3 px-4">Status / Milestone</th>
                                    <th class="py-3 px-4">Lokasi / Koordinat</th>
                                    <th class="py-3 px-4">Deskripsi Perjalanan</th>
                                    <th class="py-3 px-4">Dicatat Oleh</th>
                                </tr>
                            </thead>
                            <tbody id="recent-activity-tbody" class="divide-y divide-slate-800/60">
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-500">Memuat data operasional langsung...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 2: DIREKTORI KARGO -->
            <!-- ------------------------------------- -->
            <section id="shipments-view" class="space-y-6 hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Direktori Kargo</h2>
                        <p class="text-xs text-slate-400 mt-1">Pencarian multi-parameter, transisi status siklus hidup & metrik keterlambatan</p>
                    </div>
                    <button onclick="window.openNewShipmentModal()" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white shadow-md shadow-blue-600/20 transition flex items-center gap-2 self-start">
                        <span>+</span> Tambah Kargo Baru
                    </button>
                </div>

                <!-- Kontrol Filter & Pencarian -->
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Cari Kargo / Komoditas</label>
                        <input type="text" id="filter-search" oninput="window.debounceSearch()" placeholder="Nomor kargo, barang..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Status Kargo</label>
                        <select id="filter-status" onchange="window.loadShipments(1)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="CREATED">DIBUAT (CREATED)</option>
                            <option value="BOOKED">DIKONFIRMASI (BOOKED)</option>
                            <option value="PICKED_UP">DITERIMA OPERATOR (PICKED_UP)</option>
                            <option value="IN_TRANSIT">DALAM PERJALANAN (IN_TRANSIT)</option>
                            <option value="DELIVERED">SAMPAI TUJUAN (DELIVERED)</option>
                            <option value="CANCELLED">DIBATALKAN (CANCELLED)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Kondisi Jadwal</label>
                        <select id="filter-delayed" onchange="window.loadShipments(1)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">Semua Pengiriman</option>
                            <option value="true">Hanya yang Terlambat</option>
                            <option value="false">Hanya yang Tepat Waktu</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="window.resetShipmentFilters()" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 rounded-lg transition">
                            Reset Filter
                        </button>
                    </div>
                </div>

                <!-- Tabel Data Kargo -->
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">Nomor Kargo</th>
                                    <th class="py-3 px-4">Armada Carrier</th>
                                    <th class="py-3 px-4">Rute Pelayaran</th>
                                    <th class="py-3 px-4">Status Terkini</th>
                                    <th class="py-3 px-4">Kondisi & Keterlambatan</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="shipments-tbody" class="divide-y divide-slate-800/60">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Memuat data kargo...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Bilah Paginasi -->
                    <div id="shipments-pagination" class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    </div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 3: PETA GIS MARITIM GLOBAL -->
            <!-- ------------------------------------- -->
            <section id="map-view" class="space-y-6 hidden">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Peta GIS Maritim Global</h2>
                    <p class="text-xs text-slate-400 mt-1">Pemantauan titik koordinat nyata & rute pelayaran aktif berbasis Leaflet.js & OpenStreetMap</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                    <div id="global-leaflet-map" style="height: 600px; width: 100%;" class="rounded-xl overflow-hidden border border-slate-800"></div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 4: ESKALASI KENDALA (EXCEPTIONS) -->
            <!-- ------------------------------------- -->
            <section id="exceptions-view" class="space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Manajemen Eskalasi Kendala Kargo</h2>
                        <p class="text-xs text-slate-400 mt-1">Penanganan kendala bea cukai, cuaca buruk, kerusakan, hingga resolusi tuntas</p>
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">Nomor Kargo</th>
                                    <th class="py-3 px-4">Tipe Kendala</th>
                                    <th class="py-3 px-4">Tingkat Keparahan</th>
                                    <th class="py-3 px-4">Status Penanganan</th>
                                    <th class="py-3 px-4">Catatan Investigasi</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="exceptions-tbody" class="divide-y divide-slate-800/60">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Memuat data kendala...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 5: ARMADA CARRIER (ADMIN) -->
            <!-- ------------------------------------- -->
            <section id="carriers-view" class="space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Master Armada Carrier</h2>
                        <p class="text-xs text-slate-400 mt-1">Daftar mitra perusahaan pelayaran resmi pengangkut kargo</p>
                    </div>
                    <button onclick="window.openNewCarrierModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition">
                        + Tambah Carrier
                    </button>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Nama Perusahaan</th>
                                <th class="py-3 px-4">Kode SCAC</th>
                                <th class="py-3 px-4">Email Kontak</th>
                                <th class="py-3 px-4">Kargo Aktif</th>
                                <th class="py-3 px-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody id="carriers-tbody" class="divide-y divide-slate-800/60"></tbody>
                    </table>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 6: PELABUHAN & HUB (ADMIN) -->
            <!-- ------------------------------------- -->
            <section id="locations-view" class="space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Master Pelabuhan & Hub Logistik</h2>
                        <p class="text-xs text-slate-400 mt-1">Daftar simpul geografis, koordinat pelabuhan, dan pemantau cuaca laut</p>
                    </div>
                    <button onclick="window.openNewLocationModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition">
                        + Tambah Lokasi
                    </button>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Nama Pelabuhan / Hub</th>
                                <th class="py-3 px-4">Kode LOCODE</th>
                                <th class="py-3 px-4">Jenis</th>
                                <th class="py-3 px-4">Kota & Negara</th>
                                <th class="py-3 px-4">Koordinat (Lintang, Bujur)</th>
                                <th class="py-3 px-4 text-right">Cek Cuaca</th>
                            </tr>
                        </thead>
                        <tbody id="locations-tbody" class="divide-y divide-slate-800/60"></tbody>
                    </table>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- TAMPILAN 7: JEJAK AUDIT SISTEM (ADMIN) -->
            <!-- ------------------------------------- -->
            <section id="audit-view" class="space-y-6 hidden">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Jejak Audit Sistem (Audit Trail)</h2>
                    <p class="text-xs text-slate-400 mt-1">Catatan forensik permanen atas setiap aksi dan perubahan data kritis</p>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Aksi / Aktivitas</th>
                                <th class="py-3 px-4">Pengguna (Aktor)</th>
                                <th class="py-3 px-4">Entitas Target</th>
                                <th class="py-3 px-4">Alamat IP</th>
                            </tr>
                        </thead>
                        <tbody id="audit-tbody" class="divide-y divide-slate-800/60 font-mono text-[11px]"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- ========================================== -->
    <!-- 3. MODAL & DIALOG INTERAKTIF -->
    <!-- ========================================== -->

    <!-- MODAL DETAIL & TIMELINE KARGO -->
    <div id="shipment-detail-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Header Modal -->
            <div class="p-6 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-xl">📦</div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="modal-shipment-number" class="text-lg font-bold text-white font-mono">CT-000000</h3>
                            <span id="modal-status-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">STATUS</span>
                            <span id="modal-delay-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">TEPAT WAKTU</span>
                        </div>
                        <p id="modal-shipment-route" class="text-xs text-slate-400 mt-0.5">Asal &rarr; Tujuan</p>
                    </div>
                </div>
                <button onclick="window.closeDetailModal()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm transition">✕</button>
            </div>

            <!-- Tab Navigasi Modal -->
            <div class="px-6 border-b border-slate-800 flex gap-4 text-xs font-semibold bg-slate-950/20 shrink-0">
                <button onclick="window.switchModalTab('tab-overview')" id="btn-tab-overview" class="py-3 border-b-2 border-blue-500 text-blue-400">Ringkasan</button>
                <button onclick="window.switchModalTab('tab-map')" id="btn-tab-map" class="py-3 border-b-2 border-transparent text-slate-400 hover:text-white">Peta Rute</button>
                <button onclick="window.switchModalTab('tab-timeline')" id="btn-tab-timeline" class="py-3 border-b-2 border-transparent text-slate-400 hover:text-white">Riwayat Pelacakan</button>
                <button onclick="window.switchModalTab('tab-exceptions')" id="btn-tab-exceptions" class="py-3 border-b-2 border-transparent text-slate-400 hover:text-white">Kendala Kargo</button>
            </div>

            <!-- Konten Modal (Scrollable) -->
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                <!-- TAB 1: RINGKASAN -->
                <div id="tab-overview" class="space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Armada Carrier</span>
                            <p id="det-carrier" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Jenis Kargo</span>
                            <p id="det-cargo" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Berat & Volume</span>
                            <p id="det-weight-vol" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Pelanggan</span>
                            <p id="det-customer" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                    </div>

                    <!-- Panel Transisi State Machine (Admin & Operator) -->
                    <div id="lifecycle-controller-panel" class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-white">Transisi Status Berurutan (Lifecycle Guard)</span>
                            <span class="text-[10px] text-slate-400">Aturan bisnis mencegah lompatan status ilegal</span>
                        </div>
                        <div id="lifecycle-action-buttons" class="flex flex-wrap gap-2"></div>
                    </div>

                    <!-- Panel Khusus Administrator (Hapus Kargo / Soft Delete) -->
                    <div id="admin-shipment-danger-zone" class="p-4 bg-rose-950/20 rounded-2xl border border-rose-900/40 flex items-center justify-between hidden">
                        <div>
                            <span class="text-xs font-bold text-rose-400 block">Tindakan Administrator</span>
                            <span class="text-[10px] text-slate-400">Menghapus kargo dari sistem secara aman (Soft Delete).</span>
                        </div>
                        <button onclick="window.deleteActiveShipment()" class="px-4 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 text-xs font-semibold rounded-xl transition">
                            🗑️ Hapus Kargo Ini
                        </button>
                    </div>
                </div>

                <!-- TAB 2: PETA RUTE -->
                <div id="tab-map" class="space-y-4 hidden">
                    <div id="detail-leaflet-map" style="height: 380px; width: 100%;" class="rounded-xl border border-slate-800"></div>
                </div>

                <!-- TAB 3: RIWAYAT PELACAKAN -->
                <div id="tab-timeline" class="space-y-6 hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white">Kronologi Riwayat Perjalanan (Append-Only)</span>
                        <button id="btn-add-event-toggle" onclick="window.toggleAddEventForm()" class="px-3 py-1.5 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-semibold border border-blue-500/30 transition">
                            + Catat Milestone Baru
                        </button>
                    </div>

                    <!-- Formulir Catat Milestone -->
                    <form id="form-record-event" onsubmit="window.submitTrackingEvent(event)" class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-3 hidden">
                        <h4 class="text-xs font-bold text-white">Tambah Titik Koordinat / Milestone</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Status / Jenis Kejadian</label>
                                <select id="evt-status-type" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                                    <option value="IN_TRANSIT">DALAM PERJALANAN (IN_TRANSIT)</option>
                                    <option value="PORT_ARRIVAL">TIBA DI PELABUHAN TRANSIT</option>
                                    <option value="CUSTOMS_CLEARANCE">PEMERIKSAAN BEA CUKAI</option>
                                    <option value="DELIVERED">SELESAI DITERIMA (DELIVERED)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Lintang (Latitude)</label>
                                <input type="number" step="0.0001" id="evt-lat" placeholder="-6.1018" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Bujur (Longitude)</label>
                                <input type="number" step="0.0001" id="evt-lng" placeholder="106.8833" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Deskripsi Kejadian</label>
                            <input type="text" id="evt-desc" required placeholder="Kapal lego jangkar di luar perairan Selat Singapura menunggu antrean dermaga..." class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="window.toggleAddEventForm()" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-semibold">Simpan Milestone</button>
                        </div>
                    </form>

                    <!-- Alur Garis Waktu Riwayat -->
                    <div id="timeline-list" class="space-y-4 pl-4 border-l-2 border-slate-800"></div>
                </div>

                <!-- TAB 4: KENDALA KARGO -->
                <div id="tab-exceptions" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white">Laporan Kendala Kargo</span>
                        <button onclick="window.toggleReportExceptionForm()" class="px-3 py-1.5 rounded-lg bg-rose-600/20 text-rose-400 hover:bg-rose-600/30 text-xs font-semibold border border-rose-500/30 transition">
                            + Laporkan Kendala
                        </button>
                    </div>

                    <!-- Formulir Lapor Kendala -->
                    <form id="form-report-exception" onsubmit="window.submitException(event)" class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-3 hidden">
                        <h4 class="text-xs font-bold text-rose-400">Lapor Gangguan Operasional Baru</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Jenis Kendala</label>
                                <select id="exc-type" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                                    <option value="CUSTOMS_HOLD">TAHANAN BEA CUKAI (CUSTOMS_HOLD)</option>
                                    <option value="WEATHER_DELAY">CUACA BURUK (WEATHER_DELAY)</option>
                                    <option value="PORT_CONGESTION">KONGESI PELABUHAN (PORT_CONGESTION)</option>
                                    <option value="DAMAGED">KERUSAKAN BARANG (DAMAGED)</option>
                                    <option value="MISROUTED">SALAH RUTE (MISROUTED)</option>
                                    <option value="DOCUMENTATION_ISSUE">MASALAH DOKUMEN (DOCUMENTATION_ISSUE)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Tingkat Keparahan</label>
                                <select id="exc-severity" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                                    <option value="LOW">RENDAH (0 - 6 Jam)</option>
                                    <option value="MEDIUM">SEDANG (6 - 24 Jam)</option>
                                    <option value="HIGH">TINGGI (&gt; 24 Jam)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Catatan Detail Kendala</label>
                            <textarea id="exc-notes" rows="2" required placeholder="Proses clearence terhambat akibat ketidaksesuaian kode HS pada faktur..." class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white"></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="window.toggleReportExceptionForm()" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold">Kirim Laporan</button>
                        </div>
                    </form>

                    <div id="exceptions-list" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL BUAT ORDER KARGO BARU -->
    <div id="new-shipment-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-xl p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Buat Order Pengiriman Kargo Baru</h3>
                <button onclick="window.closeNewShipmentModal()" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form onsubmit="window.handleCreateShipment(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Armada Carrier</label>
                        <select id="create-carrier" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Jenis Kargo / Komoditas</label>
                        <input type="text" id="create-cargo" required placeholder="Elektronik, Farmasi..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Pelabuhan Asal</label>
                        <select id="create-origin" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Pelabuhan Tujuan</label>
                        <select id="create-dest" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Berat Total (KG)</label>
                        <input type="number" step="0.01" id="create-weight" placeholder="12500" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Volume (CBM)</label>
                        <input type="number" step="0.01" id="create-volume" placeholder="45.5" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Jadwal Keberangkatan</label>
                        <input type="date" id="create-dep" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Jadwal Tiba (Expected)</label>
                        <input type="date" id="create-arr" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="window.closeNewShipmentModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">Simpan Order Kargo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH ARMADA CARRIER BARU -->
    <div id="new-carrier-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Daftarkan Armada Carrier Baru</h3>
                <button onclick="window.closeNewCarrierModal()" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form onsubmit="window.handleCreateCarrier(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nama Perusahaan</label>
                        <input type="text" id="carrier-name" required placeholder="Contoh: Maersk Line" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Kode SCAC (Unik)</label>
                        <input type="text" id="carrier-code" required placeholder="Contoh: MAEU" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white uppercase font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Email Kontak</label>
                        <input type="email" id="carrier-email" placeholder="ops@carrier.com" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Telepon Operasional</label>
                        <input type="text" id="carrier-phone" placeholder="+62811..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Alamat Kantor Pusat</label>
                    <textarea id="carrier-address" rows="2" placeholder="Alamat kantor perusahaan..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></textarea>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="window.closeNewCarrierModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">Simpan Carrier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH PELABUHAN / HUB LOKASI BARU -->
    <div id="new-location-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Tambah Pelabuhan / Hub Logistik</h3>
                <button onclick="window.closeNewLocationModal()" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form onsubmit="window.handleCreateLocation(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nama Pelabuhan / Hub</label>
                        <input type="text" id="loc-name" required placeholder="Contoh: Tanjung Priok" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Kode LOCODE (Unik)</label>
                        <input type="text" id="loc-code" required placeholder="Contoh: IDJKT" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white uppercase font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Jenis Lokasi</label>
                        <select id="loc-type" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            <option value="port">Pelabuhan Laut (Port)</option>
                            <option value="hub">Hub Logistik</option>
                            <option value="warehouse">Gudang (Warehouse)</option>
                            <option value="customs">Pos Bea Cukai</option>
                            <option value="airport">Bandara Kargo</option>
                            <option value="depot">Depo Kontainer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Kota</label>
                        <input type="text" id="loc-city" placeholder="Jakarta" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Negara</label>
                        <input type="text" id="loc-country" placeholder="Indonesia" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Titik Koordinat Geografis</span>
                        <button type="button" onclick="window.autoGeocodeLocation()" class="text-[10px] font-semibold text-cyan-400 hover:text-cyan-300">
                            🔍 Cari Koordinat Otomatis (Nominatim)
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Lintang (Latitude)</label>
                            <input type="number" step="0.0001" id="loc-lat" required placeholder="-6.1018" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-1">Bujur (Longitude)</label>
                            <input type="number" step="0.0001" id="loc-lng" required placeholder="106.8833" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="window.closeNewLocationModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. LOGIKA JAVASCRIPT APLIKASI UTAMA -->
    <!-- ========================================== -->
    <script>
        // State Utama Aplikasi
        window.appState = {
            token: localStorage.getItem('cargotrace_token') || null,
            user: JSON.parse(localStorage.getItem('cargotrace_user') || 'null'),
            activeShipment: null,
            carriers: [],
            locations: [],
            charts: {},
            maps: {},
        };

        // Klien API dengan Token Bearer Sanctum
        window.api = async function(url, options = {}) {
            options.headers = options.headers || {};
            options.headers['Accept'] = 'application/json';
            options.headers['Content-Type'] = 'application/json';

            if (window.appState.token) {
                options.headers['Authorization'] = `Bearer ${window.appState.token}`;
            }

            try {
                const response = await fetch(url, options);
                const data = await response.json();

                if (response.status === 401) {
                    window.handleLogout();
                    window.toast('Sesi Anda telah berakhir. Silakan masuk kembali.', 'warning');
                    throw new Error('Sesi berakhir');
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Permintaan gagal');
                }

                return data;
            } catch (err) {
                console.error("Kesalahan API:", err);
                throw err;
            }
        };

        // Notifikasi Toast
        window.toast = function(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const el = document.createElement('div');
            const bg = type === 'success' ? 'bg-emerald-600' : type === 'warning' ? 'bg-amber-600' : type === 'error' ? 'bg-rose-600' : 'bg-blue-600';

            el.className = `px-4 py-3 rounded-xl text-white text-xs font-semibold shadow-xl flex items-center gap-2 transform transition-all duration-300 pointer-events-auto ${bg}`;
            el.innerHTML = `<span>${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}</span> <span>${message}</span>`;
            container.appendChild(el);

            setTimeout(() => {
                el.classList.add('opacity-0', 'translate-x-4');
                setTimeout(() => el.remove(), 300);
            }, 3500);
        };

        // Navigasi Antar Tampilan
        window.navigateTo = function(viewId) {
            const views = ['dashboard-view', 'shipments-view', 'map-view', 'exceptions-view', 'carriers-view', 'locations-view', 'audit-view'];
            views.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');

                const navKey = id.replace('-view', '');
                const navBtn = document.getElementById(`nav-${navKey}`);
                if (navBtn) {
                    navBtn.classList.remove('bg-blue-600/20', 'text-blue-400', 'font-bold');
                    navBtn.classList.add('text-slate-400');
                }
            });

            const target = document.getElementById(viewId);
            if (target) target.classList.remove('hidden');

            const activeNavKey = viewId.replace('-view', '');
            const activeNavBtn = document.getElementById(`nav-${activeNavKey}`);
            if (activeNavBtn) {
                activeNavBtn.classList.add('bg-blue-600/20', 'text-blue-400', 'font-bold');
                activeNavBtn.classList.remove('text-slate-400');
            }

            if (viewId === 'dashboard-view') window.loadDashboardData();
            if (viewId === 'shipments-view') window.loadShipments(1);
            if (viewId === 'map-view') {
                setTimeout(() => {
                    window.initGlobalMap();
                    if (window.appState.maps.global) {
                        window.appState.maps.global.invalidateSize();
                    }
                }, 150);
            }
            if (viewId === 'exceptions-view') window.loadExceptions();
            if (viewId === 'carriers-view') window.loadCarriers();
            if (viewId === 'locations-view') window.loadLocations();
            if (viewId === 'audit-view') window.loadAuditLogs();
        };

        // Pengisian Akun Demo Cepat
        window.fillDemo = function(email) {
            document.getElementById('login-email').value = email;
            document.getElementById('login-password').value = 'password';
        };

        // Handler Masuk / Login
        window.handleLogin = async function(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const btn = document.getElementById('login-btn');

            btn.disabled = true;
            btn.innerText = 'Memverifikasi Kredensial...';

            try {
                const res = await window.api('/api/login', {
                    method: 'POST',
                    body: JSON.stringify({ email, password })
                });

                window.appState.token = res.data.token;
                window.appState.user = res.data.user;
                localStorage.setItem('cargotrace_token', res.data.token);
                localStorage.setItem('cargotrace_user', JSON.stringify(res.data.user));

                window.toast(`Selamat datang kembali, ${res.data.user.name}!`, 'success');
                window.renderAuthenticatedState();
            } catch (err) {
                window.toast(err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Masuk ke Workspace';
            }
        };

        // Handler Keluar / Logout
        window.handleLogout = async function() {
            try {
                if (window.appState.token) {
                    await window.api('/api/logout', { method: 'POST' });
                }
            } catch (err) {}

            window.appState.token = null;
            window.appState.user = null;
            localStorage.removeItem('cargotrace_token');
            localStorage.removeItem('cargotrace_user');

            document.getElementById('auth-section').classList.remove('hidden');
            document.getElementById('main-section').classList.add('hidden');
            window.toast('Berhasil keluar dari akun.', 'info');
        };

        // Tampilkan Tampilan Sesuai Peran Pengguna
        window.renderAuthenticatedState = function() {
            if (!window.appState.token || !window.appState.user) {
                document.getElementById('auth-section').classList.remove('hidden');
                document.getElementById('main-section').classList.add('hidden');
                return;
            }

            document.getElementById('auth-section').classList.add('hidden');
            document.getElementById('main-section').classList.remove('hidden');

            const u = window.appState.user;
            document.getElementById('user-name').innerText = u.name;
            document.getElementById('user-role-badge').innerText = u.role.toUpperCase();
            document.getElementById('user-avatar').innerText = u.name.substring(0, 2).toUpperCase();

            // Batasi menu navigasi admin
            const adminGroup = document.getElementById('admin-nav-group');
            if (u.role === 'admin') {
                adminGroup.classList.remove('hidden');
            } else {
                adminGroup.classList.add('hidden');
            }

            window.loadMasterReferences();
            window.navigateTo('dashboard-view');
        };

        // Muat Data Master Armada & Lokasi untuk Dropdown
        window.loadMasterReferences = async function() {
            try {
                const [cRes, lRes] = await Promise.all([
                    window.api('/api/carriers'),
                    window.api('/api/locations')
                ]);
                window.appState.carriers = cRes.data;
                window.appState.locations = lRes.data;

                const cSelect = document.getElementById('create-carrier');
                const oSelect = document.getElementById('create-origin');
                const dSelect = document.getElementById('create-dest');

                cSelect.innerHTML = window.appState.carriers.map(c => `<option value="${c.id}">${c.name} (${c.code})</option>`).join('');
                oSelect.innerHTML = window.appState.locations.map(l => `<option value="${l.id}">${l.name} (${l.city || l.country})</option>`).join('');
                dSelect.innerHTML = window.appState.locations.map(l => `<option value="${l.id}">${l.name} (${l.city || l.country})</option>`).join('');
            } catch (err) {}
        };

        // DASBOR: Muat metrik statistik & grafik
        window.loadDashboardData = async function() {
            try {
                const res = await window.api('/api/dashboard');
                const stats = res.data.stats;
                const recents = res.data.recent_activity;

                document.getElementById('kpi-total').innerText = stats.total_shipments;
                document.getElementById('kpi-intransit').innerText = stats.in_transit;
                document.getElementById('kpi-ontime').innerText = `${stats.on_time_ratio}%`;
                document.getElementById('kpi-delayed-sub').innerText = `${stats.delayed} kargo terlambat`;
                document.getElementById('kpi-exceptions').innerText = stats.open_exceptions;

                const tbody = document.getElementById('recent-activity-tbody');
                if (recents.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-500">Belum ada riwayat pelacakan tercatat.</td></tr>`;
                } else {
                    tbody.innerHTML = recents.map(r => `
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">${new Date(r.event_time).toLocaleDateString()}</td>
                            <td class="py-3 px-4 font-bold text-white font-mono">${r.shipment ? r.shipment.shipment_number : '-'}</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">${r.status_type}</span></td>
                            <td class="py-3 px-4 text-slate-300">${r.location ? r.location.name : (r.latitude ? `${r.latitude}, ${r.longitude}` : 'Perairan Laut')}</td>
                            <td class="py-3 px-4 text-slate-400">${r.description}</td>
                            <td class="py-3 px-4 text-slate-500">${r.creator ? r.creator.name : 'Sistem'}</td>
                        </tr>
                    `).join('');
                }

                window.renderDashboardCharts();
            } catch (err) {
                console.error(err);
            }
        };

        // Render Grafik Chart.js
        window.renderDashboardCharts = async function() {
            try {
                const [statusRes, carrierRes, trendRes] = await Promise.all([
                    window.api('/api/dashboard/shipments-by-status'),
                    window.api('/api/dashboard/shipments-by-carrier'),
                    window.api('/api/dashboard/delay-trend')
                ]);

                if (window.appState.charts.status) window.appState.charts.status.destroy();
                if (window.appState.charts.carrier) window.appState.charts.carrier.destroy();
                if (window.appState.charts.trend) window.appState.charts.trend.destroy();

                // 1. Grafik Status Kargo
                const ctxStatus = document.getElementById('chart-status').getContext('2d');
                window.appState.charts.status = new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: statusRes.data.labels,
                        datasets: [{
                            data: statusRes.data.data,
                            backgroundColor: ['#64748b', '#3b82f6', '#6366f1', '#eab308', '#22c55e', '#ef4444'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, color: '#94a3b8', font: { size: 10 } } } }
                    }
                });

                // 2. Grafik Volume Per Armada
                const ctxCarrier = document.getElementById('chart-carrier').getContext('2d');
                window.appState.charts.carrier = new Chart(ctxCarrier, {
                    type: 'bar',
                    data: {
                        labels: carrierRes.data.labels,
                        datasets: [{
                            label: 'Volume Kargo',
                            data: carrierRes.data.data,
                            backgroundColor: '#38bdf8',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: '#94a3b8', font: { size: 9 } }, grid: { display: false } },
                            y: { ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#1e293b' } }
                        }
                    }
                });

                // 3. Grafik Tren Keterlambatan
                const ctxTrend = document.getElementById('chart-trend').getContext('2d');
                window.appState.charts.trend = new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: trendRes.data.labels,
                        datasets: [
                            {
                                label: 'Tepat Waktu',
                                data: trendRes.data.on_time,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.3,
                            },
                            {
                                label: 'Terlambat',
                                data: trendRes.data.delayed,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                fill: true,
                                tension: 0.3,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, color: '#94a3b8', font: { size: 10 } } } },
                        scales: {
                            x: { ticks: { color: '#94a3b8', font: { size: 9 } }, grid: { display: false } },
                            y: { ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#1e293b' } }
                        }
                    }
                });

            } catch (err) {
                console.error("Kesalahan Grafik:", err);
            }
        };

        // DIREKTORI KARGO: Muat daftar dengan server-side pagination
        window.loadShipments = async function(page = 1) {
            const search = document.getElementById('filter-search').value;
            const status = document.getElementById('filter-status').value;
            const delayed = document.getElementById('filter-delayed').value;

            let url = `/api/shipments?page=${page}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (status) url += `&status=${encodeURIComponent(status)}`;
            if (delayed) url += `&is_delayed=${encodeURIComponent(delayed)}`;

            try {
                const res = await window.api(url);
                const items = res.data;
                const meta = res.meta;

                const tbody = document.getElementById('shipments-tbody');
                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-500">Tidak ada kargo yang cocok dengan filter pencarian.</td></tr>`;
                    document.getElementById('shipments-pagination').innerHTML = '';
                    return;
                }

                tbody.innerHTML = items.map(s => {
                    const statusColor = s.current_status === 'DELIVERED' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                        s.current_status === 'IN_TRANSIT' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' :
                        s.current_status === 'CANCELLED' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' :
                        'bg-blue-500/10 text-blue-400 border-blue-500/20';

                    const delayBadge = s.is_delayed ?
                        `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">TERLAMBAT (+${s.delay_hours} jam)</span>` :
                        `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">TEPAT WAKTU</span>`;

                    return `
                        <tr class="hover:bg-slate-800/40 transition cursor-pointer" onclick="window.openShipmentDetail(${s.id})">
                            <td class="py-3 px-4 font-bold text-white font-mono">${s.shipment_number}</td>
                            <td class="py-3 px-4 text-slate-300">${s.carrier ? s.carrier.name : '-'}</td>
                            <td class="py-3 px-4 text-slate-400">${s.origin ? s.origin.name : '?'} &rarr; ${s.destination ? s.destination.name : '?'}</td>
                            <td class="py-3 px-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold border ${statusColor}">${s.current_status}</span></td>
                            <td class="py-3 px-4">${delayBadge}</td>
                            <td class="py-3 px-4 text-right">
                                <button onclick="event.stopPropagation(); window.openShipmentDetail(${s.id})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-xs text-white">Periksa &rarr;</button>
                            </td>
                        </tr>
                    `;
                }).join('');

                // Tampilkan Paginasi
                document.getElementById('shipments-pagination').innerHTML = `
                    <span>Halaman ${meta.current_page} dari ${meta.last_page} (Total ${meta.total} kargo)</span>
                    <div class="flex gap-2">
                        <button ${meta.current_page <= 1 ? 'disabled class="opacity-40"' : `onclick="window.loadShipments(${meta.current_page - 1})"`} class="px-3 py-1 bg-slate-800 rounded">Sebelumnya</button>
                        <button ${meta.current_page >= meta.last_page ? 'disabled class="opacity-40"' : `onclick="window.loadShipments(${meta.current_page + 1})"`} class="px-3 py-1 bg-slate-800 rounded">Selanjutnya</button>
                    </div>
                `;
            } catch (err) {}
        };

        let searchTimer = null;
        window.debounceSearch = function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => window.loadShipments(1), 300);
        };

        window.resetShipmentFilters = function() {
            document.getElementById('filter-search').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-delayed').value = '';
            window.loadShipments(1);
        };

        // BUKA MODAL DETAIL KARGO
        window.openShipmentDetail = async function(id) {
            try {
                const res = await window.api(`/api/shipments/${id}`);
                const s = res.data.shipment;
                window.appState.activeShipment = s;

                document.getElementById('modal-shipment-number').innerText = s.shipment_number;
                document.getElementById('modal-status-badge').innerText = s.current_status;
                document.getElementById('modal-shipment-route').innerText = `${s.origin ? s.origin.name : 'Asal'} → ${s.destination ? s.destination.name : 'Tujuan'}`;

                // Metrik Keterlambatan
                const delayBadge = document.getElementById('modal-delay-badge');
                if (s.is_delayed) {
                    delayBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-400 border border-rose-500/20';
                    delayBadge.innerText = `TERLAMBAT +${s.delay_hours} Jam (${s.delay_severity || 'LOW'})`;
                } else {
                    delayBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                    delayBadge.innerText = 'TEPAT WAKTU';
                }

                // Data Ringkasan
                document.getElementById('det-carrier').innerText = s.carrier ? `${s.carrier.name} (${s.carrier.code})` : '-';
                document.getElementById('det-cargo').innerText = s.cargo_type || '-';
                document.getElementById('det-weight-vol').innerText = `${s.weight || 0} KG / ${s.volume || 0} CBM`;
                document.getElementById('det-customer').innerText = s.customer ? s.customer.name : 'Muatan Umum';

                // Hak Akses Fitur Hapus Kargo (Khusus Admin)
                const dangerZone = document.getElementById('admin-shipment-danger-zone');
                if (dangerZone) {
                    if (window.appState.user && window.appState.user.role === 'admin') {
                        dangerZone.classList.remove('hidden');
                    } else {
                        dangerZone.classList.add('hidden');
                    }
                }

                // Hak Akses Tambah Milestone & Laporkan Kendala (Operator & Admin)
                const isCustomer = window.appState.user && window.appState.user.role === 'customer';
                const addEventBtn = document.getElementById('btn-add-event-toggle');
                if (addEventBtn) {
                    if (isCustomer) addEventBtn.classList.add('hidden');
                    else addEventBtn.classList.remove('hidden');
                }
                const reportExcBtn = document.querySelector('#tab-exceptions button[onclick*="toggleReportExceptionForm"]');
                if (reportExcBtn) {
                    if (isCustomer) reportExcBtn.classList.add('hidden');
                    else reportExcBtn.classList.remove('hidden');
                }

                window.renderLifecycleButtons(s);
                window.renderTimeline(s.tracking_events || []);
                window.renderExceptions(s.exceptions || []);

                document.getElementById('shipment-detail-modal').classList.remove('hidden');
                window.switchModalTab('tab-overview');
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        window.closeDetailModal = function() {
            document.getElementById('shipment-detail-modal').classList.add('hidden');
            window.appState.activeShipment = null;
        };

        // Hapus Kargo Aktif (Khusus Administrator - Soft Delete)
        window.deleteActiveShipment = async function() {
            if (!window.appState.activeShipment) return;
            const s = window.appState.activeShipment;
            if (!confirm(`Konfirmasi: Apakah Anda yakin ingin menghapus kargo ${s.shipment_number}? Tindakan ini akan mengarsipkan kargo secara aman (soft delete).`)) return;

            try {
                await window.api(`/api/shipments/${s.id}`, { method: 'DELETE' });
                window.toast(`Kargo ${s.shipment_number} berhasil dihapus dari sistem.`, 'success');
                window.closeDetailModal();
                window.loadShipments(1);
                window.loadDashboardData();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        window.switchModalTab = function(tabId) {
            ['tab-overview', 'tab-map', 'tab-timeline', 'tab-exceptions'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
                document.getElementById(`btn-${id}`).className = 'py-3 border-b-2 border-transparent text-slate-400 hover:text-white';
            });

            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(`btn-${tabId}`).className = 'py-3 border-b-2 border-blue-500 text-blue-400 font-semibold';

            if (tabId === 'tab-map' && window.appState.activeShipment) {
                setTimeout(() => {
                    window.initDetailMap(window.appState.activeShipment.id);
                    if (window.appState.maps.detail) {
                        window.appState.maps.detail.invalidateSize();
                    }
                }, 150);
            }
        };

        // Render Tombol Transisi Status Lifecycle
        window.renderLifecycleButtons = function(s) {
            const container = document.getElementById('lifecycle-action-buttons');
            const panel = document.getElementById('lifecycle-controller-panel');

            if (window.appState.user.role === 'customer') {
                panel.classList.add('hidden');
                return;
            }
            panel.classList.remove('hidden');

            const transitions = {
                'CREATED': ['BOOKED', 'CANCELLED'],
                'BOOKED': ['PICKED_UP', 'CANCELLED'],
                'PICKED_UP': ['IN_TRANSIT', 'CANCELLED'],
                'IN_TRANSIT': ['DELIVERED', 'CANCELLED'],
                'DELIVERED': [],
                'CANCELLED': []
            };

            const labels = {
                'BOOKED': 'Konfirmasi Armada (BOOKED)',
                'PICKED_UP': 'Barang Diterima (PICKED_UP)',
                'IN_TRANSIT': 'Mulai Perjalanan (IN_TRANSIT)',
                'DELIVERED': 'Kargo Sampai (DELIVERED)',
                'CANCELLED': 'Batalkan Pengiriman (CANCELLED)'
            };

            const allowed = transitions[s.current_status] || [];

            if (allowed.length === 0) {
                container.innerHTML = `<span class="text-xs text-slate-500 italic">Kargo berada dalam status terminal akhir (${s.current_status}) dan terkunci permanen.</span>`;
                return;
            }

            container.innerHTML = allowed.map(target => {
                const isCancel = target === 'CANCELLED';
                const color = isCancel ? 'bg-rose-600/20 text-rose-400 border-rose-500/30 hover:bg-rose-600/30' : 'bg-blue-600 hover:bg-blue-500 text-white';
                return `<button onclick="window.transitionStatus('${target}')" class="px-4 py-2 rounded-xl text-xs font-semibold border ${color} transition">Transisi ke &rarr; ${labels[target] || target}</button>`;
            }).join('');
        };

        window.transitionStatus = async function(newStatus) {
            if (!window.appState.activeShipment) return;
            try {
                await window.api(`/api/shipments/${window.appState.activeShipment.id}/status`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status: newStatus })
                });

                window.toast(`Status berhasil diubah menjadi ${newStatus}`, 'success');
                window.openShipmentDetail(window.appState.activeShipment.id);
                window.loadShipments();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Render Garis Waktu Riwayat
        window.renderTimeline = function(events) {
            const container = document.getElementById('timeline-list');
            if (events.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-500 italic py-4">Belum ada titik milestone tercatat.</p>`;
                return;
            }

            container.innerHTML = events.map(e => `
                <div class="relative pl-6 pb-4">
                    <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-500 border-4 border-slate-900"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-white">${e.status_type}</span>
                        <span class="text-[10px] text-slate-500 font-mono">${new Date(e.event_time).toLocaleString()}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">${e.description}</p>
                    ${e.location ? `<span class="text-[10px] text-cyan-400 mt-1 block">⚓ Lokasi: ${e.location.name}</span>` : ''}
                </div>
            `).join('');
        };

        window.toggleAddEventForm = function() {
            document.getElementById('form-record-event').classList.toggle('hidden');
        };

        window.submitTrackingEvent = async function(e) {
            e.preventDefault();
            if (!window.appState.activeShipment) return;

            const payload = {
                status_type: document.getElementById('evt-status-type').value,
                latitude: document.getElementById('evt-lat').value || null,
                longitude: document.getElementById('evt-lng').value || null,
                description: document.getElementById('evt-desc').value,
            };

            try {
                await window.api(`/api/shipments/${window.appState.activeShipment.id}/tracking`, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                window.toast('Milestone kargo berhasil disimpan.', 'success');
                window.toggleAddEventForm();
                window.openShipmentDetail(window.appState.activeShipment.id);
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Render Daftar Kendala (Exceptions)
        window.renderExceptions = function(exceptions) {
            const container = document.getElementById('exceptions-list');
            if (exceptions.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-500 italic py-4">Tidak ada kendala operasional yang dilaporkan untuk kargo ini.</p>`;
                return;
            }

            container.innerHTML = exceptions.map(exc => `
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-rose-400">${exc.exception_type}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">${exc.severity}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold ${exc.status === 'RESOLVED' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20'}">${exc.status}</span>
                    </div>
                    <p class="text-xs text-slate-300">${exc.notes || 'Tidak ada catatan operasional.'}</p>
                    ${exc.status !== 'RESOLVED' && window.appState.user.role !== 'customer' ? `
                        <div class="flex gap-2 pt-2 border-t border-slate-800/80">
                            <button onclick="window.updateExceptionStatus(${exc.id}, 'RESOLVED')" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-semibold rounded-lg">Tandai Selesai (Resolve)</button>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        };

        window.toggleReportExceptionForm = function() {
            document.getElementById('form-report-exception').classList.toggle('hidden');
        };

        window.submitException = async function(e) {
            e.preventDefault();
            if (!window.appState.activeShipment) return;

            const payload = {
                exception_type: document.getElementById('exc-type').value,
                severity: document.getElementById('exc-severity').value,
                notes: document.getElementById('exc-notes').value,
            };

            try {
                await window.api(`/api/shipments/${window.appState.activeShipment.id}/exceptions`, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                window.toast('Laporan kendala berhasil dikirim.', 'warning');
                window.toggleReportExceptionForm();
                window.openShipmentDetail(window.appState.activeShipment.id);
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        window.updateExceptionStatus = async function(excId, status) {
            try {
                await window.api(`/api/exceptions/${excId}`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status })
                });

                window.toast(`Status kendala berhasil diperbarui menjadi ${status}.`, 'success');
                if (window.appState.activeShipment) {
                    window.openShipmentDetail(window.appState.activeShipment.id);
                }
                window.loadExceptions();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Peta Detail Rute Kargo (Leaflet.js)
        window.initDetailMap = async function(shipmentId) {
            try {
                const res = await window.api(`/api/shipments/${shipmentId}/map`);
                const mapData = res.data;

                if (window.appState.maps.detail) {
                    window.appState.maps.detail.remove();
                }

                const map = L.map('detail-leaflet-map').setView([mapData.origin.latitude, mapData.origin.longitude], 4);
                window.appState.maps.detail = map;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; Kontributor OpenStreetMap'
                }).addTo(map);

                L.marker([mapData.origin.latitude, mapData.origin.longitude])
                    .addTo(map)
                    .bindPopup(`<b>Pelabuhan Asal: ${mapData.origin.name}</b>`);

                L.marker([mapData.destination.latitude, mapData.destination.longitude])
                    .addTo(map)
                    .bindPopup(`<b>Pelabuhan Tujuan: ${mapData.destination.name}</b>`);

                const points = [
                    [mapData.origin.latitude, mapData.origin.longitude],
                    ...mapData.waypoints.map(w => [w.latitude, w.longitude]),
                    [mapData.destination.latitude, mapData.destination.longitude]
                ];

                L.polyline(points, { color: '#38bdf8', weight: 3, dashArray: '6, 6' }).addTo(map);
                map.fitBounds(points, { padding: [40, 40] });
            } catch (err) {
                console.error("Kesalahan peta:", err);
            }
        };

        // Peta Global Seluruh Kargo
        window.initGlobalMap = async function() {
            try {
                const res = await window.api('/api/shipments?per_page=100');
                const shipments = res.data;

                if (window.appState.maps.global) {
                    window.appState.maps.global.remove();
                }

                const map = L.map('global-leaflet-map').setView([10.0, 100.0], 3);
                window.appState.maps.global = map;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; Kontributor OpenStreetMap'
                }).addTo(map);

                shipments.forEach(s => {
                    if (s.origin && s.destination && s.origin.latitude) {
                        L.marker([s.origin.latitude, s.origin.longitude])
                            .addTo(map)
                            .bindPopup(`<b>${s.shipment_number}</b><br>Status: ${s.current_status}<br>${s.carrier ? s.carrier.name : ''}`);
                    }
                });
            } catch (err) {}
        };

        // Modal Buat Order Baru
        window.openNewShipmentModal = function() {
            document.getElementById('new-shipment-modal').classList.remove('hidden');
        };

        window.closeNewShipmentModal = function() {
            document.getElementById('new-shipment-modal').classList.add('hidden');
        };

        window.handleCreateShipment = async function(e) {
            e.preventDefault();
            const payload = {
                carrier_id: document.getElementById('create-carrier').value,
                cargo_type: document.getElementById('create-cargo').value,
                origin_id: document.getElementById('create-origin').value,
                destination_id: document.getElementById('create-dest').value,
                weight: document.getElementById('create-weight').value || null,
                volume: document.getElementById('create-volume').value || null,
                expected_departure: document.getElementById('create-dep').value || null,
                expected_arrival: document.getElementById('create-arr').value || null,
            };

            try {
                const res = await window.api('/api/shipments', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });

                window.toast(`Kargo ${res.data.shipment_number} berhasil dibuat!`, 'success');
                window.closeNewShipmentModal();
                window.loadShipments(1);
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Tampilan Eskalasi Kendala
        window.loadExceptions = async function() {
            try {
                const res = await window.api('/api/exceptions');
                const list = res.data;
                const tbody = document.getElementById('exceptions-tbody');

                if (list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-500">Tidak ada kendala aktif yang butuh penanganan.</td></tr>`;
                    return;
                }

                tbody.innerHTML = list.map(exc => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-mono font-bold text-white">${exc.shipment ? exc.shipment.shipment_number : '-'}</td>
                        <td class="py-3 px-4 font-semibold text-rose-400">${exc.exception_type}</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">${exc.severity}</span></td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">${exc.status}</span></td>
                        <td class="py-3 px-4 text-slate-300">${exc.notes || '-'}</td>
                        <td class="py-3 px-4 text-right">
                            <button onclick="window.updateExceptionStatus(${exc.id}, 'RESOLVED')" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[11px] font-semibold">Tandai Selesai</button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // Tampilan Armada Carrier
        window.loadCarriers = async function() {
            try {
                const res = await window.api('/api/carriers');
                const tbody = document.getElementById('carriers-tbody');
                const isAdmin = window.appState.user && window.appState.user.role === 'admin';

                tbody.innerHTML = res.data.map(c => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-white">${c.name}</td>
                        <td class="py-3 px-4 font-mono text-cyan-400">${c.code}</td>
                        <td class="py-3 px-4 text-slate-400">${c.contact_email || '-'}</td>
                        <td class="py-3 px-4 text-slate-300 font-bold">${c.shipments_count || 0}</td>
                        <td class="py-3 px-4 text-right">
                            ${isAdmin ? `
                                <button onclick="window.deleteCarrier(${c.id}, '${c.name.replace(/'/g, "\\'")}')" class="px-2.5 py-1 rounded bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/20 text-[11px] font-semibold transition">
                                    Hapus
                                </button>
                            ` : `<span class="text-xs text-emerald-400">Aktif</span>`}
                        </td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // Modal Tambah Armada Carrier
        window.openNewCarrierModal = function() {
            document.getElementById('carrier-name').value = '';
            document.getElementById('carrier-code').value = '';
            document.getElementById('carrier-email').value = '';
            document.getElementById('carrier-phone').value = '';
            document.getElementById('carrier-address').value = '';
            document.getElementById('new-carrier-modal').classList.remove('hidden');
        };

        window.closeNewCarrierModal = function() {
            document.getElementById('new-carrier-modal').classList.add('hidden');
        };

        window.handleCreateCarrier = async function(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('carrier-name').value.trim(),
                code: document.getElementById('carrier-code').value.trim().toUpperCase(),
                contact_email: document.getElementById('carrier-email').value.trim() || null,
                contact_phone: document.getElementById('carrier-phone').value.trim() || null,
                address: document.getElementById('carrier-address').value.trim() || null,
            };

            try {
                const res = await window.api('/api/carriers', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                window.toast(`Armada carrier ${res.data.name} (${res.data.code}) berhasil didaftarkan!`, 'success');
                window.closeNewCarrierModal();
                window.loadCarriers();
                window.loadMasterReferences();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        window.deleteCarrier = async function(id, name) {
            if (!confirm(`Konfirmasi: Apakah Anda yakin ingin menghapus armada carrier "${name}"?`)) return;

            try {
                await window.api(`/api/carriers/${id}`, { method: 'DELETE' });
                window.toast(`Armada carrier ${name} berhasil dihapus.`, 'success');
                window.loadCarriers();
                window.loadMasterReferences();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Tampilan Master Pelabuhan & Cek Cuaca
        window.loadLocations = async function() {
            try {
                const res = await window.api('/api/locations');
                const tbody = document.getElementById('locations-tbody');
                const isAdmin = window.appState.user && window.appState.user.role === 'admin';

                tbody.innerHTML = res.data.map(loc => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-white">${loc.name}</td>
                        <td class="py-3 px-4 font-mono text-cyan-400">${loc.code}</td>
                        <td class="py-3 px-4 uppercase text-[10px] text-slate-400">${loc.type}</td>
                        <td class="py-3 px-4 text-slate-300">${loc.city || ''}, ${loc.country || ''}</td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-400">${loc.latitude}, ${loc.longitude}</td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button onclick="window.checkLiveWeather(${loc.latitude}, ${loc.longitude}, '${loc.name.replace(/'/g, "\\'")}')" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-cyan-400 transition">
                                ⛅ Cek Cuaca
                            </button>
                            ${isAdmin ? `
                                <button onclick="window.deleteLocation(${loc.id}, '${loc.name.replace(/'/g, "\\'")}')" class="px-2 py-1 rounded bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/20 text-[11px] font-semibold transition">
                                    Hapus
                                </button>
                            ` : ''}
                        </td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // Modal Tambah Pelabuhan & Hub Lokasi
        window.openNewLocationModal = function() {
            document.getElementById('loc-name').value = '';
            document.getElementById('loc-code').value = '';
            document.getElementById('loc-type').value = 'port';
            document.getElementById('loc-city').value = '';
            document.getElementById('loc-country').value = '';
            document.getElementById('loc-lat').value = '';
            document.getElementById('loc-lng').value = '';
            document.getElementById('new-location-modal').classList.remove('hidden');
        };

        window.closeNewLocationModal = function() {
            document.getElementById('new-location-modal').classList.add('hidden');
        };

        window.autoGeocodeLocation = async function() {
            const name = document.getElementById('loc-name').value.trim();
            const city = document.getElementById('loc-city').value.trim();
            const country = document.getElementById('loc-country').value.trim();

            const query = [name, city, country].filter(Boolean).join(', ');
            if (!query) {
                window.toast('Ketikkan nama pelabuhan atau kota untuk mencari titik koordinat otomatis.', 'warning');
                return;
            }

            try {
                window.toast(`Mencari titik koordinat untuk "${query}" via Nominatim...`, 'info');
                const res = await window.api(`/api/geocode?query=${encodeURIComponent(query)}`);
                if (res.data && res.data.latitude && res.data.longitude) {
                    document.getElementById('loc-lat').value = res.data.latitude;
                    document.getElementById('loc-lng').value = res.data.longitude;
                    window.toast(`Koordinat ditemukan: ${res.data.latitude}, ${res.data.longitude}`, 'success');
                } else {
                    window.toast('Koordinat tidak ditemukan. Silakan masukkan secara manual.', 'warning');
                }
            } catch (err) {
                window.toast('Pencarian koordinat otomatis tidak berhasil. Masukkan koordinat secara manual.', 'warning');
            }
        };

        window.handleCreateLocation = async function(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('loc-name').value.trim(),
                code: document.getElementById('loc-code').value.trim().toUpperCase(),
                type: document.getElementById('loc-type').value,
                city: document.getElementById('loc-city').value.trim() || null,
                country: document.getElementById('loc-country').value.trim() || null,
                latitude: parseFloat(document.getElementById('loc-lat').value),
                longitude: parseFloat(document.getElementById('loc-lng').value),
            };

            try {
                const res = await window.api('/api/locations', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                window.toast(`Lokasi pelabuhan/hub ${res.data.name} (${res.data.code}) berhasil didaftarkan!`, 'success');
                window.closeNewLocationModal();
                window.loadLocations();
                window.loadMasterReferences();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        window.deleteLocation = async function(id, name) {
            if (!confirm(`Konfirmasi: Apakah Anda yakin ingin menghapus pelabuhan / hub "${name}"?`)) return;

            try {
                await window.api(`/api/locations/${id}`, { method: 'DELETE' });
                window.toast(`Lokasi pelabuhan ${name} berhasil dihapus.`, 'success');
                window.loadLocations();
                window.loadMasterReferences();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        window.checkLiveWeather = async function(lat, lon, name) {
            try {
                const res = await window.api(`/api/weather?lat=${lat}&lon=${lon}`);
                const w = res.data;
                window.toast(`${name}: ${w.condition}, Suhu: ${w.temperature}°C, Angin: ${w.wind_speed} km/j`, 'info');
            } catch (err) {
                window.toast('Gagal memuat data cuaca maritim.', 'error');
            }
        };

        // Tampilan Jejak Audit
        window.loadAuditLogs = async function() {
            try {
                const res = await window.api('/api/audit-logs');
                const tbody = document.getElementById('audit-tbody');
                tbody.innerHTML = res.data.map(a => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">${new Date(a.created_at).toLocaleString()}</td>
                        <td class="py-3 px-4 font-bold text-cyan-400 font-mono text-[11px]">${a.action}</td>
                        <td class="py-3 px-4 text-slate-300">${a.user ? a.user.name : 'Sistem'}</td>
                        <td class="py-3 px-4 text-slate-400">${a.auditable_type || 'Umum'}</td>
                        <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">${a.ip_address || '127.0.0.1'}</td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('email')) {
                window.fillDemo(urlParams.get('email'));
            }
            window.renderAuthenticatedState();
        });
    </script>
</body>
</html>
