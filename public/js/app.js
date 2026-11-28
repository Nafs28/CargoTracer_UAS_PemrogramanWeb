/**
 * CargoTrace — Authentication, Navigation & App Initialization (Warm Alabaster & Terracotta)
 */

window.switchAuthTab = function (tab) {
    const isLogin = tab === 'login';
    document.getElementById('login-form').classList.toggle('hidden', !isLogin);
    document.getElementById('register-form').classList.toggle('hidden', isLogin);
    document.getElementById('tab-btn-login').className = isLogin
        ? 'py-2 rounded-md bg-[#C2410C] text-white transition'
        : 'py-2 rounded-md text-[#57534E] hover:text-[#1C1917] transition';
    document.getElementById('tab-btn-register').className = !isLogin
        ? 'py-2 rounded-md bg-[#C2410C] text-white transition'
        : 'py-2 rounded-md text-[#57534E] hover:text-[#1C1917] transition';
};

window.fillDemo = function (email) {
    window.switchAuthTab('login');
    document.getElementById('login-email').value = email;
    document.getElementById('login-password').value = 'password';
};

window.handleLogin = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api('/api/login', {
            method: 'POST',
            body: JSON.stringify({
                email: document.getElementById('login-email').value,
                password: document.getElementById('login-password').value,
            }),
        });
        window.appState.token = res.data.token;
        window.appState.user = res.data.user;
        localStorage.setItem('cargotrace_token', res.data.token);
        localStorage.setItem('cargotrace_user', JSON.stringify(res.data.user));
        window.toast(`Selamat datang, ${res.data.user.name}!`, 'success');
        window.renderAuthenticatedState();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.handleRegister = async function (e) {
    e.preventDefault();
    const password = document.getElementById('reg-password').value;
    const passwordConfirmation = document.getElementById('reg-password-confirmation').value;
    if (password !== passwordConfirmation) {
        window.toast('Konfirmasi kata sandi tidak cocok.', 'error');
        return;
    }
    try {
        const res = await window.api('/api/register', {
            method: 'POST',
            body: JSON.stringify({
                name: document.getElementById('reg-name').value,
                email: document.getElementById('reg-email').value,
                password,
                password_confirmation: passwordConfirmation,
            }),
        });
        window.appState.token = res.data.token;
        window.appState.user = res.data.user;
        localStorage.setItem('cargotrace_token', res.data.token);
        localStorage.setItem('cargotrace_user', JSON.stringify(res.data.user));
        window.toast('Akun berhasil dibuat!', 'success');
        window.renderAuthenticatedState();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.handleLogout = async function () {
    try {
        await window.api('/api/logout', { method: 'POST' });
    } catch {
        // Ignore if token expired
    }
    localStorage.removeItem('cargotrace_token');
    localStorage.removeItem('cargotrace_user');
    window.appState.token = null;
    window.appState.user = null;
    window.renderAuthenticatedState();
};

window.navigateTo = function (viewId) {
    const viewNavPairs = {
        'dashboard-view': 'nav-dashboard',
        'shipments-view': 'nav-shipments',
        'map-view': 'nav-map',
        'exceptions-view': 'nav-exceptions',
        'news-view': 'nav-news',
        'carriers-view': 'nav-carriers',
        'locations-view': 'nav-locations',
        'users-view': 'nav-users',
        'audit-view': 'nav-audit',
    };

    Object.entries(viewNavPairs).forEach(([vId, navId]) => {
        const viewEl = document.getElementById(vId);
        if (viewEl) {
            const isTarget = vId === viewId;
            viewEl.classList.toggle('hidden', !isTarget);
            if (isTarget) {
                viewEl.classList.remove('view-animate');
                void viewEl.offsetWidth;
                viewEl.classList.add('view-animate');
            }
        }
        const btn = document.getElementById(navId);
        if (btn) {
            btn.className = vId === viewId
                ? 'nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg bg-[#C2410C] text-white transition text-left'
                : 'nav-item w-full flex items-center px-3 py-2 text-xs font-semibold rounded-lg text-[#57534E] hover:text-[#1C1917] hover:bg-[#E7E4DC] transition text-left';
        }
    });

    const navMap = {
        'dashboard-view': () => window.loadDashboardData(),
        'shipments-view': () => window.loadShipments(1),
        'map-view': () => window.loadGlobalMap(),
        'exceptions-view': () => window.loadExceptions(),
        'news-view': () => window.loadMarketNews(),
        'carriers-view': () => window.loadCarriers(),
        'locations-view': () => window.loadLocations(),
        'users-view': () => window.loadUsers(),
        'audit-view': () => window.loadAuditLogs(),
    };

    if (navMap[viewId]) navMap[viewId]();
};

window.renderAuthenticatedState = function () {
    const authSec = document.getElementById('auth-section');
    const mainSec = document.getElementById('main-section');

    if (!window.appState.token || !window.appState.user) {
        authSec.classList.remove('hidden');
        mainSec.classList.add('hidden');
        return;
    }

    authSec.classList.add('hidden');
    mainSec.classList.remove('hidden');

    const user = window.appState.user;
    document.getElementById('user-name').textContent = user.name;
    document.getElementById('user-role-badge').textContent = user.role;

    const isAdmin = user.role === 'admin';
    const isOps = user.role === 'admin' || user.role === 'operator';
    document.getElementById('admin-nav-group').classList.toggle('hidden', !isAdmin);
    document.getElementById('nav-exceptions').classList.toggle('hidden', !isOps);

    window.loadMasterReferences();
    window.loadNotifications();
    window.navigateTo('dashboard-view');
};

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('email')) {
        window.fillDemo(urlParams.get('email'));
    }
    window.renderAuthenticatedState();
});
