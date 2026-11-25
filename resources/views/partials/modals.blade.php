<!-- MODAL DETAIL & TIMELINE KARGO -->
<div id="shipment-detail-modal" class="fixed inset-0 z-50 bg-[#1C1917]/50 flex items-center justify-center p-4 hidden">
    <div class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Header Modal -->
        <div class="p-6 border-b border-[#E2DFD7] flex items-center justify-between shrink-0 bg-[#F0EEE9]">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 id="modal-shipment-number" class="text-lg font-bold text-[#1C1917] font-mono">CT-000000</h3>
                    <span id="modal-status-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">STATUS</span>
                    <span id="modal-delay-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">TEPAT WAKTU</span>
                </div>
                <p id="modal-shipment-route" class="text-xs text-[#57534E] mt-1">Asal &rarr; Tujuan</p>
            </div>
            <button onclick="window.closeDetailModal()" class="w-8 h-8 rounded-lg bg-[#F9F8F6] border border-[#E2DFD7] hover:bg-[#E7E4DC] text-[#57534E] hover:text-[#1C1917] flex items-center justify-center text-sm transition">✕</button>
        </div>

        <!-- Tab Navigasi Modal -->
        <div class="px-6 border-b border-[#E2DFD7] flex gap-6 text-xs font-semibold bg-[#F0EEE9]/50 shrink-0">
            <button onclick="window.switchModalTab('tab-overview')" id="btn-tab-overview" class="py-3 border-b-2 border-[#C2410C] text-[#C2410C]">Ringkasan</button>
            <button onclick="window.switchModalTab('tab-map')" id="btn-tab-map" class="py-3 border-b-2 border-transparent text-[#57534E] hover:text-[#1C1917]">Peta Rute</button>
            <button onclick="window.switchModalTab('tab-timeline')" id="btn-tab-timeline" class="py-3 border-b-2 border-transparent text-[#57534E] hover:text-[#1C1917]">Riwayat Pelacakan</button>
            <button onclick="window.switchModalTab('tab-exceptions')" id="btn-tab-exceptions" class="py-3 border-b-2 border-transparent text-[#57534E] hover:text-[#1C1917]">Kendala Kargo</button>
        </div>

        <!-- Konten Modal -->
        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-6">
            <!-- TAB 1: RINGKASAN -->
            <div id="tab-overview" class="space-y-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="p-3.5 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7]">
                        <span class="text-[10px] uppercase font-semibold text-[#78716C] block">Armada Carrier</span>
                        <p id="det-carrier" class="text-xs font-semibold text-[#1C1917] mt-1">-</p>
                    </div>
                    <div class="p-3.5 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7]">
                        <span class="text-[10px] uppercase font-semibold text-[#78716C] block">Jenis Kargo</span>
                        <p id="det-cargo" class="text-xs font-semibold text-[#1C1917] mt-1">-</p>
                    </div>
                    <div class="p-3.5 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7]">
                        <span class="text-[10px] uppercase font-semibold text-[#78716C] block">Berat &amp; Volume</span>
                        <p id="det-weight-vol" class="text-xs font-semibold text-[#1C1917] mt-1">-</p>
                    </div>
                    <div class="p-3.5 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7]">
                        <span class="text-[10px] uppercase font-semibold text-[#78716C] block">Pelanggan</span>
                        <p id="det-customer" class="text-xs font-semibold text-[#1C1917] mt-1">-</p>
                    </div>
                </div>

                <!-- Transparansi Keterlambatan & Threshold -->
                <div id="det-delay-box" class="p-4 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7] space-y-1">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="text-xs font-semibold text-[#1C1917]">Analisis Keterlambatan &amp; Threshold SLA</span>
                        <span id="det-thresholds-info" class="text-[11px] font-mono text-[#57534E]">LOW &le; 6j | MEDIUM &le; 24j | HIGH &gt; 24j</span>
                    </div>
                    <p id="det-delay-reason" class="text-xs text-[#57534E]">Pengiriman berjalan sesuai jadwal target.</p>
                </div>

                <!-- Panel Transisi State Machine -->
                <div id="lifecycle-controller-panel" class="p-4 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7]">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-[#1C1917]">Transisi Status Berurutan (Lifecycle Guard)</span>
                        <span class="text-[11px] text-[#78716C]">Aturan bisnis mencegah lompatan status ilegal</span>
                    </div>
                    <div id="lifecycle-action-buttons" class="flex flex-wrap gap-2"></div>
                </div>

                <!-- Panel Khusus Administrator (Override & Soft Delete) -->
                <div id="admin-shipment-danger-zone" class="p-4 bg-[#F0EEE9] rounded-lg border border-[#C2410C]/40 space-y-3 hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-semibold text-[#C2410C] block">Tindakan Administrator</span>
                            <span class="text-[11px] text-[#57534E]">Override status darurat (tercatat di Audit Log) atau hapus kargo (Soft Delete).</span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="window.toggleOverrideBox()" class="px-3 py-1.5 bg-[#F9F8F6] hover:bg-[#E7E4DC] text-[#1C1917] border border-[#E2DFD7] text-xs font-semibold rounded-lg transition">
                                Override Status
                            </button>
                            <button onclick="window.deleteActiveShipment()" class="px-3 py-1.5 bg-[#C2410C] hover:bg-[#9A3412] text-white text-xs font-semibold rounded-lg transition">
                                Hapus Kargo
                            </button>
                        </div>
                    </div>
                    <form id="admin-override-form" onsubmit="window.submitOverrideStatus(event)" class="hidden pt-3 border-t border-[#E2DFD7] grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <select id="override-status" class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                            <option value="IN_TRANSIT">IN_TRANSIT</option>
                            <option value="PICKED_UP">PICKED_UP</option>
                            <option value="BOOKED">BOOKED</option>
                            <option value="CREATED">CREATED</option>
                            <option value="DELIVERED">DELIVERED</option>
                            <option value="CANCELLED">CANCELLED</option>
                        </select>
                        <input type="text" id="override-reason" required minlength="5" placeholder="Alasan override wajib diisi..." class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                        <button type="submit" class="px-3 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white">Konfirmasi Override</button>
                    </form>
                </div>
            </div>

            <!-- TAB 2: PETA RUTE -->
            <div id="tab-map" class="space-y-4 hidden">
                <div id="detail-leaflet-map" style="height: 380px; width: 100%;" class="rounded-lg border border-[#E2DFD7]"></div>
            </div>

            <!-- TAB 3: RIWAYAT PELACAKAN -->
            <div id="tab-timeline" class="space-y-5 hidden">
                <div class="p-3.5 bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg">
                    <span class="text-[10px] uppercase font-semibold text-[#C2410C] block">Lokasi Terkini (Current Location)</span>
                    <p id="det-current-location" class="text-xs font-semibold text-[#1C1917] mt-0.5">-</p>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#1C1917]">Kronologi Riwayat Perjalanan (Append-Only)</span>
                    <button id="btn-add-event-toggle" onclick="window.toggleAddEventForm()" class="px-3 py-1.5 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-xs font-semibold transition">
                        + Catat Milestone Baru
                    </button>
                </div>

                <form id="form-record-event" onsubmit="window.submitTrackingEvent(event)" class="p-4 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7] space-y-3 hidden">
                    <h4 class="text-xs font-semibold text-[#1C1917]">Tambah Titik Koordinat / Milestone</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Status / Jenis Kejadian</label>
                            <select id="evt-status-type" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                                <option value="IN_TRANSIT">IN_TRANSIT</option>
                                <option value="PORT_ARRIVAL">PORT_ARRIVAL</option>
                                <option value="CUSTOMS_CLEARANCE">CUSTOMS_CLEARANCE</option>
                                <option value="DELIVERED">DELIVERED</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Lintang (Latitude)</label>
                            <input type="number" step="0.0001" id="evt-lat" placeholder="-6.1018" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Bujur (Longitude)</label>
                            <input type="number" step="0.0001" id="evt-lng" placeholder="106.8833" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Deskripsi Kejadian</label>
                        <input type="text" id="evt-desc" required placeholder="Kapal lego jangkar di Selat Singapura..." class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="window.toggleAddEventForm()" class="px-3 py-1.5 text-xs text-[#57534E] hover:text-[#1C1917]">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-xs font-semibold">Simpan Milestone</button>
                    </div>
                </form>

                <div id="timeline-list" class="space-y-4 pl-4 border-l-2 border-[#E2DFD7]"></div>
            </div>

            <!-- TAB 4: KENDALA KARGO -->
            <div id="tab-exceptions" class="space-y-4 hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#1C1917]">Laporan Kendala Kargo</span>
                    <button id="btn-report-exception-toggle" onclick="window.toggleReportExceptionForm()" class="px-3 py-1.5 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-xs font-semibold transition">
                        + Laporkan Kendala
                    </button>
                </div>

                <form id="form-report-exception" onsubmit="window.submitException(event)" class="p-4 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7] space-y-3 hidden">
                    <h4 class="text-xs font-semibold text-[#C2410C]">Lapor Gangguan Operasional Baru</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Jenis Kendala</label>
                            <select id="exc-type" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                                <option value="CUSTOMS_HOLD">CUSTOMS_HOLD</option>
                                <option value="WEATHER_DELAY">WEATHER_DELAY</option>
                                <option value="PORT_CONGESTION">PORT_CONGESTION</option>
                                <option value="DAMAGED">DAMAGED</option>
                                <option value="MISROUTED">MISROUTED</option>
                                <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Tingkat Keparahan</label>
                            <select id="exc-severity" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                                <option value="LOW">LOW</option>
                                <option value="MEDIUM">MEDIUM</option>
                                <option value="HIGH">HIGH</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-[#57534E] mb-1">Catatan Detail Kendala</label>
                        <textarea id="exc-notes" rows="2" required placeholder="Proses clearance terhambat..." class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="window.toggleReportExceptionForm()" class="px-3 py-1.5 text-xs text-[#57534E] hover:text-[#1C1917]">Batal</button>
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-white text-xs font-semibold">Kirim Laporan</button>
                    </div>
                </form>

                <div id="exceptions-list" class="space-y-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BUAT ORDER KARGO BARU -->
<div id="new-shipment-modal" class="fixed inset-0 z-50 bg-[#1C1917]/50 flex items-center justify-center p-4 hidden">
    <div class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-xl w-full max-w-xl p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#E2DFD7] mb-4">
            <h3 class="font-serif-editorial text-xl font-semibold text-[#1C1917]">Buat Order Pengiriman Kargo Baru</h3>
            <button onclick="window.closeNewShipmentModal()" class="text-[#57534E] hover:text-[#1C1917]">✕</button>
        </div>
        <form onsubmit="window.handleCreateShipment(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Armada Carrier</label>
                    <select id="create-carrier" required class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]"></select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Jenis Kargo / Komoditas</label>
                    <input type="text" id="create-cargo" required placeholder="Elektronik, Farmasi..." class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Pelabuhan Asal</label>
                    <select id="create-origin" required class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]"></select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Pelabuhan Tujuan</label>
                    <select id="create-dest" required class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]"></select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Berat Total (KG)</label>
                    <input type="number" step="0.01" id="create-weight" placeholder="12500" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Volume (CBM)</label>
                    <input type="number" step="0.01" id="create-volume" placeholder="45.5" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Jadwal Keberangkatan</label>
                    <input type="date" id="create-dep" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Jadwal Tiba (Expected)</label>
                    <input type="date" id="create-arr" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
            </div>
            <div class="pt-4 border-t border-[#E2DFD7] flex justify-end gap-2">
                <button type="button" onclick="window.closeNewShipmentModal()" class="px-4 py-2 text-xs text-[#57534E] hover:text-[#1C1917]">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white">Simpan Order Kargo</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH ARMADA CARRIER BARU -->
<div id="new-carrier-modal" class="fixed inset-0 z-50 bg-[#1C1917]/50 flex items-center justify-center p-4 hidden">
    <div class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#E2DFD7] mb-4">
            <h3 class="font-serif-editorial text-xl font-semibold text-[#1C1917]">Daftarkan Armada Carrier Baru</h3>
            <button onclick="window.closeNewCarrierModal()" class="text-[#57534E] hover:text-[#1C1917]">✕</button>
        </div>
        <form onsubmit="window.handleCreateCarrier(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Nama Perusahaan</label>
                    <input type="text" id="carrier-name" required placeholder="Maersk Line" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Kode SCAC (Unik)</label>
                    <input type="text" id="carrier-code" required placeholder="MAEU" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917] uppercase font-mono">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Email Kontak</label>
                    <input type="email" id="carrier-email" placeholder="ops@carrier.com" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Telepon Operasional</label>
                    <input type="text" id="carrier-phone" placeholder="+62811..." class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
            </div>
            <div>
                <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Alamat Kantor Pusat</label>
                <textarea id="carrier-address" rows="2" placeholder="Alamat kantor..." class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]"></textarea>
            </div>
            <div class="pt-4 border-t border-[#E2DFD7] flex justify-end gap-2">
                <button type="button" onclick="window.closeNewCarrierModal()" class="px-4 py-2 text-xs text-[#57534E] hover:text-[#1C1917]">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white">Simpan Carrier</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH PELABUHAN / HUB LOKASI BARU -->
