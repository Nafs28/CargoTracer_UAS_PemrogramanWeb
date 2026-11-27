/**
 * CargoTrace — Admin & Operations Modules (Warm Alabaster & Terracotta)
 */

window.loadExceptions = async function () {
    try {
        const res = await window.api('/api/exceptions');
        const rows = res.data || [];
        const tbody = document.getElementById('exceptions-tbody');
        const canResolve = ['admin', 'operator'].includes(window.appState.user?.role);

        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-[#78716C]">Tidak ada kendala aktif.</td></tr>';
            return;
        }

        tbody.innerHTML = rows.map((ex) => `
            <tr class="hover:bg-[#E7E4DC]/50 transition">
                <td class="py-3 px-4 font-mono font-semibold text-[#C2410C]">${ex.shipment ? ex.shipment.shipment_number : '-'}</td>
                <td class="py-3 px-4 font-semibold text-[#1C1917]">${ex.exception_type}</td>
                <td class="py-3 px-4">${window.severityBadge(ex.severity)}</td>
                <td class="py-3 px-4"><span class="px-2 py-0.5 rounded bg-[#E7E4DC] text-[#57534E] text-[10px] font-bold">${ex.status}</span></td>
                <td class="py-3 px-4 text-[#57534E] max-w-xs truncate">${ex.notes || '-'}</td>
                <td class="py-3 px-4 text-right space-x-1">
                    ${canResolve && ex.status === 'OPEN' ? `<button onclick="window.updateExceptionStatus(${ex.id}, 'INVESTIGATING')" class="px-2.5 py-1 rounded bg-[#F9F8F6] border border-[#E2DFD7] text-[#1C1917] text-xs font-semibold">Investigasi</button>` : ''}
                    ${canResolve && ex.status !== 'RESOLVED' ? `<button onclick="window.updateExceptionStatus(${ex.id}, 'RESOLVED')" class="px-2.5 py-1 rounded bg-[#C2410C] text-white text-xs font-semibold">Selesaikan</button>` : ''}
                </td>
            </tr>
        `).join('');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.updateExceptionStatus = async function (id, status) {
    const notes = prompt(`Catatan untuk status ${status}:`, `Diperbarui menjadi ${status}`);
    if (notes === null) return;
    try {
        const res = await window.api(`/api/exceptions/${id}`, {
            method: 'PATCH',
            body: JSON.stringify({ status, notes }),
        });
        window.toast(res.message || 'Status kendala diperbarui.', 'success');
        window.loadExceptions();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.loadCarriers = async function () {
    try {
        const res = await window.api('/api/carriers');
        const isAdmin = window.appState.user?.role === 'admin';
        document.getElementById('carriers-tbody').innerHTML = (res.data || []).map((c) => `
            <tr class="hover:bg-[#E7E4DC]/50 transition">
                <td class="py-3 px-4 font-semibold text-[#1C1917]">${c.name}</td>
                <td class="py-3 px-4 font-mono text-[#C2410C]">${c.code}</td>
                <td class="py-3 px-4 text-[#57534E]">${c.contact_email || '-'}</td>
                <td class="py-3 px-4 text-[#57534E]">${c.shipments_count ?? 0}</td>
                <td class="py-3 px-4 text-right">
                    <span class="px-2 py-0.5 rounded bg-[#DCFCE7] text-[#166534] text-[10px] font-bold">AKTIF</span>
                    ${isAdmin ? `<button onclick="window.deleteCarrier(${c.id}, '${c.name.replace(/'/g, "\\'")}')" class="ml-2 text-[#C2410C] hover:underline text-xs font-semibold">Hapus</button>` : ''}
                </td>
            </tr>
        `).join('');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.openNewCarrierModal = () => document.getElementById('new-carrier-modal').classList.remove('hidden');
window.closeNewCarrierModal = () => document.getElementById('new-carrier-modal').classList.add('hidden');

window.handleCreateCarrier = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api('/api/carriers', {
            method: 'POST',
            body: JSON.stringify({
                name: document.getElementById('carrier-name').value,
                code: document.getElementById('carrier-code').value.toUpperCase(),
                contact_email: document.getElementById('carrier-email').value || null,
                contact_phone: document.getElementById('carrier-phone').value || null,
                address: document.getElementById('carrier-address').value || null,
            }),
        });
        window.toast(`Carrier ${res.data.name} berhasil ditambahkan.`, 'success');
        window.closeNewCarrierModal();
        window.loadCarriers();
        window.loadMasterReferences();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.deleteCarrier = async function (id, name) {
    if (!confirm(`Hapus carrier ${name}?`)) return;
    try {
        await window.api(`/api/carriers/${id}`, { method: 'DELETE' });
        window.toast(`Carrier ${name} dihapus.`, 'success');
        window.loadCarriers();
        window.loadMasterReferences();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.loadLocations = async function () {
    try {
        const res = await window.api('/api/locations');
        const isAdmin = window.appState.user?.role === 'admin';
        document.getElementById('locations-tbody').innerHTML = (res.data || []).map((loc) => `
            <tr class="hover:bg-[#E7E4DC]/50 transition">
                <td class="py-3 px-4 font-semibold text-[#1C1917]">${loc.name}</td>
                <td class="py-3 px-4 font-mono text-[#C2410C]">${loc.code}</td>
                <td class="py-3 px-4 uppercase text-[#78716C] text-[10px]">${loc.type || 'port'}</td>
                <td class="py-3 px-4 text-[#57534E]">${loc.city || '-'}, ${loc.country || '-'}</td>
                <td class="py-3 px-4 font-mono text-[11px] text-[#78716C]">${loc.latitude}, ${loc.longitude}</td>
                <td class="py-3 px-4 text-right space-x-2">
                    <button onclick="window.checkLiveWeather(${loc.latitude}, ${loc.longitude}, '${loc.name.replace(/'/g, "\\'")}')" class="px-2.5 py-1 rounded bg-[#F9F8F6] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-[#1C1917] text-xs font-semibold">Cek Cuaca</button>
                    ${isAdmin ? `<button onclick="window.deleteLocation(${loc.id}, '${loc.name.replace(/'/g, "\\'")}')" class="text-[#C2410C] hover:underline text-xs font-semibold">Hapus</button>` : ''}
                </td>
            </tr>
        `).join('');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.openNewLocationModal = () => document.getElementById('new-location-modal').classList.remove('hidden');
window.closeNewLocationModal = () => document.getElementById('new-location-modal').classList.add('hidden');

window.autoGeocodeLocation = async function () {
    const query = [document.getElementById('loc-name').value, document.getElementById('loc-city').value, document.getElementById('loc-country').value].filter(Boolean).join(', ');
    if (!query) return window.toast('Isi nama pelabuhan atau kota terlebih dahulu.', 'warning');

    try {
        const res = await window.api(`/api/geocode?q=${encodeURIComponent(query)}`);
        if (res.data?.latitude) {
            document.getElementById('loc-lat').value = res.data.latitude;
            document.getElementById('loc-lng').value = res.data.longitude;
            window.toast(`Koordinat ditemukan: ${res.data.latitude}, ${res.data.longitude}`, 'success');
        } else {
            window.toast('Koordinat tidak ditemukan.', 'warning');
        }
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.handleCreateLocation = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api('/api/locations', {
            method: 'POST',
            body: JSON.stringify({
                name: document.getElementById('loc-name').value,
                code: document.getElementById('loc-code').value.toUpperCase(),
                type: document.getElementById('loc-type').value,
                city: document.getElementById('loc-city').value || null,
                country: document.getElementById('loc-country').value || null,
                latitude: parseFloat(document.getElementById('loc-lat').value),
                longitude: parseFloat(document.getElementById('loc-lng').value),
            }),
        });
        window.toast(`Lokasi ${res.data.name} berhasil ditambahkan.`, 'success');
        window.closeNewLocationModal();
        window.loadLocations();
        window.loadMasterReferences();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.deleteLocation = async function (id, name) {
    if (!confirm(`Hapus lokasi ${name}?`)) return;
    try {
        await window.api(`/api/locations/${id}`, { method: 'DELETE' });
        window.toast(`Lokasi ${name} dihapus.`, 'success');
        window.loadLocations();
        window.loadMasterReferences();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.checkLiveWeather = async function (lat, lon, name) {
    try {
        const res = await window.api(`/api/weather?lat=${lat}&lon=${lon}`);
        const w = res.data;
        window.toast(`${name}: ${w.condition}, Suhu ${w.temperature}°C, Angin ${w.wind_speed} km/jam`, 'info');
    } catch {
        window.toast('Gagal memuat data cuaca.', 'error');
    }
};

window.loadUsers = async function () {
    try {
        const res = await window.api('/api/users');
        document.getElementById('users-tbody').innerHTML = (res.data || []).map((u) => `
            <tr class="hover:bg-[#E7E4DC]/50 transition">
                <td class="py-3 px-4 font-semibold text-[#1C1917]">${u.name}</td>
                <td class="py-3 px-4 text-[#57534E]">${u.email}</td>
                <td class="py-3 px-4 uppercase text-[10px] font-bold text-[#C2410C]">${u.role}</td>
                <td class="py-3 px-4 text-[#78716C] font-mono">${u.phone ? u.phone.replace(/^\+?62/, '0') : '-'}</td>
                <td class="py-3 px-4 text-right">
                    <button onclick="window.deleteUser(${u.id}, '${u.name.replace(/'/g, "\\'")}')" class="text-[#C2410C] hover:underline text-xs font-semibold">Hapus</button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.openNewUserModal = () => document.getElementById('new-user-modal').classList.remove('hidden');
window.closeNewUserModal = () => document.getElementById('new-user-modal').classList.add('hidden');

window.handleCreateUser = async function (e) {
    e.preventDefault();
    try {
        const res = await window.api('/api/users', {
            method: 'POST',
            body: JSON.stringify({
                name: document.getElementById('usr-name').value,
                email: document.getElementById('usr-email').value,
                role: document.getElementById('usr-role').value,
                password: document.getElementById('usr-password').value,
            }),
        });
        window.toast(res.message, 'success');
        window.closeNewUserModal();
        window.loadUsers();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.deleteUser = async function (id, name) {
    if (!confirm(`Hapus pengguna ${name}?`)) return;
    try {
        const res = await window.api(`/api/users/${id}`, { method: 'DELETE' });
        window.toast(res.message, 'success');
        window.loadUsers();
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.loadAuditLogs = async function () {
    try {
        const res = await window.api('/api/audit-logs');
        document.getElementById('audit-tbody').innerHTML = (res.data || []).map((log) => `
            <tr class="hover:bg-[#E7E4DC]/50 transition">
                <td class="py-3 px-4 text-[#78716C]">${new Date(log.created_at).toLocaleString('id-ID')}</td>
                <td class="py-3 px-4 font-bold text-[#C2410C]">${log.action}</td>
                <td class="py-3 px-4 text-[#1C1917]">${log.user ? log.user.name : 'System'}</td>
                <td class="py-3 px-4 text-[#57534E]">${log.auditable_type ? log.auditable_type.split('\\').pop() : '-'} #${log.auditable_id || '-'}</td>
                <td class="py-3 px-4 text-[#78716C]">${log.ip_address || '-'}</td>
            </tr>
        `).join('');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.loadNotifications = async function () {
    try {
        const res = await window.api('/api/notifications');
        const badge = document.getElementById('notif-badge');
        const list = document.getElementById('notifications-list');
        const unread = res.unread_count || 0;

        badge.textContent = unread;
        badge.classList.toggle('hidden', unread === 0);

        const items = res.data || [];
        list.innerHTML = items.length
            ? items.map((n) => `
                <div onclick="window.markNotificationRead(${n.id})" class="p-2 rounded border cursor-pointer ${n.is_read ? 'border-[#E2DFD7] opacity-60' : 'border-[#C2410C] bg-[#F0EEE9]'}">
                    <p class="font-semibold text-[#1C1917]">${n.title}</p>
                    <p class="text-[#57534E]">${n.message}</p>
                </div>
            `).join('')
            : '<p class="text-center py-2 text-[#78716C]">Belum ada notifikasi.</p>';
    } catch {
        // Silent fail
    }
};

window.toggleNotificationsPanel = function () {
    document.getElementById('notifications-panel').classList.toggle('hidden');
    window.loadNotifications();
};

window.markNotificationRead = async function (id) {
    await window.api(`/api/notifications/${id}/read`, { method: 'PATCH' });
    window.loadNotifications();
};

window.markAllNotificationsRead = async function () {
    await window.api('/api/notifications/read-all', { method: 'PATCH' });
    window.loadNotifications();
};
