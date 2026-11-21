<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — Global Shipment Visibility & Tracking Platform</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Leaflet.js (GIS Map) -->
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

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    <!-- ========================================== -->
    <!-- 1. AUTHENTICATION SECTION (LOGIN / REGISTER) -->
    <!-- ========================================== -->
    <div id="auth-section" class="min-h-full flex items-center justify-center p-6 relative">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(40rem_40rem_at_top,theme(colors.blue.900),theme(colors.slate.950))] opacity-30"></div>

        <div class="w-full max-w-md bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            <!-- Brand -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white text-3xl shadow-lg shadow-blue-500/25 mb-4">
                    🚢
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Cargo<span class="text-blue-500">Trace</span></h1>
                <p class="text-xs text-slate-400 mt-1">Global Shipment Visibility & Tracking Platform</p>
            </div>

            <!-- LOGIN FORM -->
            <form id="login-form" onsubmit="window.handleLogin(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" id="login-email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="name@company.com" value="admin@cargotrace.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                    <input type="password" id="login-password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" placeholder="••••••••" value="password">
                </div>
                <button type="submit" id="login-btn" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white text-sm font-semibold shadow-lg shadow-blue-600/30 transition transform active:scale-[0.98]">
                    Sign In to Workspace
                </button>
            </form>

            <!-- Quick Demo Role Switcher -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 text-center">
                <span class="text-xs text-slate-500 font-medium block mb-3">Quick Demo Login (Pre-fills credentials):</span>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="window.fillDemo('admin@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/20 hover:bg-purple-500/20 transition">Admin</button>
                    <button type="button" onclick="window.fillDemo('operator@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition">Operator</button>
                    <button type="button" onclick="window.fillDemo('customer@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition">Customer</button>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="/" class="text-xs text-slate-400 hover:text-white transition">← Back to Overview</a>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. MAIN APPLICATION WORKSPACE (AUTHENTICATED) -->
    <!-- ========================================== -->
    <div id="main-section" class="min-h-full flex flex-col md:flex-row flex-1 hidden">

        <!-- SIDEBAR -->
        <aside class="w-full md:w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
            <div>
                <!-- Brand Header -->
                <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center text-white text-lg shadow-md">
                            🚢
                        </div>
                        <div>
                            <span class="font-bold text-white tracking-tight">Cargo<span class="text-blue-500">Trace</span></span>
                            <span class="block text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Visibility Hub</span>
                        </div>
                    </div>
                </div>

                <!-- User Role Profile Badge -->
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

                <!-- Navigation Links -->
                <nav class="px-3 space-y-1">
                    <button onclick="window.navigateTo('dashboard-view')" id="nav-dashboard" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">📊</span> Analytics Dashboard
                    </button>
                    <button onclick="window.navigateTo('shipments-view')" id="nav-shipments" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">📦</span> Shipments Directory
                    </button>
                    <button onclick="window.navigateTo('map-view')" id="nav-map" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">🗺️</span> Global GIS Map
                    </button>
                    <button onclick="window.navigateTo('exceptions-view')" id="nav-exceptions" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                        <span class="text-base">⚠️</span> Incident Escalations
                    </button>
                    <!-- Admin Only Navigation Links -->
                    <div id="admin-nav-group" class="pt-4 mt-4 border-t border-slate-800/80 space-y-1">
                        <span class="px-3 text-[10px] uppercase tracking-wider text-slate-500 font-bold block mb-1">Master Records</span>
                        <button onclick="window.navigateTo('carriers-view')" id="nav-carriers" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                            <span class="text-base">🏢</span> Carrier Fleet
                        </button>
                        <button onclick="window.navigateTo('locations-view')" id="nav-locations" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                            <span class="text-base">⚓</span> Ports & Hubs
                        </button>
                        <button onclick="window.navigateTo('audit-view')" id="nav-audit" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/60 transition group text-left">
                            <span class="text-base">📜</span> System Audit Trail
                        </button>
                    </div>
                </nav>
            </div>

            <!-- Bottom Actions -->
            <div class="p-4 border-t border-slate-800 flex items-center justify-between">
                <button onclick="window.handleLogout()" class="w-full flex items-center justify-center gap-2 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/10 rounded-lg transition border border-rose-500/20">
                    Sign Out
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT VIEWPORT -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-8 bg-slate-950">

            <!-- ------------------------------------- -->
            <!-- VIEW A: OPERATIONAL ANALYTICS DASHBOARD -->
            <!-- ------------------------------------- -->
            <section id="dashboard-view" class="space-y-8">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Executive Dashboard</h2>
                        <p class="text-xs text-slate-400 mt-1">Live operational telemetry & SLA compliance indices</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="window.loadDashboardData()" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-xs font-semibold text-slate-300 transition flex items-center gap-1.5">
                            <span>🔄</span> Refresh Stats
                        </button>
                        <button onclick="window.openNewShipmentModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition flex items-center gap-1.5 shadow-md shadow-blue-600/20">
                            <span>+</span> Create Shipment
                        </button>
                    </div>
                </div>

                <!-- 4 KPI Metrics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Shipments -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>Total Volume</span>
                            <span class="p-2 rounded-lg bg-blue-500/10 text-blue-400 text-sm">📦</span>
                        </div>
                        <p id="kpi-total" class="text-3xl font-extrabold text-white">0</p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Active tracked orders</span>
                    </div>
                    <!-- In Transit -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>In-Transit Active</span>
                            <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400 text-sm">🚢</span>
                        </div>
                        <p id="kpi-intransit" class="text-3xl font-extrabold text-amber-400">0</p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Vessels currently on route</span>
                    </div>
                    <!-- On-Time Ratio -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>On-Time SLA Ratio</span>
                            <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 text-sm">⏱️</span>
                        </div>
                        <p id="kpi-ontime" class="text-3xl font-extrabold text-emerald-400">100%</p>
                        <span id="kpi-delayed-sub" class="text-[11px] text-slate-500 mt-1 block">0 shipments delayed</span>
                    </div>
                    <!-- Open Exceptions -->
                    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/90 shadow-sm">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-2">
                            <span>Open Incidents</span>
                            <span class="p-2 rounded-lg bg-rose-500/10 text-rose-400 text-sm">⚠️</span>
                        </div>
                        <p id="kpi-exceptions" class="text-3xl font-extrabold text-rose-400">0</p>
                        <span class="text-[11px] text-slate-500 mt-1 block">Requires ops escalation</span>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Status Distribution Doughnut -->
                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                        <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                            <span>Status Breakdown</span>
                            <span class="text-[10px] text-slate-400 font-normal">Lifecycle distribution</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="chart-status"></canvas>
                        </div>
                    </div>

                    <!-- Carrier Breakdown Bar -->
                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                        <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                            <span>Carrier Volume</span>
                            <span class="text-[10px] text-slate-400 font-normal">Active fleet share</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="chart-carrier"></canvas>
                        </div>
                    </div>

                    <!-- Delay Trend Line -->
                    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                        <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                            <span>14-Day Delivery Cadence</span>
                            <span class="text-[10px] text-slate-400 font-normal">On-time vs Delayed</span>
                        </h3>
                        <div class="h-64 relative flex items-center justify-center">
                            <canvas id="chart-trend"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Live Tracking Activity Stream -->
                <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800/90">
                    <h3 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
                        <span>Recent Tracking Milestones</span>
                        <span class="text-xs text-blue-400">Append-Only Event Stream</span>
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">Timestamp</th>
                                    <th class="py-3 px-4">Shipment #</th>
                                    <th class="py-3 px-4">Status / Event</th>
                                    <th class="py-3 px-4">Location / Coords</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4">Logged By</th>
                                </tr>
                            </thead>
                            <tbody id="recent-activity-tbody" class="divide-y divide-slate-800/60">
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-500">Loading live operational activity...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- VIEW B: SHIPMENTS DIRECTORY -->
            <!-- ------------------------------------- -->
            <section id="shipments-view" class="space-y-6 hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Shipments Directory</h2>
                        <p class="text-xs text-slate-400 mt-1">Multi-parameter search, lifecycle transitions & ETA tracking</p>
                    </div>
                    <button onclick="window.openNewShipmentModal()" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white shadow-md shadow-blue-600/20 transition flex items-center gap-2 self-start">
                        <span>+</span> New Shipment
                    </button>
                </div>

                <!-- Filter Controls -->
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Search Identifier</label>
                        <input type="text" id="filter-search" oninput="window.debounceSearch()" placeholder="Shipment #, cargo..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Status State</label>
                        <select id="filter-status" onchange="window.loadShipments(1)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="CREATED">CREATED</option>
                            <option value="BOOKED">BOOKED</option>
                            <option value="PICKED_UP">PICKED_UP</option>
                            <option value="IN_TRANSIT">IN_TRANSIT</option>
                            <option value="DELIVERED">DELIVERED</option>
                            <option value="CANCELLED">CANCELLED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Delay State</label>
                        <select id="filter-delayed" onchange="window.loadShipments(1)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="">All Deliveries</option>
                            <option value="true">Delayed Only</option>
                            <option value="false">On-Time Only</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="window.resetShipmentFilters()" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-300 rounded-lg transition">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">Shipment Number</th>
                                    <th class="py-3 px-4">Carrier</th>
                                    <th class="py-3 px-4">Route</th>
                                    <th class="py-3 px-4">Current Status</th>
                                    <th class="py-3 px-4">ETA & Delay Metric</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="shipments-tbody" class="divide-y divide-slate-800/60">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Loading shipments...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Bar -->
                    <div id="shipments-pagination" class="p-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    </div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- VIEW C: GLOBAL GIS MAP EXPLORER -->
            <!-- ------------------------------------- -->
            <section id="map-view" class="space-y-6 hidden">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Interactive GIS Maritime Map</h2>
                    <p class="text-xs text-slate-400 mt-1">Live coordinate tracking & active shipping lanes powered by Leaflet.js</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
                    <div id="global-leaflet-map" style="height: 600px; width: 100%;" class="rounded-xl overflow-hidden border border-slate-800"></div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- VIEW D: INCIDENT ESCALATIONS (EXCEPTIONS) -->
            <!-- ------------------------------------- -->
            <section id="exceptions-view" class="space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Operational Exceptions Engine</h2>
                        <p class="text-xs text-slate-400 mt-1">Active delays, customs bottlenecks & incident resolution lifecycle</p>
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="py-3 px-4">Shipment #</th>
                                    <th class="py-3 px-4">Incident Type</th>
                                    <th class="py-3 px-4">Severity</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Investigation Notes</th>
                                    <th class="py-3 px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="exceptions-tbody" class="divide-y divide-slate-800/60">
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Loading incident data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- VIEW E: CARRIERS DIRECTORY (ADMIN ONLY) -->
            <!-- ------------------------------------- -->
            <section id="carriers-view" class="space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Carrier Fleet Master</h2>
                        <p class="text-xs text-slate-400 mt-1">Authorized logistics transport partners</p>
                    </div>
                    <button onclick="window.openNewCarrierModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition">
                        + Add Carrier
                    </button>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Carrier Name</th>
                                <th class="py-3 px-4">SCAC Code</th>
                                <th class="py-3 px-4">Contact Email</th>
                                <th class="py-3 px-4">Active Shipments</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="carriers-tbody" class="divide-y divide-slate-800/60"></tbody>
                    </table>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- VIEW F: PORTS & HUBS (ADMIN ONLY) -->
            <!-- ------------------------------------- -->
            <section id="locations-view" class="space-y-6 hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Ports & Hub Locations</h2>
                        <p class="text-xs text-slate-400 mt-1">Geographic logistics nodes and coordinate reference</p>
                    </div>
                    <button onclick="window.openNewLocationModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition">
                        + Add Location
                    </button>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Location Name</th>
                                <th class="py-3 px-4">UN/LOCODE</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">City / Country</th>
                                <th class="py-3 px-4">Coordinates (Lat, Lng)</th>
                                <th class="py-3 px-4 text-right">Weather Check</th>
                            </tr>
                        </thead>
                        <tbody id="locations-tbody" class="divide-y divide-slate-800/60"></tbody>
                    </table>
                </div>
            </section>

            <!-- ------------------------------------- -->
            <!-- VIEW G: SYSTEM AUDIT TRAIL (ADMIN ONLY) -->
            <!-- ------------------------------------- -->
            <section id="audit-view" class="space-y-6 hidden">
                <div>
                    <h2 class="text-2xl font-bold text-white tracking-tight">System Audit Log</h2>
                    <p class="text-xs text-slate-400 mt-1">Immutable forensic audit records of critical platform mutations</p>
                </div>
                <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-400 bg-slate-950/60 border-b border-slate-800 uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="py-3 px-4">Timestamp</th>
                                <th class="py-3 px-4">Action</th>
                                <th class="py-3 px-4">User</th>
                                <th class="py-3 px-4">Target Entity</th>
                                <th class="py-3 px-4">IP Address</th>
                            </tr>
                        </thead>
                        <tbody id="audit-tbody" class="divide-y divide-slate-800/60 font-mono text-[11px]"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- ========================================== -->
    <!-- 3. MODALS -->
    <!-- ========================================== -->

    <!-- SHIPMENT DETAIL & TIMELINE MODAL -->
    <div id="shipment-detail-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-800 flex items-center justify-between shrink-0 bg-slate-950/40">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-xl">📦</div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 id="modal-shipment-number" class="text-lg font-bold text-white font-mono">CT-000000</h3>
                            <span id="modal-status-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">STATUS</span>
                            <span id="modal-delay-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">ON TIME</span>
                        </div>
                        <p id="modal-shipment-route" class="text-xs text-slate-400 mt-0.5">Origin &rarr; Destination</p>
                    </div>
                </div>
                <button onclick="window.closeDetailModal()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm transition">✕</button>
            </div>

            <!-- Modal Nav Tabs -->
            <div class="px-6 border-b border-slate-800 flex gap-4 text-xs font-semibold bg-slate-950/20 shrink-0">
                <button onclick="window.switchModalTab('tab-overview')" id="btn-tab-overview" class="py-3 border-b-2 border-blue-500 text-blue-400">Overview</button>
                <button onclick="window.switchModalTab('tab-map')" id="btn-tab-map" class="py-3 border-b-2 border-transparent text-slate-400 hover:text-white">Route Map</button>
                <button onclick="window.switchModalTab('tab-timeline')" id="btn-tab-timeline" class="py-3 border-b-2 border-transparent text-slate-400 hover:text-white">Tracking History</button>
                <button onclick="window.switchModalTab('tab-exceptions')" id="btn-tab-exceptions" class="py-3 border-b-2 border-transparent text-slate-400 hover:text-white">Exceptions</button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
                <!-- TAB 1: OVERVIEW -->
                <div id="tab-overview" class="space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Carrier</span>
                            <p id="det-carrier" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Cargo Type</span>
                            <p id="det-cargo" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Weight & Volume</span>
                            <p id="det-weight-vol" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                        <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Customer</span>
                            <p id="det-customer" class="text-xs font-semibold text-white mt-1">-</p>
                        </div>
                    </div>

                    <!-- State Machine Lifecycle Controller (For Admin & Operator) -->
                    <div id="lifecycle-controller-panel" class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-white">Lifecycle Transition Guard</span>
                            <span class="text-[10px] text-slate-400">Enforces deterministic state sequences</span>
                        </div>
                        <div id="lifecycle-action-buttons" class="flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <!-- TAB 2: ROUTE MAP -->
                <div id="tab-map" class="space-y-4 hidden">
                    <div id="detail-leaflet-map" style="height: 380px; width: 100%;" class="rounded-xl border border-slate-800"></div>
                </div>

                <!-- TAB 3: TIMELINE -->
                <div id="tab-timeline" class="space-y-6 hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white">Milestone Chronology (Append-Only)</span>
                        <button id="btn-add-event-toggle" onclick="window.toggleAddEventForm()" class="px-3 py-1.5 rounded-lg bg-blue-600/20 text-blue-400 hover:bg-blue-600/30 text-xs font-semibold border border-blue-500/30 transition">
                            + Record Milestone
                        </button>
                    </div>

                    <!-- Add Event Form -->
                    <form id="form-record-event" onsubmit="window.submitTrackingEvent(event)" class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-3 hidden">
                        <h4 class="text-xs font-bold text-white">Record Tracking Waypoint</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Status Type</label>
                                <select id="evt-status-type" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                                    <option value="IN_TRANSIT">IN_TRANSIT</option>
                                    <option value="PORT_ARRIVAL">PORT_ARRIVAL</option>
                                    <option value="CUSTOMS_CLEARANCE">CUSTOMS_CLEARANCE</option>
                                    <option value="DELIVERED">DELIVERED</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Latitude</label>
                                <input type="number" step="0.0001" id="evt-lat" placeholder="-6.1018" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Longitude</label>
                                <input type="number" step="0.0001" id="evt-lng" placeholder="106.8833" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Event Description</label>
                            <input type="text" id="evt-desc" required placeholder="Vessel anchored outside Singapore strait waiting berth..." class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="window.toggleAddEventForm()" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white">Cancel</button>
                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-semibold">Save Milestone</button>
                        </div>
                    </form>

                    <!-- Events Timeline Stream -->
                    <div id="timeline-list" class="space-y-4 pl-4 border-l-2 border-slate-800"></div>
                </div>

                <!-- TAB 4: EXCEPTIONS -->
                <div id="tab-exceptions" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white">Reported Incidents</span>
                        <button onclick="window.toggleReportExceptionForm()" class="px-3 py-1.5 rounded-lg bg-rose-600/20 text-rose-400 hover:bg-rose-600/30 text-xs font-semibold border border-rose-500/30 transition">
                            + Flag Exception
                        </button>
                    </div>

                    <!-- Report Exception Form -->
                    <form id="form-report-exception" onsubmit="window.submitException(event)" class="p-4 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-3 hidden">
                        <h4 class="text-xs font-bold text-rose-400">Report Operational Incident</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Incident Type</label>
                                <select id="exc-type" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                                    <option value="CUSTOMS_HOLD">CUSTOMS_HOLD</option>
                                    <option value="WEATHER_DELAY">WEATHER_DELAY</option>
                                    <option value="PORT_CONGESTION">PORT_CONGESTION</option>
                                    <option value="DAMAGED">DAMAGED</option>
                                    <option value="MISROUTED">MISROUTED</option>
                                    <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Severity Matrix</label>
                                <select id="exc-severity" class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white">
                                    <option value="LOW">LOW (0 - 6 Hours)</option>
                                    <option value="MEDIUM">MEDIUM (6 - 24 Hours)</option>
                                    <option value="HIGH">HIGH (&gt; 24 Hours)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Incident Notes / Root Cause</label>
                            <textarea id="exc-notes" rows="2" required placeholder="Customs clearance halted due to discrepancies in commercial invoice tariff codes..." class="w-full bg-slate-900 border border-slate-800 rounded-lg p-2 text-xs text-white"></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="window.toggleReportExceptionForm()" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white">Cancel</button>
                            <button type="submit" class="px-4 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold">Submit Incident</button>
                        </div>
                    </form>

                    <div id="exceptions-list" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW SHIPMENT MODAL -->
    <div id="new-shipment-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl w-full max-w-xl p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <h3 class="text-base font-bold text-white">Create New Shipment Order</h3>
                <button onclick="window.closeNewShipmentModal()" class="text-slate-400 hover:text-white">✕</button>
            </div>
            <form onsubmit="window.handleCreateShipment(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Carrier</label>
                        <select id="create-carrier" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Cargo Type</label>
                        <input type="text" id="create-cargo" required placeholder="Electronics, Medical Supplies..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Origin Port</label>
                        <select id="create-origin" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Destination Port</label>
                        <select id="create-dest" required class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white"></select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Weight (KG)</label>
                        <input type="number" step="0.01" id="create-weight" placeholder="12500" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Volume (CBM)</label>
                        <input type="number" step="0.01" id="create-volume" placeholder="45.5" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Expected Departure</label>
                        <input type="date" id="create-dep" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Expected Arrival</label>
                        <input type="date" id="create-arr" class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2 text-xs text-white">
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="window.closeNewShipmentModal()" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">Submit Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. CORE JAVASCRIPT APPLICATION LOGIC -->
    <!-- ========================================== -->
    <script>
        // Central Application State
        window.appState = {
            token: localStorage.getItem('cargotrace_token') || null,
            user: JSON.parse(localStorage.getItem('cargotrace_user') || 'null'),
            activeShipment: null,
            carriers: [],
            locations: [],
            charts: {},
            maps: {},
        };

        // Utility: API Client with Sanctum Token
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
                    window.toast('Session expired. Please log in again.', 'warning');
                    throw new Error('Unauthorized');
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Request failed');
                }

                return data;
            } catch (err) {
                console.error("API Error:", err);
                throw err;
            }
        };

        // Utility: Toast notifications
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

        // Navigation between views
        window.navigateTo = function(viewId) {
            const views = ['dashboard-view', 'shipments-view', 'map-view', 'exceptions-view', 'carriers-view', 'locations-view', 'audit-view'];
            views.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });

            const target = document.getElementById(viewId);
            if (target) target.classList.remove('hidden');

            // Trigger specific view loaders
            if (viewId === 'dashboard-view') window.loadDashboardData();
            if (viewId === 'shipments-view') window.loadShipments(1);
            if (viewId === 'map-view') window.initGlobalMap();
            if (viewId === 'exceptions-view') window.loadExceptions();
            if (viewId === 'carriers-view') window.loadCarriers();
            if (viewId === 'locations-view') window.loadLocations();
            if (viewId === 'audit-view') window.loadAuditLogs();
        };

        // Quick demo filler
        window.fillDemo = function(email) {
            document.getElementById('login-email').value = email;
            document.getElementById('login-password').value = 'password';
        };

        // Auth: Login
        window.handleLogin = async function(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const btn = document.getElementById('login-btn');

            btn.disabled = true;
            btn.innerText = 'Authenticating...';

            try {
                const res = await window.api('/api/login', {
                    method: 'POST',
                    body: JSON.stringify({ email, password })
                });

                window.appState.token = res.data.token;
                window.appState.user = res.data.user;
                localStorage.setItem('cargotrace_token', res.data.token);
                localStorage.setItem('cargotrace_user', JSON.stringify(res.data.user));

                window.toast(`Welcome back, ${res.data.user.name}!`, 'success');
                window.renderAuthenticatedState();
            } catch (err) {
                window.toast(err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Sign In to Workspace';
            }
        };

        // Auth: Logout
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
            window.toast('Signed out successfully.', 'info');
        };

        // Setup authenticated UI based on role
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
            document.getElementById('user-role-badge').innerText = u.role;
            document.getElementById('user-avatar').innerText = u.name.substring(0, 2).toUpperCase();

            // Role based navigation restriction
            const adminGroup = document.getElementById('admin-nav-group');
            if (u.role === 'admin') {
                adminGroup.classList.remove('hidden');
            } else {
                adminGroup.classList.add('hidden');
            }

            // Preload master dropdown data
            window.loadMasterReferences();
            window.navigateTo('dashboard-view');
        };

        // Preload Carriers & Locations for dropdowns
        window.loadMasterReferences = async function() {
            try {
                const [cRes, lRes] = await Promise.all([
                    window.api('/api/carriers'),
                    window.api('/api/locations')
                ]);
                window.appState.carriers = cRes.data;
                window.appState.locations = lRes.data;

                // Populate create modal dropdowns
                const cSelect = document.getElementById('create-carrier');
                const oSelect = document.getElementById('create-origin');
                const dSelect = document.getElementById('create-dest');

                cSelect.innerHTML = window.appState.carriers.map(c => `<option value="${c.id}">${c.name} (${c.code})</option>`).join('');
                oSelect.innerHTML = window.appState.locations.map(l => `<option value="${l.id}">${l.name} (${l.city || l.country})</option>`).join('');
                dSelect.innerHTML = window.appState.locations.map(l => `<option value="${l.id}">${l.name} (${l.city || l.country})</option>`).join('');
            } catch (err) {}
        };

        // DASHBOARD: Load stats and charts
        window.loadDashboardData = async function() {
            try {
                const res = await window.api('/api/dashboard');
                const stats = res.data.stats;
                const recents = res.data.recent_activity;

                document.getElementById('kpi-total').innerText = stats.total_shipments;
                document.getElementById('kpi-intransit').innerText = stats.in_transit;
                document.getElementById('kpi-ontime').innerText = `${stats.on_time_ratio}%`;
                document.getElementById('kpi-delayed-sub').innerText = `${stats.delayed} shipments delayed`;
                document.getElementById('kpi-exceptions').innerText = stats.open_exceptions;

                // Render recent table
                const tbody = document.getElementById('recent-activity-tbody');
                if (recents.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-6 text-center text-slate-500">No tracking milestones recorded yet.</td></tr>`;
                } else {
                    tbody.innerHTML = recents.map(r => `
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 font-mono text-slate-400">${new Date(r.event_time).toLocaleDateString()}</td>
                            <td class="py-3 px-4 font-bold text-white font-mono">${r.shipment ? r.shipment.shipment_number : '-'}</td>
                            <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">${r.status_type}</span></td>
                            <td class="py-3 px-4 text-slate-300">${r.location ? r.location.name : (r.latitude ? `${r.latitude}, ${r.longitude}` : 'At Sea')}</td>
                            <td class="py-3 px-4 text-slate-400">${r.description}</td>
                            <td class="py-3 px-4 text-slate-500">${r.creator ? r.creator.name : 'System'}</td>
                        </tr>
                    `).join('');
                }

                // Load Chart.js data
                window.renderDashboardCharts();
            } catch (err) {
                console.error(err);
            }
        };

        // Chart.js renderers
        window.renderDashboardCharts = async function() {
            try {
                const [statusRes, carrierRes, trendRes] = await Promise.all([
                    window.api('/api/dashboard/shipments-by-status'),
                    window.api('/api/dashboard/shipments-by-carrier'),
                    window.api('/api/dashboard/delay-trend')
                ]);

                // Destroy old charts if existing
                if (window.appState.charts.status) window.appState.charts.status.destroy();
                if (window.appState.charts.carrier) window.appState.charts.carrier.destroy();
                if (window.appState.charts.trend) window.appState.charts.trend.destroy();

                // 1. Status Chart
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

                // 2. Carrier Chart
                const ctxCarrier = document.getElementById('chart-carrier').getContext('2d');
                window.appState.charts.carrier = new Chart(ctxCarrier, {
                    type: 'bar',
                    data: {
                        labels: carrierRes.data.labels,
                        datasets: [{
                            label: 'Volume',
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

                // 3. Delay Trend Chart
                const ctxTrend = document.getElementById('chart-trend').getContext('2d');
                window.appState.charts.trend = new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: trendRes.data.labels,
                        datasets: [
                            {
                                label: 'On-Time',
                                data: trendRes.data.on_time,
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                fill: true,
                                tension: 0.3,
                            },
                            {
                                label: 'Delayed',
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
                console.error("Chart Error:", err);
            }
        };

        // SHIPMENTS: Load list with server pagination
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
                    tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-500">No shipments found matching filters.</td></tr>`;
                    document.getElementById('shipments-pagination').innerHTML = '';
                    return;
                }

                tbody.innerHTML = items.map(s => {
                    const statusColor = s.current_status === 'DELIVERED' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' :
                        s.current_status === 'IN_TRANSIT' ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' :
                        s.current_status === 'CANCELLED' ? 'bg-rose-500/10 text-rose-400 border-rose-500/20' :
                        'bg-blue-500/10 text-blue-400 border-blue-500/20';

                    const delayBadge = s.is_delayed ?
                        `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">DELAYED (+${s.delay_hours}h)</span>` :
                        `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">ON-TIME</span>`;

                    return `
                        <tr class="hover:bg-slate-800/40 transition cursor-pointer" onclick="window.openShipmentDetail(${s.id})">
                            <td class="py-3 px-4 font-bold text-white font-mono">${s.shipment_number}</td>
                            <td class="py-3 px-4 text-slate-300">${s.carrier ? s.carrier.name : '-'}</td>
                            <td class="py-3 px-4 text-slate-400">${s.origin ? s.origin.name : '?'} &rarr; ${s.destination ? s.destination.name : '?'}</td>
                            <td class="py-3 px-4"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold border ${statusColor}">${s.current_status}</span></td>
                            <td class="py-3 px-4">${delayBadge}</td>
                            <td class="py-3 px-4 text-right">
                                <button onclick="event.stopPropagation(); window.openShipmentDetail(${s.id})" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-xs text-white">Inspect &rarr;</button>
                            </td>
                        </tr>
                    `;
                }).join('');

                // Render Pagination
                document.getElementById('shipments-pagination').innerHTML = `
                    <span>Showing page ${meta.current_page} of ${meta.last_page} (${meta.total} total shipments)</span>
                    <div class="flex gap-2">
                        <button ${meta.current_page <= 1 ? 'disabled class="opacity-40"' : `onclick="window.loadShipments(${meta.current_page - 1})"`} class="px-3 py-1 bg-slate-800 rounded">Previous</button>
                        <button ${meta.current_page >= meta.last_page ? 'disabled class="opacity-40"' : `onclick="window.loadShipments(${meta.current_page + 1})"`} class="px-3 py-1 bg-slate-800 rounded">Next</button>
                    </div>
                `;
            } catch (err) {}
        };

        // Debounced search
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

        // OPEN SHIPMENT DETAIL MODAL
        window.openShipmentDetail = async function(id) {
            try {
                const res = await window.api(`/api/shipments/${id}`);
                const s = res.data.shipment;
                window.appState.activeShipment = s;

                document.getElementById('modal-shipment-number').innerText = s.shipment_number;
                document.getElementById('modal-status-badge').innerText = s.current_status;
                document.getElementById('modal-shipment-route').innerText = `${s.origin ? s.origin.name : 'Origin'} → ${s.destination ? s.destination.name : 'Destination'}`;

                // Delays
                const delayBadge = document.getElementById('modal-delay-badge');
                if (s.is_delayed) {
                    delayBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/10 text-rose-400 border border-rose-500/20';
                    delayBadge.innerText = `DELAYED +${s.delay_hours}h (${s.delay_severity || 'LOW'})`;
                } else {
                    delayBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                    delayBadge.innerText = 'ON TIME';
                }

                // Overview tab values
                document.getElementById('det-carrier').innerText = s.carrier ? `${s.carrier.name} (${s.carrier.code})` : '-';
                document.getElementById('det-cargo').innerText = s.cargo_type || '-';
                document.getElementById('det-weight-vol').innerText = `${s.weight || 0} KG / ${s.volume || 0} CBM`;
                document.getElementById('det-customer').innerText = s.customer ? s.customer.name : 'General Cargo';

                // Render State Machine Action Buttons
                window.renderLifecycleButtons(s);

                // Render Timeline Tab
                window.renderTimeline(s.tracking_events || []);

                // Render Exceptions Tab
                window.renderExceptions(s.exceptions || []);

                // Open modal and switch to overview
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

        // Switch Modal Tabs
        window.switchModalTab = function(tabId) {
            ['tab-overview', 'tab-map', 'tab-timeline', 'tab-exceptions'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
                document.getElementById(`btn-${id}`).className = 'py-3 border-b-2 border-transparent text-slate-400 hover:text-white';
            });

            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(`btn-${tabId}`).className = 'py-3 border-b-2 border-blue-500 text-blue-400';

            // Trigger Leaflet map render on tab show
            if (tabId === 'tab-map' && window.appState.activeShipment) {
                setTimeout(() => window.initDetailMap(window.appState.activeShipment.id), 100);
            }
        };

        // Render State Machine transition buttons
        window.renderLifecycleButtons = function(s) {
            const container = document.getElementById('lifecycle-action-buttons');
            const panel = document.getElementById('lifecycle-controller-panel');

            // Customers cannot transition status
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

            const allowed = transitions[s.current_status] || [];

            if (allowed.length === 0) {
                container.innerHTML = `<span class="text-xs text-slate-500 italic">This shipment is in a terminal locked state (${s.current_status}). No further transitions allowed.</span>`;
                return;
            }

            container.innerHTML = allowed.map(target => {
                const isCancel = target === 'CANCELLED';
                const color = isCancel ? 'bg-rose-600/20 text-rose-400 border-rose-500/30 hover:bg-rose-600/30' : 'bg-blue-600 hover:bg-blue-500 text-white';
                return `<button onclick="window.transitionStatus('${target}')" class="px-4 py-2 rounded-xl text-xs font-semibold border ${color} transition">Transition to &rarr; ${target}</button>`;
            }).join('');
        };

        // Execute status transition
        window.transitionStatus = async function(newStatus) {
            if (!window.appState.activeShipment) return;
            try {
                const res = await window.api(`/api/shipments/${window.appState.activeShipment.id}/status`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status: newStatus })
                });

                window.toast(`Status updated to ${newStatus}`, 'success');
                window.openShipmentDetail(window.appState.activeShipment.id);
                window.loadShipments();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Render timeline stream
        window.renderTimeline = function(events) {
            const container = document.getElementById('timeline-list');
            if (events.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-500 italic py-4">No tracking milestones recorded yet.</p>`;
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
                    ${e.location ? `<span class="text-[10px] text-cyan-400 mt-1 block">⚓ Location: ${e.location.name}</span>` : ''}
                </div>
            `).join('');
        };

        // Toggle add event form
        window.toggleAddEventForm = function() {
            const form = document.getElementById('form-record-event');
            form.classList.toggle('hidden');
        };

        // Submit tracking event
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

                window.toast('Milestone recorded successfully.', 'success');
                window.toggleAddEventForm();
                window.openShipmentDetail(window.appState.activeShipment.id);
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // Render Exceptions tab list
        window.renderExceptions = function(exceptions) {
            const container = document.getElementById('exceptions-list');
            if (exceptions.length === 0) {
                container.innerHTML = `<p class="text-xs text-slate-500 italic py-4">No operational exceptions or incidents flagged for this shipment.</p>`;
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
                    <p class="text-xs text-slate-300">${exc.notes || 'No operational notes provided.'}</p>
                    ${exc.status !== 'RESOLVED' && window.appState.user.role !== 'customer' ? `
                        <div class="flex gap-2 pt-2 border-t border-slate-800/80">
                            <button onclick="window.updateExceptionStatus(${exc.id}, 'RESOLVED')" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-semibold rounded-lg">Mark Resolved</button>
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

                window.toast('Incident reported.', 'warning');
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

                window.toast(`Exception marked as ${status}.`, 'success');
                if (window.appState.activeShipment) {
                    window.openShipmentDetail(window.appState.activeShipment.id);
                }
                window.loadExceptions();
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // LEAFLET MAP: Detail Map
        window.initDetailMap = async function(shipmentId) {
            try {
                const res = await window.api(`/api/shipments/${shipmentId}/map`);
                const mapData = res.data;

                const mapContainer = document.getElementById('detail-leaflet-map');
                if (window.appState.maps.detail) {
                    window.appState.maps.detail.remove();
                }

                const map = L.map('detail-leaflet-map').setView([mapData.origin.latitude, mapData.origin.longitude], 4);
                window.appState.maps.detail = map;

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Origin & Destination Markers
                const originMarker = L.marker([mapData.origin.latitude, mapData.origin.longitude])
                    .addTo(map)
                    .bindPopup(`<b>Origin: ${mapData.origin.name}</b>`);

                const destMarker = L.marker([mapData.destination.latitude, mapData.destination.longitude])
                    .addTo(map)
                    .bindPopup(`<b>Destination: ${mapData.destination.name}</b>`);

                // Waypoints polyline
                const points = [
                    [mapData.origin.latitude, mapData.origin.longitude],
                    ...mapData.waypoints.map(w => [w.latitude, w.longitude]),
                    [mapData.destination.latitude, mapData.destination.longitude]
                ];

                L.polyline(points, { color: '#38bdf8', weight: 3, dashArray: '6, 6' }).addTo(map);

                // Fit bounds
                map.fitBounds(points, { padding: [40, 40] });
            } catch (err) {
                console.error("Map load error:", err);
            }
        };

        // LEAFLET MAP: Global Map Explorer
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
                    attribution: '&copy; OpenStreetMap contributors'
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

        // NEW SHIPMENT MODAL
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

                window.toast(`Shipment ${res.data.shipment_number} created successfully!`, 'success');
                window.closeNewShipmentModal();
                window.loadShipments(1);
            } catch (err) {
                window.toast(err.message, 'error');
            }
        };

        // EXCEPTIONS DIRECTORY VIEW
        window.loadExceptions = async function() {
            try {
                const res = await window.api('/api/exceptions');
                const list = res.data;
                const tbody = document.getElementById('exceptions-tbody');

                if (list.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-slate-500">No active exceptions pending resolution.</td></tr>`;
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
                            <button onclick="window.updateExceptionStatus(${exc.id}, 'RESOLVED')" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded text-[11px] font-semibold">Resolve</button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // CARRIERS VIEW
        window.loadCarriers = async function() {
            try {
                const res = await window.api('/api/carriers');
                const tbody = document.getElementById('carriers-tbody');
                tbody.innerHTML = res.data.map(c => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-white">${c.name}</td>
                        <td class="py-3 px-4 font-mono text-cyan-400">${c.code}</td>
                        <td class="py-3 px-4 text-slate-400">${c.contact_email || '-'}</td>
                        <td class="py-3 px-4 text-slate-300 font-bold">${c.shipments_count || 0}</td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-xs text-slate-500">Active</span>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // LOCATIONS VIEW WITH RESILIENT WEATHER CHECK
        window.loadLocations = async function() {
            try {
                const res = await window.api('/api/locations');
                const tbody = document.getElementById('locations-tbody');
                tbody.innerHTML = res.data.map(loc => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 font-bold text-white">${loc.name}</td>
                        <td class="py-3 px-4 font-mono text-cyan-400">${loc.code}</td>
                        <td class="py-3 px-4 uppercase text-[10px] text-slate-400">${loc.type}</td>
                        <td class="py-3 px-4 text-slate-300">${loc.city || ''}, ${loc.country || ''}</td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-400">${loc.latitude}, ${loc.longitude}</td>
                        <td class="py-3 px-4 text-right">
                            <button onclick="window.checkLiveWeather(${loc.latitude}, ${loc.longitude}, '${loc.name}')" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-cyan-400 transition">
                                ⛅ Check Weather
                            </button>
                        </td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // Check weather API
        window.checkLiveWeather = async function(lat, lon, name) {
            try {
                const res = await window.api(`/api/weather?lat=${lat}&lon=${lon}`);
                const w = res.data;
                window.toast(`${name}: ${w.condition}, Temp: ${w.temperature}°C, Wind: ${w.wind_speed} km/h`, 'info');
            } catch (err) {
                window.toast('Could not fetch weather data.', 'error');
            }
        };

        // AUDIT LOGS VIEW
        window.loadAuditLogs = async function() {
            try {
                const res = await window.api('/api/audit-logs');
                const tbody = document.getElementById('audit-tbody');
                tbody.innerHTML = res.data.map(a => `
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 text-slate-400">${new Date(a.created_at).toLocaleString()}</td>
                        <td class="py-3 px-4 font-bold text-cyan-400">${a.action}</td>
                        <td class="py-3 px-4 text-slate-300">${a.user ? a.user.name : 'System'}</td>
                        <td class="py-3 px-4 text-slate-400">${a.auditable_type || 'General'}</td>
                        <td class="py-3 px-4 text-slate-500">${a.ip_address || '127.0.0.1'}</td>
                    </tr>
                `).join('');
            } catch (err) {}
        };

        // Bootstrap on page load
        document.addEventListener('DOMContentLoaded', () => {
            window.renderAuthenticatedState();
        });
    </script>
</body>
</html>
