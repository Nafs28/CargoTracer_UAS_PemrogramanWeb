<section id="locations-view" class="space-y-6 hidden">
    <div class="flex items-end justify-between pb-6 border-b border-[#E2DFD7]">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Master Data</p>
            <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-1">Pelabuhan &amp; Hub Logistik</h2>
            <p class="text-sm text-[#57534E] mt-1">Daftar simpul geografis, koordinat pelabuhan, dan telemetri cuaca.</p>
        </div>
        <button onclick="window.openNewLocationModal()" class="px-4 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white transition">
            + Tambah Lokasi
        </button>
    </div>
    <div class="rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="text-[#57534E] bg-[#E7E4DC]/60 border-b border-[#E2DFD7] uppercase tracking-wider text-[10px]">
                <tr>
                    <th class="py-3 px-4">Nama Pelabuhan / Hub</th>
                    <th class="py-3 px-4">Kode LOCODE</th>
                    <th class="py-3 px-4">Jenis</th>
                    <th class="py-3 px-4">Kota &amp; Negara</th>
                    <th class="py-3 px-4">Koordinat</th>
                    <th class="py-3 px-4 text-right">Cek Cuaca &amp; Aksi</th>
                </tr>
            </thead>
            <tbody id="locations-tbody" class="divide-y divide-[#E2DFD7]"></tbody>
        </table>
    </div>
</section>