<div id="new-location-modal" class="fixed inset-0 z-50 bg-[#1C1917]/50 flex items-center justify-center p-4 hidden">
    <div class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#E2DFD7] mb-4">
            <h3 class="font-serif-editorial text-xl font-semibold text-[#1C1917]">Tambah Pelabuhan / Hub Logistik</h3>
            <button onclick="window.closeNewLocationModal()" class="text-[#57534E] hover:text-[#1C1917]">✕</button>
        </div>
        <form onsubmit="window.handleCreateLocation(event)" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Nama Pelabuhan / Hub</label>
                    <input type="text" id="loc-name" required placeholder="Tanjung Priok" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Kode LOCODE (Unik)</label>
                    <input type="text" id="loc-code" required placeholder="IDJKT" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917] uppercase font-mono">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Jenis Lokasi</label>
                    <select id="loc-type" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                        <option value="port">Port</option>
                        <option value="hub">Hub</option>
                        <option value="warehouse">Warehouse</option>
                        <option value="customs">Customs</option>
                        <option value="airport">Airport</option>
                        <option value="depot">Depot</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Kota</label>
                    <input type="text" id="loc-city" placeholder="Jakarta" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Negara</label>
                    <input type="text" id="loc-country" placeholder="Indonesia" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
            </div>
            <div class="p-3 bg-[#F0EEE9] rounded-lg border border-[#E2DFD7] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold uppercase text-[#57534E]">Titik Koordinat Geografis</span>
                    <button type="button" onclick="window.autoGeocodeLocation()" class="text-[11px] font-semibold text-[#C2410C] hover:underline">
                        Cari Koordinat Otomatis (Nominatim)
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] text-[#78716C] mb-1">Lintang (Latitude)</label>
                        <input type="number" step="0.0001" id="loc-lat" required placeholder="-6.1018" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                    </div>
                    <div>
                        <label class="block text-[10px] text-[#78716C] mb-1">Bujur (Longitude)</label>
                        <input type="number" step="0.0001" id="loc-lng" required placeholder="106.8833" class="w-full bg-[#F9F8F6] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                    </div>
                </div>
            </div>
            <div class="pt-4 border-t border-[#E2DFD7] flex justify-end gap-2">
                <button type="button" onclick="window.closeNewLocationModal()" class="px-4 py-2 text-xs text-[#57534E] hover:text-[#1C1917]">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH PENGGUNA (ADMIN) -->
<div id="new-user-modal" class="fixed inset-0 z-50 bg-[#1C1917]/50 flex items-center justify-center p-4 hidden">
    <div class="bg-[#F9F8F6] border border-[#E2DFD7] rounded-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between pb-4 border-b border-[#E2DFD7] mb-4">
            <h3 class="font-serif-editorial text-xl font-semibold text-[#1C1917]">Tambah Pengguna Baru</h3>
            <button onclick="window.closeNewUserModal()" class="text-[#57534E] hover:text-[#1C1917]">✕</button>
        </div>
        <form onsubmit="window.handleCreateUser(event)" class="space-y-4">
            <div>
                <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Nama Lengkap</label>
                <input type="text" id="usr-name" required class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
            </div>
            <div>
                <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Email</label>
                <input type="email" id="usr-email" required class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Role</label>
                    <select id="usr-role" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                        <option value="customer">Customer</option>
                        <option value="operator">Operator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-semibold text-[#57534E] mb-1">Password</label>
                    <input type="password" id="usr-password" required minlength="6" class="w-full bg-[#F0EEE9] border border-[#E2DFD7] rounded-lg p-2 text-xs text-[#1C1917]">
                </div>
            </div>
            <div class="pt-4 border-t border-[#E2DFD7] flex justify-end gap-2">
                <button type="button" onclick="window.closeNewUserModal()" class="px-4 py-2 text-xs text-[#57534E] hover:text-[#1C1917]">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-[#C2410C] hover:bg-[#9A3412] text-xs font-semibold text-white">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
