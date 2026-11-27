/**
 * CargoTrace — Executive Dashboard & Chart.js Analytics (Warm Alabaster & Terracotta)
 */

window.loadDashboardData = async function () {
    try {
        const [summaryRes, statusRes, carrierRes, trendRes] = await Promise.all([
            window.api('/api/dashboard'),
            window.api('/api/dashboard/shipments-by-status'),
            window.api('/api/dashboard/shipments-by-carrier'),
            window.api('/api/dashboard/delay-trend'),
        ]);

        const stats = summaryRes.data.stats;
        document.getElementById('kpi-total').textContent = stats.total_shipments;
        document.getElementById('kpi-intransit').textContent = stats.in_transit;
        document.getElementById('kpi-ontime').textContent = `${stats.on_time_ratio}%`;
        document.getElementById('kpi-delayed-sub').textContent = `${stats.delayed} kargo terlambat (${stats.delayed_ratio}%)`;
        document.getElementById('kpi-exceptions').textContent = stats.open_exceptions;

        window.renderChart('chart-status', 'doughnut', {
            labels: statusRes.data.labels,
            datasets: [{
                data: statusRes.data.data,
                backgroundColor: ['#A8A29E', '#78716C', '#D97706', '#C2410C', '#15803D', '#991B1B'],
                borderColor: '#F0EEE9',
                borderWidth: 2,
            }],
        });

        window.renderChart('chart-carrier', 'bar', {
            labels: carrierRes.data.labels,
            datasets: [{
                label: 'Total Kargo',
                data: carrierRes.data.data,
                backgroundColor: '#C2410C',
                borderRadius: 4,
            }],
        });

        window.renderChart('chart-trend', 'line', {
            labels: trendRes.data.labels,
            datasets: [
                { label: 'Tepat Waktu', data: trendRes.data.on_time, borderColor: '#15803D', tension: 0.3 },
                { label: 'Terlambat', data: trendRes.data.delayed, borderColor: '#C2410C', tension: 0.3 },
            ],
        });

        const tbody = document.getElementById('recent-activity-tbody');
        const activities = summaryRes.data.recent_activity || [];

        if (!activities.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-[#78716C]">Belum ada aktivitas pelacakan.</td></tr>';
            return;
        }

        tbody.innerHTML = activities.map((evt) => {
            const lat = evt.latitude ?? evt.location?.latitude;
            const lng = evt.longitude ?? evt.location?.longitude;
            const coordsText = (lat !== null && lat !== undefined && lng !== null && lng !== undefined)
                ? `${parseFloat(lat).toFixed(4)}, ${parseFloat(lng).toFixed(4)}`
                : 'Koordinat tidak tersedia';
            const routeFallback = (evt.shipment?.origin?.code && evt.shipment?.destination?.code)
                ? `Koridor Maritim (${evt.shipment.origin.code} → ${evt.shipment.destination.code})`
                : 'Titik Transit Maritim';
            const locName = evt.location ? evt.location.name : routeFallback;

            return `
            <tr class="hover:bg-[#E7E4DC]/50 transition">
                <td class="py-3.5 px-4 text-[#57534E] font-mono whitespace-nowrap align-middle">${new Date(evt.event_time).toLocaleString('id-ID')}</td>
                <td class="py-3.5 px-4 font-mono font-semibold text-[#C2410C] whitespace-nowrap align-middle">${evt.shipment ? evt.shipment.shipment_number : '-'}</td>
                <td class="py-3.5 px-4 whitespace-nowrap align-middle">${window.statusBadge(evt.status_type)}</td>
                <td class="py-3.5 px-4 whitespace-nowrap align-middle">
                    <div class="font-medium text-[#1C1917]">${locName}</div>
                    <div class="text-[11px] font-mono text-[#78716C]">${coordsText}</div>
                </td>
                <td class="py-3.5 px-4 text-[#57534E] align-middle min-w-[220px]">${evt.description}</td>
                <td class="py-3.5 px-4 text-[#57534E] whitespace-nowrap align-middle">${evt.creator ? evt.creator.name : '-'}</td>
            </tr>`;
        }).join('');
    } catch (err) {
        window.toast(err.message, 'error');
    }
};

window.renderChart = function (canvasId, type, data) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    if (window.appState.charts[canvasId]) {
        window.appState.charts[canvasId].destroy();
    }

    window.appState.charts[canvasId] = new Chart(canvas, {
        type,
        data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#57534E', font: { size: 11 } } },
            },
            scales: type === 'doughnut' ? {} : {
                x: { ticks: { color: '#78716C', font: { size: 10 } }, grid: { color: '#E2DFD7' } },
                y: { ticks: { color: '#78716C', font: { size: 10 } }, grid: { color: '#E2DFD7' }, beginAtZero: true },
            },
        },
    });
};
