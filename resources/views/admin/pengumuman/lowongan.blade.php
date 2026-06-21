@extends('layouts.app')

@section('title', 'Info Lowongan Magang')

@section('content')
    <div class="container-fluid px-0">
        
        {{-- Top Control Panel (Search, Filter, Sort) --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center m-0">
                    
                    {{-- Search Bar --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="input-group align-items-center bg-light rounded-3 px-2 border">
                            <i class="bi bi-search text-muted ps-2"></i>
                            <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none" 
                                placeholder="Cari posisi atau perusahaan..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Filter Angkatan --}}
                    <div class="col-6 col-md-3 col-lg-2">
                        <select name="angkatan" class="form-select bg-light border text-muted shadow-none">
                            <option value="">Semua Angkatan</option>
                            <option value="2021" {{ request('angkatan') == '2021' ? 'selected' : '' }}>Angkatan 2021</option>
                            <option value="2022" {{ request('angkatan') == '2022' ? 'selected' : '' }}>Angkatan 2022</option>
                            <option value="2023" {{ request('angkatan') == '2023' ? 'selected' : '' }}>Angkatan 2023</option>
                        </select>
                    </div>

                    {{-- Filter Paid / Unpaid --}}
                    <div class="col-6 col-md-3 col-lg-2">
                        <select name="tipe_pendapatan" class="form-select bg-light border text-muted shadow-none">
                            <option value="">Paid/Unpaid</option>
                            <option value="paid" {{ request('tipe_pendapatan') == 'paid' ? 'selected' : '' }}>Paid (Berbayar)</option>
                            <option value="unpaid" {{ request('tipe_pendapatan') == 'unpaid' ? 'selected' : '' }}>Unpaid (Sukarela)</option>
                        </select>
                    </div>

                    {{-- Sorting --}}
                    <div class="col-6 col-md-6 col-lg-2">
                        <select name="sort" class="form-select bg-light border text-muted shadow-none">
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="col-6 col-md-6 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-medium shadow-sm">Terapkan</button>
                        {{-- Tombol Reset muncul jika ada filter/search yang aktif --}}
                        @if(request('search') || request('angkatan') || request('tipe_pendapatan') || (request('sort') && request('sort') != 'terbaru'))
                            <a href="{{ url()->current() }}" class="btn btn-outline-danger shadow-sm" title="Reset Filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Result Counter --}}
        <div class="mb-3 d-flex align-items-center text-muted small">
            <i class="bi bi-card-list me-2"></i> Menampilkan <strong class="text-dark mx-1">{{ $lowongan->total() }}</strong> lowongan magang tersedia
        </div>

        {{-- List Lowongan --}}
        <div class="d-flex flex-column gap-3">
            @forelse($lowongan as $l)
                <div class="card border-0 shadow-sm rounded-4 job-card-hover overflow-hidden">
                    <div class="card-body p-4">
                        <div class="row align-items-center text-center text-md-start">
                            
                            {{-- 2. Informasi Utama --}}
                            <div class="col-12 col-md mb-3 mb-md-0">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-2 mb-1">
                                    <h5 class="fw-bold text-dark mb-0">{{ $l->judul }}</h5>
                                    {{-- Badge "Terbaru" jika diposting kurang dari 3 hari yang lalu --}}
                                    @if($l->created_at->diffInDays(now()) <= 3)
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;">Terbaru</span>
                                    @endif
                                </div>
                                
                                {{-- Penambahan Status Paid / Unpaid Di Bawah Judul (Berdasarkan info_gaji) --}}
                                <div class="mb-2 d-flex justify-content-center justify-content-md-start">
                                    @if(!empty($l->info_gaji) && strtolower($l->info_gaji) !== 'unpaid') 
                                        <span class="badge bg-light text-success border border-success-subtle px-2 py-1 rounded-3 small">
                                            <i class="bi bi-cash-stack me-1"></i> Paid 
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border border-secondary-subtle px-2 py-1 rounded-3 small">
                                            <i class="bi bi-dash-circle me-1"></i> Unpaid 
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Meta Data (Lokasi, Angkatan, Tanggal) --}}
                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-2">
                                    @if($l->lokasi)
                                        <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                                            <i class="bi bi-geo-alt text-danger me-1"></i> {{ $l->lokasi }}
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                                        <i class="bi bi-people text-info me-1"></i> Angkatan: <span class="fw-medium text-dark ms-1">{{ $l->target_angkatan ?? 'Semua' }}</span>
                                    </div>

                                    <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                                        <i class="bi bi-calendar-event text-warning me-1"></i> Diposting: {{ $l->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Tombol Aksi --}}
                            <div class="col-12 col-md-auto mt-3 mt-md-0">
                                <a href="{{ route('lowongan.show', $l->id) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-medium shadow-sm w-100">
                                    Lihat Detail <i class="bi bi-chevron-right ms-1" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                {{-- Smart Empty State --}}
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 my-4">
                        <div class="mb-4 text-muted opacity-50">
                            <i class="bi bi-search display-1"></i>
                        </div>
                        
                        @if(request('search') || request('angkatan') || request('tipe_pendapatan'))
                            <h5 class="fw-bold text-dark mb-2">Pencarian Tidak Ditemukan</h5>
                            <p class="text-muted mx-auto" style="max-width: 400px;">
                                Tidak ada lowongan yang cocok dengan kata kunci atau filter yang kamu gunakan. Coba ubah kata kunci pencarianmu.
                            </p>
                            <a href="{{ url()->current() }}" class="btn btn-outline-primary mt-3 rounded-pill px-4">Hapus Filter</a>
                        @else
                            <h5 class="fw-bold text-dark mb-2">Belum Ada Lowongan Magang</h5>
                            <p class="text-muted mx-auto" style="max-width: 400px;">
                                Saat ini belum ada informasi lowongan magang yang dipublikasikan oleh Program Studi. Silakan periksa kembali nanti.
                            </p>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($lowongan->hasPages())
            <div class="d-flex justify-content-end mt-5 mb-4 custom-pagination">
                {{ $lowongan->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Styling Khusus untuk UX & Estetika --}}
    <style>
        .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
        
        .job-card-hover {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .job-card-hover:hover {
            transform: translateY(-3px);
            border-color: rgba(13, 110, 253, 0.2) !important;
            box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08)!important;
        }
    </style>
@endsection