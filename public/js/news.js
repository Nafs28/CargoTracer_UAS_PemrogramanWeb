/**
 * CargoTrace — Global Market & Maritime Trade News (Google News RSS)
 */

window.activeNewsTopic = 'global_market';

window.loadMarketNews = async function (topic = null) {
    if (topic) {
        window.activeNewsTopic = topic;
    }

    const currentTopic = window.activeNewsTopic || 'global_market';

    // Update active topic button styling
    document.querySelectorAll('.news-topic-btn').forEach((btn) => {
        const isActive = btn.getAttribute('data-news-topic') === currentTopic;
        btn.className = isActive
            ? 'news-topic-btn px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#C2410C] text-white transition'
            : 'news-topic-btn px-3.5 py-2 rounded-lg text-xs font-semibold bg-[#F0EEE9] border border-[#E2DFD7] text-[#57534E] hover:bg-[#E7E4DC] transition';
    });

    const grid = document.getElementById('news-grid');
    if (grid) {
        grid.innerHTML = '<div class="col-span-full py-10 text-center text-xs text-[#78716C]">Memuat berita pasar global...</div>';
    }

    try {
        const res = await window.api(`/api/market-news?topic=${encodeURIComponent(currentTopic)}&limit=12`);
        const payload = res.data || {};
        const articles = payload.articles || [];

        const badge = document.getElementById('news-provider-badge');
        if (badge && payload.source_provider) {
            badge.textContent = payload.source_provider;
        }

        if (!articles.length) {
            grid.innerHTML = '<div class="col-span-full py-10 text-center text-xs text-[#78716C]">Belum ada berita yang tersedia.</div>';
            return;
        }

        grid.innerHTML = articles.map((item) => `
            <article class="p-5 rounded-xl bg-[#F0EEE9] border border-[#E2DFD7] flex flex-col justify-between gap-4 hover:bg-[#E7E4DC]/60 transition">
                <div>
                    <div class="flex items-center justify-between gap-2 text-[11px] text-[#78716C] mb-2">
                        <span class="font-semibold text-[#1C1917]">${item.source || 'Global Wire'}</span>
                        <span class="font-mono">${new Date(item.published_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</span>
                    </div>
                    <h3 class="text-sm font-semibold text-[#1C1917] leading-snug">${item.title}</h3>
                </div>
                <a href="${item.url}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-[#C2410C] hover:underline">
                    Baca Selengkapnya →
                </a>
            </article>
        `).join('');
    } catch (err) {
        window.toast(err.message, 'error');
        if (grid) {
            grid.innerHTML = '<div class="col-span-full py-10 text-center text-xs text-[#C2410C]">Gagal memuat berita pasar global.</div>';
        }
    }
};
