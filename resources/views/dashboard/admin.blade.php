@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
    <div class="container-fluid py-4 px-xl-5">

        {{-- BARIS 1: KARTU STATISTIK --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4 mb-4">

            {{-- Card 1: Pendaftar Baru (Berwarna) --}}
            <div class="col">
                <div class="q-card q-card-green h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title text-white">Pendaftar Baru</span>
                            <i class="bi bi-person-plus-fill opacity-75 fs-5"></i>
                        </div>
                        <div class="stat-value text-white">{{ count($pendaftar_baru) }}</div>
                    </div>
                    <div class="stat-label text-white mt-3 d-flex justify-content-between align-items-end">
                        <span>Pengajuan magang terbaru</span>
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
                        <div class="stat-value text-dark">{{ number_format($sedang_magang) }}</div>
                    </div>
                    <div class="stat-label mt-3 d-flex justify-content-between align-items-end">
                        <span>Mahasiswa saat ini</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Belum SKP --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Belum SKP</span>
                            <div class="q-badge text-dark"><i class="bi bi-clock-history"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($belum_skp) }}</div>
                    </div>
                    <div class="stat-label mt-3">Menunggu pengajuan SKP</div>
                </div>
            </div>

            {{-- Card 4: Total Mahasiswa --}}
            <div class="col">
                <div class="q-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="q-card-header mb-2">
                            <span class="q-card-title">Total Mahasiswa</span>
                            <div class="q-badge"><i class="bi bi-mortarboard-fill"></i></div>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($total_mahasiswa) }}</div>
                    </div>
                    <div class="stat-label mt-3">Seluruh mahasiswa terdaftar</div>
                </div>
            </div>

        </div>

        {{-- BARIS 2: PENDAFTARAN TERBARU & STATUS SKP --}}
        <div class="row g-4 mb-4">

            {{-- Tabel Pendaftaran Terbaru --}}
            <div class="col-lg-8">
                <div class="q-card h-100 d-flex flex-column">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark">Pendaftaran Magang Terbaru</h5>
                        <a href="{{ route('admin.riwayat-magang.index') }}" class="q-link"><i class="bi bi-arrow-right me-1"></i>Lihat Riwayat</a>
                    </div>
                    <div class="custom-scroll flex-grow-1">
                        <table class="q-table">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Perusahaan</th>
                                    <th>Tanggal Daftar</th>
                                    <th class="text-end">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendaftar_baru as $ajuan)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initial">{{ substr($ajuan->mahasiswa->user->name ?? 'A', 0, 1) }}</div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $ajuan->mahasiswa->user->name ?? 'N/A' }}</div>
                                                    <div class="stat-label">{{ $ajuan->mahasiswa->nim ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-medium text-dark">{{ $ajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</td>
                                        <td class="stat-label">{{ $ajuan->created_at->format('d M Y, H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.riwayat-magang.show', $ajuan->id) }}" class="q-btn-icon" title="Lihat detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data pendaftaran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Status SKP --}}
            <div class="col-lg-4">
                <div class="q-card h-100 d-flex flex-column">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark">Status SKP</h5>
                        <div class="q-badge"><i class="bi bi-file-earmark-check"></i></div>
                    </div>
                    <div class="custom-scroll flex-grow-1">
                        @forelse($list_skp as $skp)
                            <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #f1f5f9;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial">{{ substr($skp->mahasiswa->user->name ?? 'A', 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $skp->mahasiswa->user->name ?? 'N/A' }}</div>
                                        <div class="stat-label text-truncate" style="max-width: 180px;">{{ $skp->perusahaan->nama_perusahaan ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                @if ($skp->status_skp == 'sudah')
                                    <span class="q-badge-status text-success">
                                        <span class="dot bg-success"></span> Selesai
                                    </span>
                                @else
                                    <span class="q-badge-status text-warning" style="color: #d97706 !important;">
                                        <span class="dot bg-warning"></span> Belum
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">Belum ada data SKP.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- BARIS 3: PETA SEBARAN --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="q-card">
                    <div class="q-card-header">
                        <h5 class="fw-bold mb-0 text-dark">Peta Sebaran Mahasiswa Magang</h5>
                        <div class="q-badge"><i class="bi bi-geo-alt-fill me-1"></i>Live</div>
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
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map', { zoomControl: false }).setView([-2.5, 118], 4);
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(map);

            var locations = @json($lokasi_magang);

            if (locations.length > 0) {
                var bounds = L.latLngBounds();
                locations.forEach(function(loc) {

                    // Sembunyikan marker jika magang selesai atau sudah lulus SKP
                    if (loc.is_selesai || loc.status_skp === 'sudah') {
                        return;
                    }

                    if (loc.lat && loc.lng) {
                        var studentList = loc.nama_mhs.map(function(name) {
                            return '<div class="small text-secondary"><i class="bi bi-person-fill me-1"></i>' + name + '</div>';
                        }).join('');
                        L.marker([loc.lat, loc.lng]).addTo(map)
                         .bindPopup('<div style="min-width:150px;"><b>' + loc.perusahaan + '</b><br>' + studentList + '</div>');
                        bounds.extend([loc.lat, loc.lng]);
                    }
                });

                // Pastikan bounds memiliki data sebelum di fitBounds
                if (bounds.isValid()) {
                    map.fitBounds(bounds, { padding: [20, 20] });
                }
            }

            setTimeout(() => { map.invalidateSize(); }, 500);
        });
    </script>
@endsection
