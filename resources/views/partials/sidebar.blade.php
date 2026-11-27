<aside class="w-full md:w-64 bg-[#F0EEE9] border-r border-[#E2DFD7] flex flex-col justify-between shrink-0">
    <div>
        <!-- Header Brand & Lonceng Notifikasi -->
        <div class="p-5 border-b border-[#E2DFD7] flex items-center justify-between">
            <div>
                <span class="font-serif-editorial text-xl font-semibold text-[#1C1917] tracking-tight">Cargo<span class="text-[#C2410C]">Trace</span></span>
                <span class="block text-[11px] text-[#78716C] uppercase tracking-widest font-medium">Hub Logistik</span>
            </div>
            <button onclick="window.toggleNotificationsPanel()" class="relative px-2.5 py-1.5 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-xs font-semibold text-[#1C1917] transition" title="Notifikasi">
                Notifikasi
                <span id="notif-badge" class="hidden ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-[#C2410C] text-white">0</span>
            </button>
        </div>

        <!-- Panel Dropdown Notifikasi -->
        <div id="notifications-panel" class="hidden mx-3 mt-2 p-3 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7] space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
            <div class="flex items-center justify-end border-b border-[#E2DFD7] pb-2">
                <button onclick="window.markAllNotificationsRead()" class="text-[11px] text-[#C2410C] hover:underline font-medium">Tandai semua dibaca</button>
            </div>
            <div id="notifications-list" class="space-y-2 text-xs text-[#57534E]">
                <p class="text-center py-2">Memuat notifikasi...</p>
            </div>
        </div>

        <!-- Profil Role Pengguna -->
        <div class="p-3.5 mx-3 my-4 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7]">
            <p id="user-name" class="text-xs font-semibold text-[#1C1917] truncate">Administrator</p>
            <span id="user-role-badge" class="inline-block text-[10px] font-semibold uppercase tracking-wider text-[#C2410C]">
                admin
            </span>
        </div>

        <!-- Menu Navigasi -->
        <nav class="px-3 space-y-1">
            <button onclick="window.navigateTo('dashboard-view')" id="nav-dashboard" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                Dasbor Analitik
            </button>
            <button onclick="window.navigateTo('shipments-view')" id="nav-shipments" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                Direktori Kargo
            </button>
            <button onclick="window.navigateTo('map-view')" id="nav-map" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                Peta GIS Maritim
            </button>
            <button onclick="window.navigateTo('exceptions-view')" id="nav-exceptions" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                Eskalasi Kendala
            </button>
            <button onclick="window.navigateTo('news-view')" id="nav-news" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                Berita Pasar Global
            </button>

            <!-- Navigasi Khusus Administrator -->
            <div id="admin-nav-group" class="pt-4 mt-4 border-t border-[#E2DFD7] space-y-1">
                <span class="px-3 text-[10px] uppercase tracking-wider text-[#C2410C] font-bold block mb-1">Master Data &amp; Admin</span>
                <button onclick="window.navigateTo('carriers-view')" id="nav-carriers" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                    Armada Carrier
                </button>
                <button onclick="window.navigateTo('locations-view')" id="nav-locations" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                    Pelabuhan &amp; Hub
                </button>
                <button onclick="window.navigateTo('users-view')" id="nav-users" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                    Kelola Pengguna
                </button>
                <button onclick="window.navigateTo('audit-view')" id="nav-audit" class="nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left">
                    Jejak Audit Sistem
                </button>
            </div>
        </nav>
    </div>

    <!-- Tombol Keluar (Logout) -->
    <div class="p-4 border-t border-[#E2DFD7]">
        <button onclick="window.handleLogout()" class="w-full py-2 text-xs font-semibold text-[#C2410C] hover:bg-[#E7E4DC] rounded-lg transition border border-[#E2DFD7]">
            Keluar Sesi
        </button>
    </div>
</aside>
