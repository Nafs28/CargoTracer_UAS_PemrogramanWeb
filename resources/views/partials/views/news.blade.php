<section id="news-view" class="space-y-6 hidden">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-[#E2DFD7]">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">Intelijen Perdagangan</p>
            <h2 class="font-serif-editorial text-3xl sm:text-4xl font-semibold text-[#1C1917] mt-1">Berita Pasar Global</h2>
            <p class="text-sm text-[#57534E] mt-1">Pembaruan terkini seputar ekonomi global, pelayaran maritim, rantai pasok, dan ekspor-impor.</p>
        </div>
        <div class="flex items-center gap-2">
            <span id="news-provider-badge" class="text-[11px] font-mono text-[#78716C] px-2.5 py-1 rounded bg-[#F0EEE9] border border-[#E2DFD7]">Google News RSS</span>
            <button onclick="window.loadMarketNews()" class="px-3.5 py-2 rounded-lg bg-[#F0EEE9] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-xs font-semibold text-[#1C1917] transition">
                Segarkan Berita
            </button>
        </div>
    </div>

    <!-- Filter Topik Berita -->
    <div class="flex flex-wrap gap-2">
        <button onclick="window.loadMarketNews('global_market')" data-news-topic="global_market" class="news-topic-btn px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#C2410C] text-white transition">
            Pasar Global
        </button>
        <button onclick="window.loadMarketNews('maritime_shipping')" data-news-topic="maritime_shipping" class="news-topic-btn px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#F0EEE9] border border-[#E2DFD7] text-[#57534E] hover:bg-[#E7E4DC] transition">
            Pelayaran &amp; Kargo
        </button>
        <button onclick="window.loadMarketNews('supply_chain')" data-news-topic="supply_chain" class="news-topic-btn px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#F0EEE9] border border-[#E2DFD7] text-[#57534E] hover:bg-[#E7E4DC] transition">
            Rantai Pasok &amp; Pelabuhan
        </button>
        <button onclick="window.loadMarketNews('indonesia_trade')" data-news-topic="indonesia_trade" class="news-topic-btn px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#F0EEE9] border border-[#E2DFD7] text-[#57534E] hover:bg-[#E7E4DC] transition">
            Ekspor-Impor Indonesia
        </button>
    </div>

    <!-- Grid Artikel Berita -->
    <div id="news-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="col-span-full py-10 text-center text-xs text-[#78716C]">Memuat berita pasar global...</div>
    </div>
</section>
