/**
 * CargoTrace — Shipments Directory, Lifecycle State Machine, Timeline & Exceptions
 */

let searchTimer = null;

window.debounceSearch = function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => window.loadShipments(1), 350);
};

window.resetShipmentFilters = function () {
    document.getElementById('filter-search').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-origin').value = '';
    document.getElementById('filter-delayed').value = '';
    document.getElementById('filter-sort').value = 'created_at:desc';
    window.loadShipments(1);
};

window.loadShipments = async function (page = 1) {
    const search = document.getElementById('filter-search')?.value || '';
    const status = document.getElementById('filter-status')?.value || '';
    const originId = document.getElementById('filter-origin')?.value || '';
    const isDelayed = document.getElementById('filter-delayed')?.value || '';
    const sortVal = document.getElementById('filter-sort')?.value || 'created_at:desc';
    const [sortBy, sortDir] = sortVal.split(':');

    const params = new URLSearchParams({ page, per_page: 10, sort_by: sortBy, sort_dir: sortDir });
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (originId) params.append('origin_id', originId);
    if (isDelayed !== '') params.append('is_delayed', isDelayed);

    try {
        const res = await window.api(`/api/shipments?${params.toString()}`);
        const tbody = document.getElementById('shipments-tbody');
        const rows = res.data || [];

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-[#78716C]">Tidak ada kargo yang sesuai filter.</td></tr>';
        } else {
            tbody.innerHTML = rows.map((s) => `
                <tr class="hover:bg-[#E7E4DC]/50 transition">
                    <td class="py-3.5 px-4 font-mono font-semibold text-[#C2410C]">${s.shipment_number}</td>
                    <td class="py-3.5 px-4 text-[#1C1917] font-medium">${s.carrier ? s.carrier.name : '-'}</td>
                    <td class="py-3.5 px-4 text-[#57534E]">${s.origin ? s.origin.code : '?'} &rarr; ${s.destination ? s.destination.code : '?'}</td>
                    <td class="py-3.5 px-4">${window.statusBadge(s.current_status)}</td>
                    <td class="py-3.5 px-4">
                        ${s.is_delayed
                            ? `<span class="text-[#C2410C] font-semibold">Terlambat ${s.delay_hours}j</span> ${window.severityBadge(s.delay_severity)}`
                            : '<span class="text-[#166534] font-semibold">Tepat Waktu</span>'}
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        <button onclick="window.openShipmentDetail(${s.id})" class="px-3 py-1.5 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7] hover:bg-[#C2410C] hover:text-white hover:border-[#C2410C] text-[#1C1917] text-xs font-semibold transition">
                            Detail &amp; Lacak →
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        const meta = res.meta;
        document.getElementById('shipments-pagination').innerHTML = `
            <span>Halaman ${meta.current_page} dari ${meta.last_page} (Total: ${meta.total} kargo)</span>
            <div class="flex gap-2">
                <button ${meta.current_page <= 1 ? 'disabled' : ''} onclick="window.loadShipments(${meta.current_page - 1})" class="px-3 py-1 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7] disabled:opacity-40">Sebelumnya</button>
                <button ${meta.current_page >= meta.last_page ? 'disabled' : ''} onclick="window.loadShipments(${meta.current_page + 1})" class="px-3 py-1 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7] disabled:opacity-40">Berikutnya</button>
            </div>
        `;
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.openShipmentDetail = async function (id) {
    window.appState.activeShipmentId = id;
    try {
        const res = await window.api(`/api/shipments/${id}`);
        const s = res.data.shipment;
        const currentLoc = res.data.current_location;
        const thresholds = res.data.delay_thresholds || { low: 6, medium: 24 };

        document.getElementById('modal-shipment-number').textContent = s.shipment_number;
        document.getElementById('modal-status-badge').outerHTML = `<span id="modal-status-badge">${window.statusBadge(s.current_status)}</span>`;

        const delayBadge = document.getElementById('modal-delay-badge');
        delayBadge.textContent = s.is_delayed ? `TERLAMBAT ${s.delay_hours}J (${s.delay_severity})` : 'TEPAT WAKTU';
        delayBadge.className = s.is_delayed
            ? 'px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA]'
            : 'px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-[#DCFCE7] text-[#166534] border border-[#BBF7D0]';

        document.getElementById('modal-shipment-route').textContent = `${s.origin?.name || '-'} → ${s.destination?.name || '-'}`;
        document.getElementById('det-carrier').textContent = s.carrier?.name || '-';
        document.getElementById('det-cargo').textContent = s.cargo_type || '-';
        document.getElementById('det-weight-vol').textContent = `${s.weight || 0} kg / ${s.volume || 0} cbm`;
        document.getElementById('det-customer').textContent = s.customer?.name || '-';

        document.getElementById('det-thresholds-info').textContent = `LOW ≤ ${thresholds.low}j | MEDIUM ≤ ${thresholds.medium}j | HIGH > ${thresholds.medium}j`;
        document.getElementById('det-delay-reason').textContent = s.is_delayed
            ? (s.delay_reason || `Estimasi tiba melewati jadwal target sebesar ${s.delay_hours} jam.`)
            : 'Pengiriman berjalan sesuai jadwal target.';

        document.getElementById('det-current-location').textContent = currentLoc
            ? `${currentLoc.location_name} (${currentLoc.latitude}, ${currentLoc.longitude}) — ${currentLoc.status_type}`
            : 'Belum ada koordinat pelacakan.';

        const transitions = {
            CREATED: ['BOOKED', 'CANCELLED'],
            BOOKED: ['PICKED_UP', 'CANCELLED'],
            PICKED_UP: ['IN_TRANSIT', 'CANCELLED'],
            IN_TRANSIT: ['DELIVERED', 'CANCELLED'],
            DELIVERED: [],
            CANCELLED: [],
        };

        const role = window.appState.user?.role;
        const canEdit = role === 'admin' || role === 'operator';
        document.getElementById('lifecycle-controller-panel').classList.toggle('hidden', !canEdit);
        document.getElementById('btn-add-event-toggle').classList.toggle('hidden', !canEdit);
        document.getElementById('btn-report-exception-toggle').classList.toggle('hidden', !canEdit);
        document.getElementById('admin-shipment-danger-zone').classList.toggle('hidden', role !== 'admin');

        const nextStates = transitions[s.current_status] || [];
        document.getElementById('lifecycle-action-buttons').innerHTML = nextStates.length
            ? nextStates.map((st) => `
                <button onclick="window.transitionStatus('${st}')" class="px-3 py-1.5 rounded-lg text-xs font-semibold ${st === 'CANCELLED' ? 'bg-[#F9F8F6] text-[#991B1B] border border-[#FECACA]' : 'bg-[#C2410C] text-white hover:bg-[#9A3412]'} transition">
                    Ubah ke ${st}
                </button>
            `).join('')
            : '<span class="text-xs text-[#78716C] italic">Status terminal — terkunci permanen.</span>';

        const events = s.tracking_events || [];
        document.getElementById('timeline-list').innerHTML = events.length
            ? events.map((e) => `
                <div class="relative pl-4">
                    <div class="absolute -left-[9px] top-1.5 w-3 h-3 rounded-full ${e.status_type === 'DELIVERED' ? 'bg-[#15803D]' : 'bg-[#C2410C]'}"></div>
                    <div class="p-3 bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            ${window.statusBadge(e.status_type)}
                            <span class="text-[10px] font-mono text-[#78716C]">${new Date(e.event_time).toLocaleString('id-ID')}</span>
                        </div>
                        <p class="text-xs text-[#1C1917] font-medium">${e.description}</p>
                        <span class="text-[10px] text-[#57534E]">${e.location?.name || (e.latitude ? `${e.latitude}, ${e.longitude}` : '-')} • Oleh: ${e.creator?.name || '-'}</span>
                    </div>
                </div>
            `).join('')
            : '<p class="text-xs text-[#78716C]">Belum ada riwayat tracking event.</p>';

        const excs = s.exceptions || [];
        document.getElementById('exceptions-list').innerHTML = excs.length
            ? excs.map((ex) => `
                <div class="p-3 bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-[#C2410C]">${ex.exception_type}</span>
                        ${window.severityBadge(ex.severity)}
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#E7E4DC] text-[#57534E]">${ex.status}</span>
                    </div>
                    <p class="text-xs text-[#57534E]">${ex.notes || '-'}</p>
                </div>
            `).join('')
            : '<p class="text-xs text-[#78716C]">Tidak ada kendala yang dilaporkan.</p>';

        window.switchModalTab('tab-overview');
        document.getElementById('shipment-detail-modal').classList.remove('hidden');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.closeDetailModal = function () {
    document.getElementById('shipment-detail-modal').classList.add('hidden');
    document.getElementById('admin-override-form').classList.add('hidden');
};

window.switchModalTab = function (tabId) {
    ['tab-overview', 'tab-map', 'tab-timeline', 'tab-exceptions'].forEach((id) => {
        document.getElementById(id).classList.toggle('hidden', id !== tabId);
        const btn = document.getElementById(`btn-${id}`);
        if (btn) {
            btn.className = id === tabId
                ? 'py-3 border-b-2 border-[#C2410C] text-[#C2410C]'
                : 'py-3 border-b-2 border-transparent text-[#57534E] hover:text-[#1C1917]';
        }
    });

    if (tabId === 'tab-map' && window.appState.activeShipmentId) {
        setTimeout(() => window.renderDetailMap(window.appState.activeShipmentId), 150);
    }
};

window.transitionStatus = async function (newStatus) {
    try {
        const res = await window.api(`/api/shipments/${window.appState.activeShipmentId}/status`, {
            method: 'PATCH',
            body: JSON.stringify({ status: newStatus }),
        });
        window.toast(res.message, 'success');
        window.openShipmentDetail(window.appState.activeShipmentId);
        window.loadShipments();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.toggleOverrideBox = function () {
    document.getElementById('admin-override-form').classList.toggle('hidden');
};

window.submitOverrideStatus = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api(`/api/shipments/${window.appState.activeShipmentId}/override-status`, {
            method: 'POST',
            body: JSON.stringify({
                status: document.getElementById('override-status').value,
                reason: document.getElementById('override-reason').value,
            }),
        });
        window.toast(res.message, 'success');
        document.getElementById('override-reason').value = '';
        window.openShipmentDetail(window.appState.activeShipmentId);
        window.loadShipments();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.deleteActiveShipment = async function () {
    if (!confirm('Hapus kargo ini secara aman (Soft Delete)?')) return;
    try {
        const res = await window.api(`/api/shipments/${window.appState.activeShipmentId}`, { method: 'DELETE' });
        window.toast(res.message, 'success');
        window.closeDetailModal();
        window.loadShipments();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.openNewShipmentModal = async function () {
    if (!window.appState.carriers.length) await window.loadMasterReferences();

    document.getElementById('create-carrier').innerHTML = window.appState.carriers
        .map((c) => `<option value="${c.id}">${c.name} (${c.code})</option>`).join('');
    const locOptions = window.appState.locations
        .map((l) => `<option value="${l.id}">${l.name} (${l.code})</option>`).join('');
    document.getElementById('create-origin').innerHTML = locOptions;
    document.getElementById('create-dest').innerHTML = locOptions;
    if (window.appState.locations.length > 1) {
        document.getElementById('create-dest').selectedIndex = 1;
    }

    document.getElementById('new-shipment-modal').classList.remove('hidden');
};

window.closeNewShipmentModal = function () {
    document.getElementById('new-shipment-modal').classList.add('hidden');
};

window.handleCreateShipment = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api('/api/shipments', {
            method: 'POST',
            body: JSON.stringify({
                carrier_id: document.getElementById('create-carrier').value,
                cargo_type: document.getElementById('create-cargo').value,
                origin_id: document.getElementById('create-origin').value,
                destination_id: document.getElementById('create-dest').value,
                weight: document.getElementById('create-weight').value || null,
                volume: document.getElementById('create-volume').value || null,
                expected_departure: document.getElementById('create-dep').value || null,
                expected_arrival: document.getElementById('create-arr').value || null,
            }),
        });
        window.toast(res.message, 'success');
        window.closeNewShipmentModal();
        window.loadShipments();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.toggleAddEventForm = function () {
    document.getElementById('form-record-event').classList.toggle('hidden');
};

window.submitTrackingEvent = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api(`/api/shipments/${window.appState.activeShipmentId}/tracking`, {
            method: 'POST',
            body: JSON.stringify({
                status_type: document.getElementById('evt-status-type').value,
                latitude: document.getElementById('evt-lat').value || null,
                longitude: document.getElementById('evt-lng').value || null,
                description: document.getElementById('evt-desc').value,
            }),
        });
        window.toast(res.message || 'Milestone berhasil dicatat.', 'success');
        document.getElementById('evt-desc').value = '';
        window.toggleAddEventForm();
        window.openShipmentDetail(window.appState.activeShipmentId);
        window.loadShipments();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.toggleReportExceptionForm = function () {
    document.getElementById('form-report-exception').classList.toggle('hidden');
};

window.submitException = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api(`/api/shipments/${window.appState.activeShipmentId}/exceptions`, {
            method: 'POST',
            body: JSON.stringify({
                exception_type: document.getElementById('exc-type').value,
                severity: document.getElementById('exc-severity').value,
                notes: document.getElementById('exc-notes').value,
            }),
        });
        window.toast(res.message || 'Kendala berhasil dilaporkan.', 'success');
        document.getElementById('exc-notes').value = '';
        window.toggleReportExceptionForm();
        window.openShipmentDetail(window.appState.activeShipmentId);
    } catch (err) {
        window.toast(err.message, 'error');
    }
};
