@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        .text-super-muted { 
            color: var(--text-muted); 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .icon-box {
            width: 48px; height: 48px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        
        /* Tinggi Peta disesuaikan karena sekarang posisinya melebar di bawah */
        #map { height: 350px; width: 100%; border-radius: var(--radius-lg); z-index: 1; border: 1px solid var(--border-light); }
        
        .bg-primary-soft { background-color: #e8f5e9; color: var(--primary-color); }
        .bg-info-soft { background-color: #E0F2FE; color: #075985; }
        .bg-success-soft { background-color: #DCFCE7; color: #166534; }

        /* --- PERBAIKAN LAYOUT GRID & SCROLL CONTROLLER --- */
        .dashboard-card-height {
            height: 400px; /* Tinggi fix agar card kiri dan kanan sejajar sempurna */
        }
        
        .scrollable-card-body {
            overflow-y: auto;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }
        
        .scrollable-card-body::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
    </style>
@endsection

@section('content')
<div class="container-fluid">
    
    {{-- 4 WIDGETS ATAS --}}
    <div class="row g-3 mb-4">
        {{-- Widget 1: Pendaftar Baru --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm" style="background: var(--primary-gradient); color: white;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-white text-primary me-3 shadow-sm">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <span class="d-block text-white opacity-75" style="font-size: 0.75rem; font-weight: 600;">PENDAFTAR BARU</span>
                        <h3 class="fw-bold text-white mb-0">{{ count($pendaftar_baru) }} <span class="fs-6 text-white opacity-75">Data</span></h3>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Widget 2: Aktif Magang --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-primary-soft me-3">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                        <span class="text-super-muted">AKTIF MAGANG</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $sedang_magang }} <span class="fs-6 text-muted fw-normal">Mhs</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 3: Menunggu SKP --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-info-soft me-3">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div>
                        <span class="text-super-muted">BELUM SKP</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $belum_skp }} <span class="fs-6 text-muted fw-normal">Mhs</span></h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 4: Total Mahasiswa --}}
        <div class="col-xl-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-success-soft me-3">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <span class="text-super-muted">TOTAL MAHASISWA</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $total_mahasiswa }} <span class="fs-6 text-muted fw-normal">Mhs</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS UTAMA: PENDAFTARAN & STATUS SKP SEJAJAR --}}
    <div class="row g-4 mb-4">
        {{-- KOLOM KIRI: Pendaftaran Terbaru (Lebar 8/12) --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm dashboard-card-height d-flex flex-column">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center position-sticky top-0" style="z-index: 2;">
                    <h5 class="fw-bold mb-0 text-dark">Pendaftaran Magang Terbaru</h5>
                    <a href="{{ route('admin.riwayat-magang.index') }}" class="btn btn-sm btn-outline-primary px-3">Lihat Riwayat</a>
                </div>
                <div class="card-body p-0 scrollable-card-body flex-grow-1">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="position-sticky top-0 bg-white shadow-sm" style="z-index: 1;">
                                <tr>
                                    <th class="ps-4">MAHASISWA</th>
                                    <th>PERUSAHAAN</th>
                                    <th>TANGGAL DAFTAR</th>
                                    <th class="text-center">DETAIL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendaftar_baru as $ajuan)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $ajuan->mahasiswa->user->name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $ajuan->mahasiswa->nim ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $ajuan->perusahaan->nama_perusahaan ?? 'N/A' }}</div>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $ajuan->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.riwayat-magang.show', $ajuan->id) }}" class="btn btn-sm btn-light py-1 px-3">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">Belum ada data pendaftaran.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Status SKP (Lebar 4/12) --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm dashboard-card-height d-flex flex-column">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Status SKP</h5>
                    <!-- <a href="{{ route('admin.skp') }}" class="text-primary fw-bold text-decoration-none" style="font-size: 0.8rem;">Kelola SKP</a> -->
                </div>
                <div class="card-body p-0 scrollable-card-body flex-grow-1">
                    <div class="list-group list-group-flush">
                        @forelse($list_skp as $skp)
                            <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-0 border-bottom">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $skp->mahasiswa->user->name }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 180px;">{{ $skp->perusahaan->nama_perusahaan }}</div>
                                </div>
                                <div>
                                    @if($skp->status_skp == 'sudah')
                                        <span class="badge bg-success-soft">Selesai</span>
                                    @else
                                        <span class="badge bg-warning-soft">Belum SKP</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">Belum ada data SKP.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS BAWAH: PETA SEBARAN MEMENUHI BAGIAN BAWAH DENGAN FULL WIDTH (col-12) --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-dark">Peta Sebaran</h5>
                </div>
                <div class="card-body p-3">
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
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

            var locations = @json($lokasi_magang);
            if(locations.length > 0) {
                var bounds = L.latLngBounds();
                locations.forEach(function(loc) {
                    
                    // Sembunyikan marker jika magang selesai atau sudah lulus SKP
                    if (loc.is_selesai || loc.status_skp === 'sudah') {
                        return;
                    }

                    if(loc.lat && loc.lng) {
                        var studentList = loc.nama_mhs.map(function(name) {
                            return '<div class="small text-secondary"><i class="bi bi-person-fill me-1"></i>' + name + '</div>';
                        }).join('');
                        L.marker([loc.lat, loc.lng]).addTo(map)
                         .bindPopup('<div style="min-width:150px;"><b>' + loc.perusahaan + '</b><br>' + studentList + '</div>');
                        bounds.extend([loc.lat, loc.lng]);
                    }
                });
                
                // Pastikan bounds memiliki data sebelum di fitBounds
                if(bounds.isValid()) {
                    map.fitBounds(bounds, { padding: [20, 20] });
                }
            }
        });
    </script>
@endsection