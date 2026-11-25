/**
 * CargoTrace — Core API Helper & Shared State (Warm Alabaster & Terracotta Edition)
 */

window.appState = {
    token: localStorage.getItem('cargotrace_token') || null,
    user: JSON.parse(localStorage.getItem('cargotrace_user') || 'null'),
    activeShipmentId: null,
    carriers: [],
    locations: [],
    charts: {},
    globalMap: null,
    detailMap: null,
};

window.api = async function (endpoint, options = {}) {
    const headers = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...(options.headers || {}),
    };

    if (window.appState.token) {
        headers.Authorization = `Bearer ${window.appState.token}`;
    }

    const response = await fetch(endpoint, { ...options, headers });
    const data = await response.json().catch(() => ({}));

    if (response.status === 401 && window.appState.token) {
        localStorage.removeItem('cargotrace_token');
        localStorage.removeItem('cargotrace_user');
        window.appState.token = null;
        window.appState.user = null;
        window.renderAuthenticatedState();
    }

    if (!response.ok) {
        throw new Error(data.message || `HTTP Error ${response.status}`);
    }

    return data;
};

window.toast = function (message, type = 'info') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const colors = {
        success: 'bg-[#F0EEE9] border-[#15803D] text-[#166534]',
        error: 'bg-[#F0EEE9] border-[#C2410C] text-[#9A3412]',
        warning: 'bg-[#F0EEE9] border-[#B45309] text-[#92400E]',
        info: 'bg-[#F0EEE9] border-[#E2DFD7] text-[#1C1917]',
    };

    const el = document.createElement('div');
    el.className = `pointer-events-auto px-4 py-3 rounded-lg border text-xs font-semibold transition-all duration-300 ${colors[type] || colors.info}`;
    el.textContent = message;
    container.appendChild(el);

    setTimeout(() => {
        el.classList.add('opacity-0', 'translate-x-4');
        setTimeout(() => el.remove(), 300);
    }, 3500);
};

window.statusBadge = function (status) {
    const map = {
        CREATED: 'bg-[#E7E4DC] text-[#57534E] border-[#D6D3CB]',
        BOOKED: 'bg-[#F0EEE9] text-[#1C1917] border-[#D6D3CB]',
        PICKED_UP: 'bg-[#FEF3C7] text-[#92400E] border-[#FDE68A]',
        IN_TRANSIT: 'bg-[#FFEDD5] text-[#C2410C] border-[#FDBA74]',
        DELIVERED: 'bg-[#DCFCE7] text-[#166534] border-[#BBF7D0]',
        CANCELLED: 'bg-[#FEE2E2] text-[#991B1B] border-[#FECACA]',
    };
    const cls = map[status] || 'bg-[#E7E4DC] text-[#57534E] border-[#D6D3CB]';
    return `<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${cls}">${status}</span>`;
};

window.severityBadge = function (severity) {
    const map = {
        LOW: 'bg-[#FEF3C7] text-[#92400E] border-[#FDE68A]',
        MEDIUM: 'bg-[#FFEDD5] text-[#C2410C] border-[#FDBA74]',
        HIGH: 'bg-[#FEE2E2] text-[#991B1B] border-[#FECACA]',
    };
    const cls = map[severity] || 'bg-[#E7E4DC] text-[#57534E] border-[#D6D3CB]';
    return `<span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${cls}">${severity || '-'}</span>`;
};

window.loadMasterReferences = async function () {
    try {
        const [carriersRes, locationsRes] = await Promise.all([
            window.api('/api/carriers'),
            window.api('/api/locations'),
        ]);
        window.appState.carriers = carriersRes.data || [];
        window.appState.locations = locationsRes.data || [];

        const originFilter = document.getElementById('filter-origin');
        if (originFilter) {
            originFilter.innerHTML = '<option value="">Semua Asal</option>' +
                window.appState.locations.map((l) => `<option value="${l.id}">${l.name} (${l.code})</option>`).join('');
        }
    } catch (err) {
        console.error('Failed to load master references:', err);
    }
};
