@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .icon-bg {
            position: absolute;
            right: -15px;
            bottom: -15px;
            font-size: 5.5rem;
            opacity: 0.08;
            transform: rotate(-15deg);
            z-index: 0;
        }

        /* Penyesuaian shadow dan transisi halus */
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
        }

        /* --- KELAS LAYOUT BARU --- */
        .dashboard-card-height {
            height: 385px;
        }

        .scrollable-card-body {
            overflow-y: auto;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge */
        }

        .scrollable-card-body::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        #map {
            height: 360px;
            width: 100%;
            border-radius: var(--radius-md);
            z-index: 1;
            border: 1px solid var(--border-light);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid px-4 py-3">

        {{-- 1. KARTU STATISTIK (4 Kolom) --}}
        <div class="row g-3 mb-4">

            {{-- Total Bimbingan --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm card-hover overflow-hidden h-100 rounded-4">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center position-relative z-1">
                            <div class="flex-shrink-0 bg-primary-soft p-3 rounded-4 text-primary">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-uppercase text-muted small fw-bold mb-1" style="font-size:0.7rem;">Total
                                    Bimbingan</h6>
                                <h3 class="mb-0 fw-bold text-primary">{{ $stat['total'] }}</h3>
                            </div>
                        </div>
                        <i class="bi bi-people-fill icon-bg text-primary"></i>
                    </div>
                </div>
            </div>

            {{-- Sedang Magang --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm card-hover overflow-hidden h-100 rounded-4">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center position-relative z-1">
                            <div class="flex-shrink-0 bg-info-soft p-3 rounded-4 text-info">
                                <i class="bi bi-person-workspace fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-uppercase text-muted small fw-bold mb-1" style="font-size:0.7rem;">Sedang
                                    Magang</h6>
                                <h3 class="mb-0 fw-bold text-info">{{ $stat['aktif'] }}</h3>
                            </div>
                        </div>
                        <i class="bi bi-person-workspace icon-bg text-info"></i>
                    </div>
                </div>
            </div>

            {{-- Selesai Magang (Belum SKP) --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm card-hover overflow-hidden h-100 rounded-4">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center position-relative z-1">
                            <div class="flex-shrink-0 bg-warning-soft p-3 rounded-4 text-warning text-dark">
                                <i class="bi bi-hourglass-split fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-uppercase text-muted small fw-bold mb-1" style="font-size:0.7rem;">Selesai
                                    (Belum SKP)</h6>
                                <h3 class="mb-0 fw-bold text-warning-emphasis">{{ $stat['selesai_magang'] }}</h3>
                            </div>
                        </div>
                        <i class="bi bi-hourglass-split icon-bg text-warning"></i>
                    </div>
                </div>
            </div>

            {{-- Sudah Lulus SKP --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm card-hover overflow-hidden h-100 rounded-4">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center position-relative z-1">
                            <div class="flex-shrink-0 bg-success-soft p-3 rounded-4 text-success">
                                <i class="bi bi-award-fill fs-4"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-uppercase text-muted small fw-bold mb-1" style="font-size:0.7rem;">Sudah
                                    Lulus SKP</h6>
                                <h3 class="mb-0 fw-bold text-success">{{ $stat['sudah_skp'] }}</h3>
                            </div>
                        </div>
                        <i class="bi bi-award-fill icon-bg text-success"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- 2. BARIS KEDUA: AGENDA SKP & TABEL STATUS MAHASISWA --}}
        <div class="row g-4 mb-4">

            {{-- AGENDA SKP MENDATANG (Sebelah Kiri) --}}
            <div class="col-xl-4 col-lg-5">
                @php
                    $agendaSkp = \App\Models\Magang::with('mahasiswa.user')
                        ->where('dosen_id', Auth::id())
                        ->where('status_jadwal_skp', 'disetujui')
                        ->where('status_skp', 'belum')
                        ->orderBy('jadwal_terpilih', 'asc')
                        ->get();
                @endphp

                <div class="card shadow-sm border-0 dashboard-card-height rounded-4 d-flex flex-column">
                    <div class="card-header bg-white py-3 border-bottom position-sticky top-0" style="z-index: 2;">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-calendar-event me-2"></i> Agenda SKP Terdekat
                        </h6>
                    </div>
                    <div class="card-body p-0 scrollable-card-body flex-grow-1">
                        <div class="list-group list-group-flush">
                            @forelse($agendaSkp as $agenda)
                                <div class="list-group-item px-4 py-3 border-light card-hover">
                                    <div class="d-flex align-items-center">

                                        {{-- 1. IKON KALENDER SOBEK --}}
                                        <div class="flex-shrink-0">
                                            <div class="border border-primary text-center overflow-hidden shadow-sm"
                                                style="width: 55px; border-radius: 12px;">
                                                <div class="bg-primary text-white text-uppercase fw-bold"
                                                    style="font-size: 0.7rem; padding: 4px 0;">
                                                    {{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->translatedFormat('M') }}
                                                </div>
                                                <div class="bg-white text-primary fw-bolder fs-4 py-1">
                                                    {{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->format('d') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 2. DETAIL MAHASISWA & WAKTU --}}
                                        <div class="ms-3 flex-grow-1 overflow-hidden">
                                            <h6 class="mb-1 fw-bold text-dark text-truncate">
                                                {{ $agenda->mahasiswa->user->name ?? 'Mahasiswa' }}</h6>
                                            <div class="d-flex flex-column gap-1 mt-1">
                                                {{-- Hari & Tanggal Lengkap --}}
                                                <div class="text-muted small d-flex align-items-center">
                                                    <i class="bi bi-calendar3 me-2"></i>
                                                    {{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->translatedFormat('l, d F Y') }}
                                                </div>
                                                {{-- Jam Pelaksanaan (Ditebalkan & Diberi Warna) --}}
                                                <div class="text-primary small fw-bold d-flex align-items-center">
                                                    <i class="bi bi-clock-fill me-2"></i>
                                                    {{ \Carbon\Carbon::parse($agenda->jadwal_terpilih)->format('H:i') }} WIB
                                                </div>
                                            </div>
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
            </div>

            {{-- TABEL STATUS MAHASISWA (Sebelah Kanan) --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow-sm border-0 dashboard-card-height rounded-4 d-flex flex-column">
                    <div class="card-header bg-white py-3 border-bottom position-sticky top-0 d-flex justify-content-between align-items-center"
                        style="z-index: 2;">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-activity me-2"></i> Status Mahasiswa Bimbingan
                        </h6>
                        <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-sm btn-outline-secondary px-3">Lihat
                            Semua</a>
                    </div>
                    <div class="card-body p-0 scrollable-card-body flex-grow-1">
                        <div class="table-responsive border-0">
                            <table class="table table-hover mb-0">
                                <thead class="position-sticky top-0 bg-white shadow-sm" style="z-index: 1;">
                                    <tr>
                                        <th class="ps-4">MAHASISWA</th>
                                        <th>PERUSAHAAN</th>
                                        <th>DURASI MAGANG</th>
                                        <th class="text-center">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Filter: Sembunyikan yang statusnya sudah Selesai SKP agar tabel fokus ke yang
                                    sedang berjalan --}}
                                    @forelse(collect($lokasi_magang)->reject(fn($mhs) => str_contains($mhs['status'], 'Selesai')) as $mhs)
                                        <tr>
                                            <td class="ps-4 align-middle">
                                                <div class="fw-bold text-dark">{{ $mhs['nama_mhs'] }}</div>
                                                <div class="small text-muted">{{ $mhs['nim'] }}</div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                                    {{ $mhs['perusahaan'] }}</div>
                                            </td>
                                            <td class="align-middle text-muted small">
                                                {{ \Carbon\Carbon::parse($mhs['tanggal_mulai'])->format('d M y') }} - <br>
                                                {{ \Carbon\Carbon::parse($mhs['tanggal_selesai'])->format('d M y') }}
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($mhs['status'] == 'Proses Seminar')
                                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-1">Proses
                                                        Seminar</span>
                                                @else
                                                    <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-1">Aktif
                                                        Magang</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="bi bi-clipboard-data text-muted opacity-25"
                                                        style="font-size: 3rem;"></i>
                                                </div>
                                                <h6 class="text-muted small">Belum ada data mahasiswa bimbingan yang aktif
                                                    magang.</h6>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- 3. BARIS KETIGA: PETA SEBARAN (Full Lebar) --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-map-fill me-2"></i> Sebaran Lokasi Magang Aktif
                            </h6>
                            <span class="badge bg-primary-soft text-primary px-3 py-1 rounded-pill">
                                <i class="bi bi-broadcast me-1"></i> Live
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3 position-relative">
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

                    var iconToUse = blueIcon;
                    var badgeColor = 'bg-primary';

                    var studentList = loc.nama_mhs.map(function (name) {
                        return '<div class="small text-secondary mb-1 fw-bold"><i class="bi bi-person-fill me-1"></i>' + name + '</div>';
                    }).join('');

                    L.marker([loc.lat, loc.lng], { icon: iconToUse })
                        .addTo(map)
                        .bindPopup(`
                            <div class="text-start p-1" style="min-width: 160px;">
                                <h6 class="fw-bold mb-2 text-dark">${loc.perusahaan}</h6>
                                ${studentList}
                                <div class="mt-2">
                                    <span class="badge ${badgeColor} rounded-pill px-2 py-1" style="font-size: 0.65rem;">
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