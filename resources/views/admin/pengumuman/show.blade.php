@extends('layouts.app')

@section('title', 'Detail Lowongan Magang')

@section('content')
{{-- Tambahkan style tinggi calc(100vh - 100px) untuk memotong tinggi navbar atas --}}
<div class="container-fluid px-0 d-flex flex-column" style="height: calc(100vh - 90px);">
    
    {{-- Tombol Kembali --}}
    <div class="mb-3 flex-shrink-0">
        <a href="{{ url()->previous() }}" class="btn btn-white border shadow-sm rounded-pill px-3 fw-medium text-secondary hover-primary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    {{-- Wrapper flex agar card mengisi sisa tinggi layar --}}
    <div class="row justify-content-center flex-grow-1 overflow-hidden m-0">
        <div class="col-lg-8 d-flex flex-column h-100 px-0 px-md-3">
            
            {{-- Card di-set h-100 dan d-flex flex-column --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column h-100">
                
                {{-- 1. Bagian Header / Cover (Flex Shrink 0 agar tidak menyusut) --}}
                <div class="bg-primary-soft p-4 text-center position-relative flex-shrink-0">
                    <div class="text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 30px; height: 30px;">
                        <i class="bi bi-briefcase-fill fs-3"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $pengumuman->judul }}</h4>
                </div>

                {{-- 2. Bagian Body / Konten (Overflow Auto agar bagian ini saja yang bisa di-scroll) --}}
                <div class="card-body p-4 overflow-auto custom-scrollbar">
                    
                    {{-- Highlight Info Singkat --}}
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4 border-bottom pb-3">
                        @if($pengumuman->lokasi)
                            <div class="text-center px-3">
                                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Lokasi</div>
                                <div class="fw-medium text-dark small"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $pengumuman->lokasi }}</div>
                            </div>
                        @endif
                        <div class="text-center px-3 border-start border-end">
                            <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Target Angkatan</div>
                            <div class="fw-medium text-dark small"><i class="bi bi-people text-info me-1"></i> {{ $pengumuman->target_angkatan ?? 'Semua' }}</div>
                        </div>
                        <div class="text-center px-3">
                            <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Diposting</div>
                            <div class="fw-medium text-dark small"><i class="bi bi-calendar-event text-warning me-1"></i> {{ $pengumuman->created_at->format('d M Y') }}</div>
                        </div>
                    </div>

                    {{-- Deskripsi Penuh --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-card-text text-primary me-2"></i>Deskripsi Lowongan</h6>
                        <div class="text-secondary" style="line-height: 1.7; font-size: 0.95rem;">
                            {!! nl2br(e($pengumuman->deskripsi)) !!}
                        </div>
                    </div>

                    {{-- Informasi Tambahan (Selalu tampil karena status gaji selalu ada) --}}
                    <div class="bg-light p-3 rounded-4 border">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Tambahan</h6>
                        <div class="row g-3">
                            
                            {{-- Info Gaji --}}
                            <div class="col-md-6">
                                <div class="text-muted" style="font-size: 0.8rem;">Info Gaji / Honorarium</div>
                                @if(!empty($pengumuman->info_gaji) && strtolower($pengumuman->info_gaji) !== 'unpaid')
                                    <div class="fw-medium text-success small">
                                        <i class="bi bi-cash-stack me-2"></i> 
                                        {{-- Menampilkan "Paid (Berbayar)" atau nilai teks lama jika ada data lama --}}
                                        {{ strtolower($pengumuman->info_gaji) == 'paid' ? 'Paid (Berbayar)' : 'Paid (' . $pengumuman->info_gaji . ')' }}
                                    </div>
                                @else
                                    <div class="fw-medium text-secondary small">
                                        <i class="bi bi-dash-circle me-2"></i> Unpaid (Tidak ada gaji)
                                    </div>
                                @endif
                            </div>

                            @if($pengumuman->info_fasilitas)
                                <div class="col-md-6">
                                    <div class="text-muted" style="font-size: 0.8rem;">Fasilitas Tambahan</div>
                                    <div class="fw-medium text-dark small"><i class="bi bi-house-door text-primary me-2"></i>{{ $pengumuman->info_fasilitas }}</div>
                                </div>
                            @endif

                            @if($pengumuman->syarat_tambahan)
                                <div class="col-12">
                                    <div class="text-muted" style="font-size: 0.8rem;">Syarat Khusus</div>
                                    <div class="fw-medium text-dark small"><i class="bi bi-check2-square text-warning me-2"></i>{{ $pengumuman->syarat_tambahan }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- 3. Card Footer / Tombol Action (Menempel di bawah) --}}
                <div class="card-footer bg-white border-top p-3 flex-shrink-0">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 px-2">
                        <a href="{{ $pengumuman->link_pendaftaran }}" target="_blank" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold w-100 w-md-auto">
                            <i class="bi bi-box-arrow-up-right me-2"></i> Daftar Sekarang
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.05); }
    .hover-primary:hover { color: #0d6efd !important; border-color: #0d6efd !important; }
    
    /* Styling agar scrollbar di dalam card terlihat elegan */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f8f9fa; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #dee2e6; 
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #adb5bd; 
    }
</style>
@endsection