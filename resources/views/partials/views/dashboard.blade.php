<section id="dashboard-view" class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-[#E2DFD7]">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Ringkasan Operasional</p>
            <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-1">Dasbor Utama</h2>
            <p class="text-sm text-[#44403C] mt-1">Ringkasan jumlah pengiriman, ketepatan jadwal, dan kendala yang perlu ditangani.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.loadDashboardData()" class="px-3.5 py-2 rounded-lg bg-[#F0EEE9] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-xs font-semibold text-[#1C1917] transition">
                Segarkan Data
            </button>
            <button onclick="window.openNewShipmentModal()" class="px-4 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white transition">
                + Buat Order Kargo
            </button>
        </div>
    </div>

    <!-- 4 Kartu Metrik KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-5 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <span class="text-xs font-semibold text-[#57534E]">Total Volume Kargo</span>
            <p id="kpi-total" class="font-serif-editorial text-4xl font-semibold text-[#1C1917] mt-2">0</p>
            <span class="text-xs text-[#78716C] mt-1 block">Seluruh order tercatat</span>
        </div>
        <div class="p-5 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <span class="text-xs font-semibold text-[#57534E]">Dalam Perjalanan</span>
            <p id="kpi-intransit" class="font-serif-editorial text-4xl font-semibold text-[#1C1917] mt-2">0</p>
            <span class="text-xs text-[#78716C] mt-1 block">Armada sedang berlayar</span>
        </div>
        <div class="p-5 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <span class="text-xs font-semibold text-[#57534E]">Rasio Tepat Waktu (SLA)</span>
            <p id="kpi-ontime" class="font-serif-editorial text-4xl font-semibold text-[#1C1917] mt-2">100%</p>
            <span id="kpi-delayed-sub" class="text-xs text-[#78716C] mt-1 block">0 kargo terlambat</span>
        </div>
        <div class="p-5 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <span class="text-xs font-semibold text-[#57534E]">Kendala Terbuka</span>
            <p id="kpi-exceptions" class="font-serif-editorial text-4xl font-semibold text-[#C2410C] mt-2">0</p>
            <span class="text-xs text-[#78716C] mt-1 block">Butuh eskalasi operasional</span>
        </div>
    </div>

    <!-- Baris Grafik Interaktif -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <div class="mb-4 pb-3 border-b border-[#E2DFD7] flex items-center justify-between">
                <h3 class="text-sm font-semibold text-[#1C1917]">Distribusi Status Kargo</h3>
                <span class="text-[11px] text-[#78716C]">Siklus hidup</span>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="chart-status"></canvas>
            </div>
        </div>
        <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <div class="mb-4 pb-3 border-b border-[#E2DFD7] flex items-center justify-between">
                <h3 class="text-sm font-semibold text-[#1C1917]">Volume Per Armada</h3>
                <span class="text-[11px] text-[#78716C]">Pangsa muatan</span>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="chart-carrier"></canvas>
            </div>
        </div>
        <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
            <div class="mb-4 pb-3 border-b border-[#E2DFD7] flex items-center justify-between">
                <h3 class="text-sm font-semibold text-[#1C1917]">Tren Pengiriman 14 Hari</h3>
                <span class="text-[11px] text-[#78716C]">Tepat waktu vs Terlambat</span>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="chart-trend"></canvas>
            </div>
        </div>
    </div>

    <!-- Aktivitas Pelacakan Terkini -->
    <div class="p-6 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7]">
        <div class="mb-4 pb-3 border-b border-[#E2DFD7] flex items-center justify-between">
            <h3 class="text-sm font-semibold text-[#1C1917]">Milestone Pelacakan Terkini</h3>
            <span class="text-xs font-medium text-[#C2410C]">Riwayat Append-Only</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-[#57534E] border-b border-[#E2DFD7] uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Waktu Kejadian</th>
                        <th class="py-3 px-4">Nomor Kargo</th>
                        <th class="py-3 px-4">Status / Milestone</th>
                        <th class="py-3 px-4">Lokasi / Koordinat</th>
                        <th class="py-3 px-4">Deskripsi Perjalanan</th>
                        <th class="py-3 px-4">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody id="recent-activity-tbody" class="divide-y divide-[#E2DFD7]">
                    <tr><td colspan="6" class="py-6 text-center text-[#78716C]">Memuat data operasional...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
