@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
    <div class="container-fluid py-4 px-xl-5">

        {{-- BARIS 1: KARTU STATISTIK --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-4">

            {{-- Card 1: Total Bimbingan (Berwarna) --}}
            <div class="col">
                <div class="q-card q-card-green h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title text-white">Total Bimbingan</span>
                            <i class="bi bi-people-fill opacity-75 fs-5"></i>
                        </div>
                        <div class="stat-value text-white">{{ number_format($stat['total']) }}</div>
                    </div>
                    <div class="stat-label text-white mt-3 d-flex justify-content-between align-items-end">
                        <span>Seluruh mahasiswa bimbingan</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Aktif Magang --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Aktif Magang</span>
                            <div class="q-badge"><i class="bi bi-briefcase"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($stat['aktif']) }}</div>
                    </div>
                    <div class="stat-label mt-3 d-flex justify-content-between align-items-end">
                        <span>Sedang berlangsung</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Selesai (Belum SKP) --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Selesai Magang</span>
                            <div class="q-badge text-dark"><i class="bi bi-hourglass-split"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($stat['selesai_magang']) }}</div>
                    </div>
                    <div class="stat-label mt-3">Menunggu proses SKP</div>
                </div>
            </div>

            {{-- Card 4: Lulus SKP --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Lulus SKP</span>
                            <div class="q-badge"><i class="bi bi-mortarboard-fill"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($stat['sudah_skp']) }}</div>
                    </div>
                    <div class="stat-label mt-3">Total mahasiswa selesai</div>
                </div>
            </div>

        </div>

        {{-- BARIS 2: AGENDA SKP & MAHASISWA BIMBINGAN --}}
        <div class="row g-4 mb-4">

            {{-- Agenda SKP Terdekat --}}
            <div class="col-lg-4">
                <div class="q-card h-100 d-flex flex-column" style="min-height: 420px;">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event me-2"></i>Agenda SKP Terdekat</h5>
                        <div class="q-badge"><i class="bi bi-calendar3"></i></div>
                    </div>
                    <div class="custom-scroll flex-grow-1">
                        @forelse($agendaSkp as $agenda)
                            <div class="d-flex align-items-center py-3" style="border-bottom: 1px solid #f1f5f9;">
                                <div class="date-chip">
                                    <div class="date-chip-month">{{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->translatedFormat('M') }}</div>
                                    <div class="date-chip-day">{{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->format('d') }}</div>
                                </div>
                                <div class="ms-3 flex-grow-1 overflow-hidden">
                                    <h6 class="mb-1 fw-bold text-dark text-truncate">{{ $agenda->mahasiswa->user->name ?? 'Mahasiswa' }}</h6>
                                    <div class="stat-label mb-1">
                                        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->translatedFormat('l, d F Y') }}
                                    </div>
                                    <div class="fw-bold" style="color: var(--q-primary); font-size: 0.8rem;">
                                        <i class="bi bi-clock-fill me-1"></i>{{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->format('H:i') }} WIB
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-calendar2-x text-muted opacity-25" style="font-size: 2.5rem;"></i>
                                <p class="text-muted small mb-0 mt-2">Belum ada agenda SKP.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Mahasiswa Bimbingan Aktif --}}
            <div class="col-lg-8">
                <div class="q-card h-100 d-flex flex-column">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-workspace me-2"></i>Mahasiswa Bimbingan Aktif</h5>
                        <a href="{{ route('dosen.bimbingan.index') }}" class="q-link"><i class="bi bi-arrow-right me-1"></i>Lihat Semua</a>
                    </div>
                    <div class="custom-scroll flex-grow-1">
                        <table class="q-table">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Perusahaan</th>
                                    <th>Durasi Magang</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Filter: HANYA tampilkan yang statusnya Aktif Magang --}}
                                @forelse(collect($lokasi_magang)->filter(fn($mhs) => $mhs['status'] == 'Aktif Magang') as $mhs)
                                    <tr onclick="window.location='{{ route('dosen.bimbingan.detail', $mhs['id']) }}'" style="cursor: pointer;" title="Klik untuk lihat detail mahasiswa">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initial">{{ substr($mhs['nama_mhs'], 0, 1) }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $mhs['nama_mhs'] }}</div>
                                                    <div class="stat-label">{{ $mhs['nim'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-medium text-dark text-truncate" style="max-width: 220px;">{{ $mhs['perusahaan'] }}</td>
                                        <td class="stat-label">
                                            {{ \Carbon\Carbon::parse($mhs['tanggal_mulai'])->format('d M y') }} -<br>
                                            {{ \Carbon\Carbon::parse($mhs['tanggal_selesai'])->format('d M y') }}
                                        </td>
                                        <td class="text-end">
                                            <span class="q-badge-status text-success">
                                                <span class="dot bg-success"></span> Aktif Magang
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="bi bi-person-dash text-muted opacity-25" style="font-size: 3rem;"></i>
                                            </div>
                                            <h6 class="text-muted small">Belum ada data mahasiswa bimbingan yang aktif magang.</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- BARIS 3: PETA SEBARAN --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="q-card">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map-fill me-2"></i>Sebaran Lokasi Magang Aktif</h5>
                        <div class="q-badge"><i class="bi bi-broadcast me-1"></i>Live</div>
                    </div>
                    <div class="map-box">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('map', { zoomControl: false }).setView([-7.7956, 110.3695], 10);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap, © CartoDB',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            var blueIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
            });

            var markerLocations = @json($marker_locations);

            if (markerLocations.length > 0) {
                var bounds = L.latLngBounds();

                markerLocations.forEach(function (loc) {
                    // Hanya tampilkan jika statusnya Aktif Magang
                    if (loc.status !== 'Aktif Magang') {
                        return;
                    }

                    var studentList = loc.nama_mhs.map(function (name) {
                        return '<div class="small text-secondary mb-1 fw-bold"><i class="bi bi-person-fill me-1"></i>' + name + '</div>';
                    }).join('');

                    L.marker([loc.lat, loc.lng], { icon: blueIcon })
                        .addTo(map)
                        .bindPopup(`
                            <div class="text-start p-1" style="min-width: 160px;">
                                <h6 class="fw-bold mb-2 text-dark">${loc.perusahaan}</h6>
                                ${studentList}
                                <div class="mt-2">
                                    <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 0.65rem;">
                                        ${loc.status}
                                    </span>
                                </div>
                            </div>
                        `);

                    bounds.extend([loc.lat, loc.lng]);
                });

                // Pastikan bounds memiliki data sebelum di fitBounds
                if (bounds.isValid()) {
                    map.fitBounds(bounds, { padding: [40, 40] });
                }
            }

            // Fix issue map tidak load sempurna saat card layout digunakan
            setTimeout(() => { map.invalidateSize(); }, 500);
        });
    </script>
@endsection
