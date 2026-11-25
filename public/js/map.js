/**
 * CargoTrace — Interactive GIS Map (Leaflet.js + OpenStreetMap)
 */

window.loadGlobalMap = async function () {
    const container = document.getElementById('global-leaflet-map');
    if (!container) return;

    if (window.appState.globalMap) {
        window.appState.globalMap.remove();
    }

    const map = L.map('global-leaflet-map').setView([5.0, 100.0], 3);
    window.appState.globalMap = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    try {
        const res = await window.api('/api/shipments?per_page=20');
        const shipments = res.data || [];

        for (const s of shipments) {
            if (!s.origin?.latitude || !s.destination?.latitude) continue;

            const originLatLng = [parseFloat(s.origin.latitude), parseFloat(s.origin.longitude)];
            const destLatLng = [parseFloat(s.destination.latitude), parseFloat(s.destination.longitude)];

            L.circleMarker(originLatLng, { radius: 6, color: '#3b82f6', fillOpacity: 0.9 })
                .bindPopup(`<b>Asal:</b> ${s.origin.name}<br>Kargo: ${s.shipment_number}`)
                .addTo(map);

            L.circleMarker(destLatLng, { radius: 6, color: '#10b981', fillOpacity: 0.9 })
                .bindPopup(`<b>Tujuan:</b> ${s.destination.name}<br>Kargo: ${s.shipment_number}`)
                .addTo(map);

            L.polyline([originLatLng, destLatLng], {
                color: s.is_delayed ? '#f43f5e' : '#38bdf8',
                weight: 2,
                dashArray: s.current_status === 'DELIVERED' ? null : '6, 6',
            }).addTo(map);
        }

        setTimeout(() => map.invalidateSize(), 200);
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.renderDetailMap = async function (shipmentId) {
    const container = document.getElementById('detail-leaflet-map');
    if (!container) return;

    if (window.appState.detailMap) {
        window.appState.detailMap.remove();
    }

    const map = L.map('detail-leaflet-map').setView([0, 100], 3);
    window.appState.detailMap = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    try {
        const res = await window.api(`/api/shipments/${shipmentId}/map`);
        const d = res.data;
        const points = [];

        if (d.origin?.latitude) {
            const p = [d.origin.latitude, d.origin.longitude];
            points.push(p);
            L.marker(p).bindPopup(`<b>Origin:</b> ${d.origin.name}`).addTo(map);
        }

        (d.waypoints || []).forEach((w) => {
            const p = [w.latitude, w.longitude];
            points.push(p);
            L.circleMarker(p, { radius: 7, color: '#f59e0b', fillOpacity: 0.9 })
                .bindPopup(`<b>${w.status_type}</b><br>${w.description}<br><small>${new Date(w.event_time).toLocaleString('id-ID')}</small>`)
                .addTo(map);
        });

        if (d.destination?.latitude) {
            const p = [d.destination.latitude, d.destination.longitude];
            points.push(p);
            L.marker(p).bindPopup(`<b>Destination:</b> ${d.destination.name}`).addTo(map);
        }

        if (points.length >= 2) {
            L.polyline(points, { color: d.is_delayed ? '#f43f5e' : '#3b82f6', weight: 3 }).addTo(map);
            map.fitBounds(points, { padding: [30, 30] });
        }

        setTimeout(() => map.invalidateSize(), 200);
    } catch (err) {
        window.toast(err.message, 'error');
    }
};
