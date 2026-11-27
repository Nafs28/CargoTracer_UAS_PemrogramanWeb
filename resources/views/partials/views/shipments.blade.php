<section id="shipments-view" class="space-y-6 hidden">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-[#E2DFD7]">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Manajemen Pengiriman</p>
            <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-1">Direktori Kargo</h2>
            <p class="text-sm text-[#44403C] mt-1">Cari pengiriman berdasarkan nomor kargo, status, pelabuhan asal, atau keterlambatan.</p>
        </div>
        <button onclick="window.openNewShipmentModal()" class="px-4 py-2.5 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white transition self-start">
            + Tambah Kargo Baru
        </button>
    </div>

    <!-- Kontrol Filter, Pencarian & Sorting -->
    <div class="p-4 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        <div>
            <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Cari Kargo</label>
            <input type="text" id="filter-search" oninput="window.debounceSearch()" placeholder="Nomor kargo, barang..." class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3 py-2 text-xs text-[#1C1917] placeholder-[#78716C] focus:outline-none focus:border-[#C2410C]">
        </div>
        <div>
            <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Status Kargo</label>
            <select id="filter-status" onchange="window.loadShipments(1)" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3 py-2 text-xs text-[#1C1917] focus:outline-none focus:border-[#C2410C]">
                <option value="">Semua Status</option>
                <option value="CREATED">CREATED</option>
                <option value="BOOKED">BOOKED</option>
                <option value="PICKED_UP">PICKED_UP</option>
                <option value="IN_TRANSIT">IN_TRANSIT</option>
                <option value="DELIVERED">DELIVERED</option>
                <option value="CANCELLED">CANCELLED</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Pelabuhan Asal</label>
            <select id="filter-origin" onchange="window.loadShipments(1)" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3 py-2 text-xs text-[#1C1917] focus:outline-none focus:border-[#C2410C]">
                <option value="">Semua Asal</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Kondisi Jadwal</label>
            <select id="filter-delayed" onchange="window.loadShipments(1)" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3 py-2 text-xs text-[#1C1917] focus:outline-none focus:border-[#C2410C]">
                <option value="">Semua Pengiriman</option>
                <option value="true">Hanya Terlambat</option>
                <option value="false">Hanya Tepat Waktu</option>
            </select>
        </div>
        <div>
            <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Urutkan</label>
            <select id="filter-sort" onchange="window.loadShipments(1)" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3 py-2 text-xs text-[#1C1917] focus:outline-none focus:border-[#C2410C]">
                <option value="created_at:desc">Terbaru</option>
                <option value="expected_arrival:asc">ETA Terdekat</option>
                <option value="delay_hours:desc">Keterlambatan Tertinggi</option>
                <option value="shipment_number:asc">Nomor Kargo (A-Z)</option>
            </select>
        </div>
        <div class="flex items-end">
            <button onclick="window.resetShipmentFilters()" class="w-full py-2 bg-[#F9F8F6] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-xs font-semibold text-[#1C1917] rounded-lg transition">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Tabel Data Kargo -->
    <div class="rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-[#57534E] bg-[#E7E4DC]/60 border-b border-[#E2DFD7] uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4">Nomor Kargo</th>
                        <th class="py-3 px-4">Armada Carrier</th>
                        <th class="py-3 px-4">Rute Pelayaran</th>
                        <th class="py-3 px-4">Status Terkini</th>
                        <th class="py-3 px-4">Kondisi &amp; Keterlambatan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="shipments-tbody" class="divide-y divide-[#E2DFD7]">
                    <tr><td colspan="6" class="py-8 text-center text-[#78716C]">Memuat data kargo...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="shipments-pagination" class="p-4 border-t border-[#E2DFD7] flex items-center justify-between text-xs text-[#57534E]"></div>
    </div>
</section>
