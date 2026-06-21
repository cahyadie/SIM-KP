@extends('layouts.app')

@section('title', 'Direktori Tempat Magang')

@section('content')
<div class="container-fluid px-0">

    {{-- HEADER, SEARCH & FILTER (Diterapkan dengan gaya Grid) --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center m-0">
            
            {{-- Search Bar --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group align-items-center bg-light rounded-3 px-2 border">
                    <i class="bi bi-search text-muted ps-2"></i>
                    <input type="text" name="cari" class="form-control border-0 bg-transparent shadow-none" 
                        placeholder="Cari posisi atau perusahaan..." value="{{ request('cari') }}">
                </div>
            </div>

            {{-- Filter Kategori --}}
            <div class="col-6 col-md-3 col-lg-2">
                <select name="kategori" class="form-select bg-light border text-muted shadow-none">
                    <option value="">Semua Kategori</option>
                    <option value="IT" {{ request('kategori') == 'IT' ? 'selected' : '' }}>IT</option>
                    <option value="BUMN" {{ request('kategori') == 'BUMN' ? 'selected' : '' }}>BUMN</option>
                    <option value="Pemerintahan" {{ request('kategori') == 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                    <option value="Start-up" {{ request('kategori') == 'Start-up' ? 'selected' : '' }}>Start-up</option>
                    <option value="Manufaktur" {{ request('kategori') == 'Manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                </select>
            </div>

            {{-- Filter Tipe --}}
            <div class="col-6 col-md-3 col-lg-2">
                <select name="tipe" class="form-select bg-light border text-muted shadow-none">
                    <option value="">Paid/Unpaid</option>
                    <option value="paid" {{ request('tipe') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('tipe') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>

            {{-- Sorting --}}
            <div class="col-6 col-md-6 col-lg-2">
    <select name="sort" class="form-select bg-light border text-muted shadow-none">
        <option value="">Sortir...</option>
        <option value="rating_tinggi" {{ request('sort') == 'rating_tinggi' ? 'selected' : '' }}>Rating Tertinggi</option>
        <option value="rating_terendah" {{ request('sort') == 'rating_terendah' ? 'selected' : '' }}>Rating Terendah</option>
        <option value="mhs_terbanyak" {{ request('sort') == 'mhs_terbanyak' ? 'selected' : '' }}>Mhs Terbanyak</option>
        <option value="mhs_tersedikit" {{ request('sort') == 'mhs_tersedikit' ? 'selected' : '' }}>Mhs Tersedikit</option>
    </select>
</div>

            {{-- Tombol Aksi --}}
            <div class="col-6 col-md-6 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn text-white w-100 fw-medium shadow-sm" style="background-color: #014f31;">
                    Terapkan
                </button>
                @if(request()->hasAny(['cari', 'kategori', 'tipe', 'sort']))
                    <a href="{{ url()->current() }}" class="btn btn-outline-danger shadow-sm" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

    {{-- INDIKATOR HASIL PENCARIAN --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-secondary fw-medium">
                Menampilkan <span class="text-dark fw-bold">{{ $perusahaans->total() }}</span> perusahaan
            </span>
            
            {{-- Active Filters Badges --}}
            @if(request('cari') || request('tipe') || request('kategori') || (request('sort') && request('sort') != 'terbaru')) 
                <div class="d-flex gap-1 ms-2">
                    @if(request('cari'))
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill d-flex align-items-center">
                            "{{ request('cari') }}"
                        </span>
                    @endif
                    @if(request('tipe'))
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill d-flex align-items-center">
                            Tipe: {{ ucfirst(request('tipe')) }}
                        </span>
                    @endif
                    @if(request('kategori'))
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill d-flex align-items-center">
                            Kategori: {{ request('kategori') }}
                        </span>
                    @endif
                    @if(request('sort') && request('sort') != 'terbaru')
                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill d-flex align-items-center">
                            Sortir: {{ ucwords(str_replace('_', ' ', request('sort'))) }}
                        </span>
                    @endif
                    
                    <a href="{{ url()->current() }}" class="text-danger ms-1 d-flex align-items-center text-decoration-none" title="Reset Semua Filter">
                        <i class="bi bi-x-circle-fill"></i> <span class="small ms-1">Reset</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- LIST VIEW PERUSAHAAN --}}
    <div class="d-flex flex-column gap-3">
        @forelse($perusahaans as $p)
            <div class="card border-0 shadow-sm rounded-3 list-hover-effect position-relative">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center g-3">
                        
                        {{-- Kolom Kiri: Identitas Perusahaan & Hashtags --}}
                        <div class="col-md-5">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <h5 class="fw-bold text-dark mb-0 text-truncate" title="{{ $p->nama_perusahaan }}" style="max-width: 250px;">
                                    {{ $p->nama_perusahaan }}
                                </h5>
                                
                                {{-- INDIKATOR PAID / UNPAID --}}
                                @if($p->has_paid)
                                    <span class="badge bg-success-soft text-success border border-success-subtle rounded-pill py-1 px-2" style="font-size: 0.65rem;">
                                        <i class="bi bi-cash-coin me-1"></i>Paid
                                    </span>
                                @endif
                                @if($p->has_unpaid && !$p->has_paid) 
                                    <span class="badge bg-secondary-soft text-secondary border border-secondary-subtle rounded-pill py-1 px-2" style="font-size: 0.65rem;">
                                        <i class="bi bi-wallet2 me-1"></i>Unpaid
                                    </span>
                                @endif
                            </div>
                            
                            <div class="d-flex align-items-start text-secondary small mb-3">
                                <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                <span class="line-clamp-2" title="{{ $p->alamat }}">{{ $p->alamat }}</span>
                            </div>
                        </div>

                        {{-- Kolom Tengah: Metrik --}}
                        <div class="col-md-4 border-start-md border-end-md px-md-4 py-2 py-md-0">
                            @if(in_array($role, ['kaprodi', 'admin', 'dosen']))
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center small border-bottom border-light pb-1">
                                        <span class="text-muted"><i class="bi bi-star-fill text-warning me-1"></i> Rata-rata Rating</span>
                                        <span class="fw-bold text-dark">
                                            @if($p->reviews_avg_rating)
                                                {{ number_format($p->reviews_avg_rating, 1) }} <span class="text-muted fw-normal">/ 5.0</span>
                                            @else
                                                <span class="text-muted fw-normal">Belum ada</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small border-bottom border-light pb-1">
                                        <span class="text-muted"><i class="bi bi-people-fill text-primary me-1"></i> Total Mahasiswa</span>
                                        <span class="fw-bold text-primary">{{ $p->total_alumni_count ?? 0 }} Orang</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-muted"><i class="bi bi-tags me-1"></i> Kategori</span>
                                        <span class="badge bg-primary-soft text-primary fw-medium text-truncate" style="max-width: 130px;" title="{{ $p->kategori_industri }}">
                                            {{ $p->kategori_industri ?? 'Umum' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex justify-content-between align-items-center small border-bottom border-light pb-1">
                                        <span class="text-muted"><i class="bi bi-star-fill text-warning me-1"></i> Rating</span>
                                        <span class="fw-bold text-dark">
                                            {{ $p->reviews_avg_rating ? number_format($p->reviews_avg_rating, 1) : '-' }} 
                                            <span class="text-muted fw-normal">({{ $p->reviews_count }} Ulasan)</span>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small border-bottom border-light pb-1">
                                        <span class="text-muted"><i class="bi bi-tags me-1"></i> Industri</span>
                                        <span class="badge bg-primary-soft text-primary fw-medium text-truncate" style="max-width: 130px;">
                                            {{ $p->kategori_industri ?? 'Umum' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="text-muted"><i class="bi bi-person-lines-fill me-1"></i> Jejak Mahasiswa</span>
                                        <span class="fw-medium text-dark">{{ $p->total_alumni_count ?? 0 }} Mhs Pernah Magang</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Kolom Kanan: Aksi --}}
                        <div class="col-md-3 text-md-end text-start mt-2 mt-md-0">
                            <a href="{{ route('perusahaan.show', $p->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 stretched-link group-hover-arrow w-100 w-md-auto fw-medium">
                                Lihat Profil <i class="bi bi-arrow-right ms-1 transition-arrow"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4 bg-light text-center py-5">
                <div class="card-body py-4">
                    <div class="mb-3 text-secondary opacity-50">
                        <i class="bi bi-search display-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Oops, Tidak Ada Hasil</h5>
                    <p class="text-muted mx-auto mb-4" style="max-width: 450px;">
                        Kami tidak menemukan perusahaan yang cocok dengan pencarian atau filter yang Anda terapkan.
                    </p>
                    <a href="{{ url()->current() }}" class="btn btn-outline-primary rounded-pill px-4 fw-medium">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($perusahaans->hasPages())
        <div class="d-flex justify-content-end mt-4 mb-4 custom-pagination">
            {{ $perusahaans->appends(request()->query())->links() }}
        </div>
    @endif

</div>

<style>
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.08); }
    .border-primary-subtle { border-color: rgba(13, 110, 253, 0.15) !important; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.12); }
    
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .border-success-subtle { border-color: rgba(25, 135, 84, 0.2) !important; }
    .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }
    .border-secondary-subtle { border-color: rgba(108, 117, 125, 0.2) !important; }

    .list-hover-effect { 
        transition: all 0.2s ease-in-out; 
        border: 1px solid transparent !important;
    }
    .list-hover-effect:hover { 
        transform: translateY(-2px); 
        border-color: rgba(1, 79, 49, 0.2) !important; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important; 
    }

    .transition-arrow { display: inline-block; transition: transform 0.2s ease-in-out; }
    .list-hover-effect:hover .transition-arrow { transform: translateX(4px); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .tag-hover { transition: all 0.2s; z-index: 2; position: relative; }
    .tag-hover:hover { background-color: #014f31 !important; color: white !important; cursor: pointer; }

    @media (min-width: 768px) {
        .border-start-md { border-left: 1px solid #e9ecef; }
        .border-end-md { border-right: 1px solid #e9ecef; }
        .w-md-auto { width: auto !important; }
    }
    
    /* Mengatur style border warna input agar senada */
    .form-control, .form-select, .input-group-text {
        border-color: #dee2e6;
    }
    input.form-control:focus, select.form-select:focus { 
        box-shadow: none; 
        border-color: #014f31; 
    }
</style>
@endsection