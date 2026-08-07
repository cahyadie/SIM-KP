@extends('layouts.app')

@section('title', 'Dashboard Kaprodi')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
    <div class="container-fluid content-wrapper py-4 px-xl-5">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-4">

            {{-- Card 1: Total Terdaftar Magang (Baru & Berwarna) --}}
            <div class="col">
                <div class="q-card q-card-green h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title text-white">Total Pendaftar</span>
                            <i class="bi bi-people-fill opacity-75 fs-5"></i>
                        </div>
                        <div class="stat-value text-white">{{ number_format($total_pengajuan) }}</div>
                    </div>
                    <div class="stat-label text-white mt-3 d-flex justify-content-between align-items-end">
                        <span>Seluruh pengajuan magang</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Sedang Magang --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Aktif Magang</span>
                            <div class="q-badge"><i class="bi bi-briefcase"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($sedang_magang) }}</div>
                    </div>
                    <div class="stat-label mt-3 d-flex justify-content-between align-items-end">
                        <span>Mahasiswa saat ini</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Belum SKP --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Belum SKP</span>
                            <div class="q-badge text-dark"><i class="bi bi-clock-history"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($belum_skp) }}</div>
                    </div>
                    <div class="stat-label mt-3"></div>
                </div>
            </div>

            {{-- Card 3: Lulus SKP --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Selesai SKP</span>
                            <div class="q-badge"><i class="bi bi-mortarboard-fill"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($sudah_skp) }}</div>
                    </div>
                    <div class="stat-label mt-3">Total mahasiswa Selesai</div>
                </div>
            </div>

</div>

        {{-- BARIS 2: RINCIAN TABEL MAHASISWA --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="q-card p-4">

                    {{-- Toggle Tabs & Header --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                        <h5 class="fw-bold mb-3 mb-md-0 text-dark">Rincian Data Mahasiswa</h5>

                        <ul class="nav q-nav-toggle" id="magangTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aktif" type="button"
                                    role="tab">
                                    Aktif Magang <span
                                        class="badge rounded-pill ms-1 text-dark">{{ $magang_aktif->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#belum-skp" type="button"
                                    role="tab">
                                    Belum SKP <span
                                        class="badge rounded-pill ms-1 text-dark">{{ $magang_belum_skp->count() }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#lulus" type="button"
                                    role="tab">
                                    Selesai SKP <span
                                        class="badge rounded-pill ms-1 text-dark">{{ $magang_lulus_skp->count() }}</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="magangTabsContent">

                        {{-- TAB 1: SEDANG MAGANG --}}
                        <div class="tab-pane fade show active" id="aktif" role="tabpanel">
                            <div class="custom-scroll">
                                <table class="q-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Mahasiswa</th>
                                            <th>Perusahaan</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($magang_aktif as $magang)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-initial">
                                                            {{ substr($magang->mahasiswa->user->name ?? 'A', 0, 1) }}</div>
                                                        <div>
                                                            <div class="fw-bold text-dark">
                                                                {{ $magang->mahasiswa->user->name ?? 'N/A' }}</div>
                                                            <div class="stat-label">{{ $magang->mahasiswa->nim ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fw-medium text-dark">
                                                    {{ $magang->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                                <td class="stat-label">
                                                    {{ \Carbon\Carbon::parse($magang->tanggal_mulai)->format('d M Y') }}</td>
                                                <td>
                                                    <span class="q-badge-status text-success">
                                                        <span class="dot bg-success"></span> Aktif Magang
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Tidak ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: BELUM SKP --}}
                        <div class="tab-pane fade" id="belum-skp" role="tabpanel">
                            <div class="custom-scroll">
                                <table class="q-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Mahasiswa</th>
                                            <th>Perusahaan</th>
                                            <th>Tgl Selesai Magang</th>
                                            <th>Status SKP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($magang_belum_skp as $magang)
                                            @php $keterlambatan = floor(\Carbon\Carbon::parse($magang->tanggal_selesai)->diffInDays(\Carbon\Carbon::now())); @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-initial" style="background:#fff3cd; color:#b45309;">
                                                            {{ substr($magang->mahasiswa->user->name ?? 'A', 0, 1) }}</div>
                                                        <div>
                                                            <div class="fw-bold text-dark">
                                                                {{ $magang->mahasiswa->user->name ?? 'N/A' }}</div>
                                                            <div class="stat-label">{{ $magang->mahasiswa->nim ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fw-medium text-dark">
                                                    {{ $magang->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="fw-medium text-dark">
                                                        {{ \Carbon\Carbon::parse($magang->tanggal_selesai)->format('d M Y') }}
                                                    </div>
                                                    <div class="stat-label text-danger">Lewat {{ $keterlambatan }} Hari</div>
                                                </td>
                                                <td>
                                                    <span class="q-badge-status text-warning"
                                                        style="color: #d97706 !important;">
                                                        <span class="dot bg-warning"></span> Belum SKP
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Semua sudah menyelesaikan
                                                    SKP.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 3: LULUS SKP --}}
                        <div class="tab-pane fade" id="lulus" role="tabpanel">
                            <div class="custom-scroll">
                                <table class="q-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Mahasiswa</th>
                                            <th>Perusahaan</th>
                                            <th>Terbit SKP</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($magang_lulus_skp as $magang)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-initial" style="background:#eaf5ee; color:#128B53;">
                                                            {{ substr($magang->mahasiswa->user->name ?? 'A', 0, 1) }}</div>
                                                        <div>
                                                            <div class="fw-bold text-dark">
                                                                {{ $magang->mahasiswa->user->name ?? 'N/A' }}</div>
                                                            <div class="stat-label">{{ $magang->mahasiswa->nim ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="fw-medium text-dark">
                                                    {{ $magang->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                                <td class="stat-label">
                                                    {{ \Carbon\Carbon::parse($magang->updated_at)->format('d M Y') }}</td>
                                                <td>
                                                    <span class="q-badge-status text-success">
                                                        <span class="dot bg-success"></span> Selesai
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 3: GRAFIK INDUSTRI (Lebar 8) & GRAFIK GAJI (Lebar 4) --}}
        <div class="row g-4 mb-4">
            {{-- Grafik Bidang Industri --}}
            <div class="col-lg-8">
                <div class="q-card h-100">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark">Sebaran Bidang Industri</h5>
                        <div class="q-badge">Tahunan</div>
                    </div>
                    <div class="chart-container mt-3">
                        <canvas id="industriChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Grafik Gaji --}}
            <div class="col-lg-4">
                <div class="q-card h-100">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark">Status Pendapatan</h5>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-3" style="height: 250px;">
                        <canvas id="gajiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 4: PETA LOKASI & TOP PERUSAHAAN --}}
        <div class="row g-4 mb-5">
            {{-- Peta Lokasi Super Lebar --}}
            <div class="col-lg-8">
                <div class="q-card h-100 d-flex flex-column" style="min-height: 420px; padding: 1rem;">
                    <div class="q-card-header mb-3 px-2">
                        <h5 class="fw-bold mb-0 text-dark">Peta Persebaran Mahasiswa Sedang Magang</h5>
                        <div class="q-badge"><i class="bi bi-geo-alt-fill me-1"></i>Active</div>
                    </div>
                    <div class="flex-grow-1" style="border-radius: 12px; overflow: hidden;">
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            {{-- Top Perusahaan --}}
            <div class="col-lg-4">
                <div class="q-card h-100 d-flex flex-column" style="min-height: 420px;">
                    <div class="q-card-header border-bottom-0 pb-0">
                        <h5 class="fw-bold mb-0 text-dark">Top Perusahaan</h5>
                        <div class="q-badge"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                    <div class="card-body p-0 mt-3 flex-grow-1 custom-scroll">
                        <table class="q-table">
                            <tbody>
                                @forelse($top_perusahaan as $index => $pt)
                                    <tr>
                                        <td style="width: 10%;">
                                            <div class="avatar-initial bg-light text-muted">{{ $index + 1 }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 170px;">
                                                {{ $pt->nama_perusahaan }}</div>
                                            <div class="stat-label text-truncate" style="max-width: 170px;">
                                                {{ $pt->kategori_industri ?? 'Umum' }}</div>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark fs-5">{{ $pt->total }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted py-4">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ==========================================
            // 1. Inisialisasi CHART.JS (Style Modern)
            // ==========================================
            Chart.defaults.font.family = "'Outfit', sans-serif";
            Chart.defaults.color = '#9ca3af';

            const industriDataRaw = @json($sebaran_bidang);
            const indLabels = industriDataRaw.map(item => item.kategori_industri || 'Umum');
            const indValues = industriDataRaw.map(item => item.total);

            new Chart(document.getElementById('industriChart'), {
                type: 'bar',
                data: {
                    labels: indLabels,
                    datasets: [{
                        label: 'Total Mahasiswa',
                        data: indValues,
                        backgroundColor: '#128B53',
                        borderRadius: 50,
                        barThickness: 30,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, padding: 10 },
                            grid: { color: '#f1f5f9', drawBorder: false }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { padding: 10 }
                        }
                    }
                }
            });

            new Chart(document.getElementById('gajiChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Unpaid'],
                    datasets: [{
                        data: [{{ $paid }}, {{ $unpaid }}],
                        backgroundColor: ['#128B53', '#eaf5ee'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyle: 'circle' } }
                    },
                    cutout: '75%'
                }
            });

            // ==========================================
            // 2. Inisialisasi LEAFLET MAPS (Filter Aktif Saja)
            // ==========================================
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
                iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            });

            var map = L.map('map', { zoomControl: false }).setView([-2.5, 118], 5);
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap', subdomains: 'abcd', maxZoom: 19
            }).addTo(map);

            const activeMagangs = @json($magang_aktif);
            const groupedMapData = {};

            activeMagangs.forEach(magang => {
                if (!magang.perusahaan || !magang.perusahaan.latitude || !magang.perusahaan.longitude) return;

                const ptId = magang.perusahaan.id;
                if (!groupedMapData[ptId]) {
                    groupedMapData[ptId] = {
                        nama_perusahaan: magang.perusahaan.nama_perusahaan,
                        lat: magang.perusahaan.latitude,
                        lng: magang.perusahaan.longitude,
                        students: []
                    };
                }

                const studentName = magang.mahasiswa && magang.mahasiswa.user ? magang.mahasiswa.user.name : 'Unknown';
                groupedMapData[ptId].students.push(studentName);
            });

            var bounds = L.latLngBounds();
            let hasMarkers = false;

            Object.values(groupedMapData).forEach(function (loc) {
                var studentList = loc.students.map(function (name) {
                    return '<div style="font-size: 0.8rem; color: #666; margin-bottom: 3px;">• ' + name + '</div>';
                }).join('');

                L.marker([loc.lat, loc.lng]).addTo(map).bindPopup(`
                        <div style="min-width: 150px; font-family: 'Outfit', sans-serif;">
                            <h6 style="font-weight: 700; color: #1f2937; margin-bottom: 8px;">${loc.nama_perusahaan}</h6>
                            ${studentList}
                        </div>
                    `);
                bounds.extend([loc.lat, loc.lng]);
                hasMarkers = true;
            });

            if (hasMarkers && bounds.isValid()) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }

            setTimeout(() => { map.invalidateSize(); }, 500);
        });
    </script>
@endsection