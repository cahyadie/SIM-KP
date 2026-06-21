@extends('layouts.app')

@section('title', $perusahaan->nama_perusahaan)

@section('styles')
    <style>
        .star-rating { direction: rtl; display: inline-flex; gap: 8px; justify-content: center; }
        .star-rating input { display: none; }
        .star-rating label { font-size: 2.2rem; color: #e2e8f0; cursor: pointer; transition: transform 0.2s, color 0.2s; }
        .star-rating input:checked~label { color: #fbbf24; }
        .star-rating label:hover, .star-rating label:hover~label { color: #fbbf24; transform: scale(1.1); }
        
        .avatar-circle { width: 48px; height: 48px; background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: 700; font-size: 1.2rem; flex-shrink: 0; }
        .bg-primary-soft { background-color: rgba(13, 110, 253, 0.08); }
        .bg-warning-soft { background-color: rgba(255, 193, 7, 0.12); }
        
        .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
        .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }

        .review-card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.05) !important; }
        .review-card:hover { border-color: rgba(13, 110, 253, 0.2) !important; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05) !important; }
    </style>
@endsection

@section('content')
<div class="container-fluid px-0">

    <div class="mb-4">
        <a href="{{ route('perusahaan.index') }}" class="btn btn-white border shadow-sm rounded-pill px-3 fw-medium text-secondary hover-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Direktori
        </a>
    </div>

    <div class="row g-4">
        
        {{-- SISI KIRI: PROFIL & FORM --}}
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    <div class="bg-primary-soft text-primary rounded-4 d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-1">{{ $perusahaan->nama_perusahaan }}</h4>
                    
                    {{-- INDIKATOR PAID / UNPAID --}}
                    <div class="d-flex justify-content-center align-items-center flex-wrap gap-2 mb-4">
                        <span class="text-muted small">{{ $perusahaan->kategori_industri ?? 'Industri Umum' }}</span>
                        
                        @if($perusahaan->has_paid || $perusahaan->has_unpaid)
                            <span class="text-muted small">•</span>
                        @endif

                        @if($perusahaan->has_paid)
                            <span class="badge bg-success-soft text-success border border-success border-opacity-25 rounded-pill">
                                <i class="bi bi-cash-coin me-1"></i> Paid
                            </span>
                        @endif
                        @if($perusahaan->has_unpaid && !$perusahaan->has_paid)
                            <span class="badge bg-secondary-soft text-secondary border border-secondary border-opacity-25 rounded-pill">
                                <i class="bi bi-wallet2 me-1"></i> Unpaid
                            </span>
                        @endif
                    </div>

                    {{-- Rating Box --}}
                    <div class="bg-warning-soft border border-warning border-opacity-25 rounded-4 p-3 mb-4 d-flex align-items-center justify-content-center gap-3">
                        <span class="fw-bold text-dark display-6 mb-0" style="line-height: 1;">{{ number_format($perusahaan->rata_rata_rating, 1) }}</span>
                        <div class="text-start">
                            <div class="text-warning fs-6 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= round($perusahaan->rata_rata_rating) ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <div class="text-muted small" style="font-size: 0.75rem;">Dari {{ $perusahaan->reviews->count() }} Ulasan</div>
                        </div>
                    </div>

                    <div class="text-start mb-4 bg-light p-3 rounded-4">
                        <label class="small text-muted fw-bold text-uppercase mb-2 d-block" style="letter-spacing: 0.5px;">Alamat Pusat</label>
                        <div class="d-flex text-secondary lh-base small">
                            <i class="bi bi-geo-alt text-danger me-2 mt-1"></i> 
                            <span>{{ $perusahaan->alamat }}</span>
                        </div>
                    </div>

                    <div class="text-start border-top pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Statistik Magang</h6>
                                <small class="text-muted">Total mahasiswa magang</small>
                            </div>
                            <div class="bg-primary-soft text-primary fw-bold rounded-pill px-3 py-2 fs-5">
                                {{ $perusahaan->magangs->count() }} <i class="bi bi-people-fill ms-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($bisa_review)
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3 text-primary bg-primary-soft rounded-circle d-inline-flex p-3">
                            <i class="bi bi-chat-square-heart fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-2 text-dark">Tulis Ulasanmu</h5>
                        <p class="small text-muted mb-4">Pengalamanmu sangat berharga bagi mahasiswa lain yang ingin mendaftar di sini.</p>

                        <form action="{{ route('perusahaan.review', $perusahaan->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <span class="d-block text-dark small fw-bold mb-2">Berapa rating untuk tempat ini?</span>
                                <div class="star-rating bg-light py-2 rounded-pill w-100">
                                    <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="Sangat Puas">★</label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="Puas">★</label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="Cukup">★</label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="Kurang">★</label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="Sangat Kurang">★</label>
                                </div>
                            </div>
                            
                            <div class="mb-4 text-start">
                                <label class="small fw-bold text-dark mb-2">Detail Pengalaman</label>
                                <textarea name="komentar" class="form-control bg-light border-0 p-3" rows="4"
                                    placeholder="Ceritakan lingkungan kerja, mentor, atau hal yang paling kamu sukai..."
                                    required style="border-radius: 12px; font-size: 0.95rem; resize: none;"></textarea>
                            </div>
                            
                            <button class="btn btn-primary fw-bold w-100 rounded-pill py-2 shadow-sm">
                                Kirim Ulasan <i class="bi bi-send ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- SISI KANAN: LIST ULASAN --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-4 mb-4 gap-3">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                Ulasan Mahasiswa <span class="badge bg-primary-soft text-primary ms-2 rounded-pill">{{ $perusahaan->reviews->count() }}</span>
                            </h5>
                            <p class="text-muted small mb-0">Baca apa kata mereka yang pernah magang di sini.</p>
                        </div>
                        
                        <div class="dropdown flex-shrink-0">
                            <button class="btn btn-light border bg-white rounded-pill px-3 py-2 text-secondary shadow-sm dropdown-toggle small fw-medium" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-filter me-1 text-primary"></i> 
                                {{ $sort == 'tertinggi' ? 'Rating Tertinggi' : ($sort == 'terendah' ? 'Rating Terendah' : 'Terbaru') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3">
                                <li><a class="dropdown-item py-2 {{ $sort == 'terbaru' ? 'active bg-primary text-white' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}">Paling Baru</a></li>
                                <li><a class="dropdown-item py-2 {{ $sort == 'tertinggi' ? 'active bg-primary text-white' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'tertinggi']) }}">Rating Tertinggi</a></li>
                                <li><a class="dropdown-item py-2 {{ $sort == 'terendah' ? 'active bg-primary text-white' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'terendah']) }}">Rating Terendah</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        @forelse($perusahaan->reviews as $review)
                            <div class="card review-card shadow-none bg-white rounded-4 border">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3 bg-light text-secondary border">
                                                {{ strtoupper(substr($review->mahasiswa->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">{{ $review->mahasiswa->user->name }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="text-warning" style="font-size: 0.85rem;">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <span class="text-muted small px-1">•</span>
                                                    <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(Auth::user()->role != 'mahasiswa')
                                            <form action="{{ route('admin.review.destroy', $review->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-light btn-sm text-danger rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0;" title="Hapus Ulasan dari Sistem">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="bg-primary-soft p-3 rounded-3 mt-3">
                                        <p class="mb-0 text-secondary" style="line-height: 1.7; font-size: 0.95rem;">
                                            "{{ $review->komentar }}"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 my-4">
                                <div class="mb-3 d-inline-flex bg-light rounded-circle p-4">
                                    <i class="bi bi-chat-square-quote text-muted opacity-50" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="text-dark fw-bold mb-2">Belum Ada Ulasan</h5>
                                <p class="text-secondary small mx-auto" style="max-width: 300px;">Perusahaan ini belum memiliki ulasan. Jadilah yang pertama membagikan pengalamanmu!</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection