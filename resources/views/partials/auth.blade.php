<div id="auth-section" class="min-h-full flex items-center justify-center p-6 bg-[#F9F8F6]">
    <div class="w-full max-w-md bg-[#F0EEE9] border border-[#E2DFD7] rounded-xl p-8">
        <div class="mb-7 pb-5 border-b border-[#E2DFD7]">
            <p class="text-xs font-semibold uppercase tracking-widest text-[#C2410C]">CargoTrace</p>
            <h1 class="font-serif-editorial text-3xl font-semibold text-[#1C1917] mt-1">Masuk ke Workspace</h1>
            <p class="text-sm text-[#57534E] mt-1">Platform visibilitas dan pelacakan kargo global.</p>
        </div>

        <!-- Tab Masuk / Daftar -->
        <div class="grid grid-cols-2 gap-1 p-1 bg-[#F9F8F6] rounded-lg border border-[#E2DFD7] mb-6 text-xs font-semibold">
            <button type="button" id="tab-btn-login" onclick="window.switchAuthTab('login')" class="py-2 rounded-md bg-[#C2410C] text-white transition">Masuk</button>
            <button type="button" id="tab-btn-register" onclick="window.switchAuthTab('register')" class="py-2 rounded-md text-[#57534E] hover:text-[#1C1917] transition">Daftar Akun</button>
        </div>

        <!-- FORM MASUK -->
        <form id="login-form" onsubmit="window.handleLogin(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-[#57534E] mb-1.5">Alamat Email</label>
                <input type="email" id="login-email" required class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3.5 py-2.5 text-sm text-[#1C1917] placeholder-[#78716C] focus:outline-none focus:border-[#C2410C]" placeholder="nama@perusahaan.com" value="admin@cargotrace.com">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#57534E] mb-1.5">Kata Sandi</label>
                <input type="password" id="login-password" required class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3.5 py-2.5 text-sm text-[#1C1917] placeholder-[#78716C] focus:outline-none focus:border-[#C2410C]" placeholder="••••••••" value="password">
            </div>
            <button type="submit" id="login-btn" class="w-full py-3 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-sm font-semibold transition">
                Masuk ke Workspace →
            </button>
        </form>

        <!-- FORM DAFTAR (CUSTOMER BARU) -->
        <form id="register-form" onsubmit="window.handleRegister(event)" class="space-y-4 hidden">
            <div>
                <label class="block text-xs font-semibold text-[#57534E] mb-1.5">Nama Lengkap / Perusahaan</label>
                <input type="text" id="reg-name" required class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3.5 py-2.5 text-sm text-[#1C1917] placeholder-[#78716C] focus:outline-none focus:border-[#C2410C]" placeholder="PT Logistik Sejahtera">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#57534E] mb-1.5">Alamat Email</label>
                <input type="email" id="reg-email" required class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3.5 py-2.5 text-sm text-[#1C1917] placeholder-[#78716C] focus:outline-none focus:border-[#C2410C]" placeholder="user@perusahaan.com">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#57534E] mb-1.5">Kata Sandi (Min. 8 karakter)</label>
                <input type="password" id="reg-password" required minlength="8" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg px-3.5 py-2.5 text-sm text-[#1C1917] placeholder-[#78716C] focus:outline-none focus:border-[#C2410C]" placeholder="••••••••">
            </div>
            <button type="submit" id="reg-btn" class="w-full py-3 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-sm font-semibold transition">
                Daftar &amp; Masuk →
            </button>
        </form>

        <!-- Pintasan Akun Demo -->
        <div class="mt-6 pt-5 border-t border-[#E2DFD7]">
            <span class="text-xs text-[#78716C] block mb-2.5">Pintasan akun demo (kata sandi: <code class="text-[#1C1917]">password</code>):</span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="window.fillDemo('admin@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-md bg-[#F9F8F6] text-[#1C1917] border border-[#E2DFD7] hover:bg-[#E7E4DC] transition">Admin</button>
                <button type="button" onclick="window.fillDemo('operator@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-md bg-[#F9F8F6] text-[#1C1917] border border-[#E2DFD7] hover:bg-[#E7E4DC] transition">Operator</button>
                <button type="button" onclick="window.fillDemo('customer@cargotrace.com')" class="px-2 py-1.5 text-xs font-semibold rounded-md bg-[#F9F8F6] text-[#1C1917] border border-[#E2DFD7] hover:bg-[#E7E4DC] transition">Pelanggan</button>
            </div>
        </div>

        <div class="mt-5 text-center">
            <a href="/" class="text-xs text-[#57534E] hover:text-[#C2410C] transition">← Kembali ke Halaman Utama</a>
        </div>
    </div>
</div>
