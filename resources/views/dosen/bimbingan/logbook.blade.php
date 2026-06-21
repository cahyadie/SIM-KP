@extends('layouts.app')

@section('title', 'Evaluasi Logbook Mahasiswa')

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- NAVIGASI ATAS --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-outline-secondary btn-sm shadow-xs rounded-3 px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <div>
                <h5 class="mb-0 fw-bold text-dark">{{ $magang->mahasiswa->user->name }}</h5>
                <span class="text-muted small">NIM: <strong class="text-secondary">{{ $magang->mahasiswa->nim }}</strong></span>
            </div>
        </div>
        <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-3 fw-medium">
            <i class="bi bi-journal-check me-1"></i> Evaluasi Logbook Magang
        </span>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs rounded-3 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill text-success me-2"></i> 
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KONTEN UTAMA --}}
    <div class="row justify-content-center">
        <div class="col-lg-12 col-xl-11">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-secondary mb-0">Riwayat Aktivitas Mingguan</h6>
                <span class="badge bg-light text-dark border fw-normal px-2 py-1">{{ $magang->logbooks->count() }} Minggu Tercatat</span>
            </div>

            @if($magang->logbooks->count() > 0)
                <div class="accordion custom-accordion" id="accordionLogbook">
                    @foreach($magang->logbooks as $index => $log)
                        <div class="accordion-item border-0 rounded-4 mb-3 shadow-xs bg-white overflow-hidden">
                            
                            {{-- HEADER ACCORDION --}}
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }} p-3 p-md-4" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $log->id }}">
                                    <div class="d-flex align-items-center justify-content-between w-100 pe-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="minggu-badge">
                                                <span class="d-block text-uppercase fw-semibold text-primary" style="font-size: 0.65rem;">Week</span>
                                                <span class="d-block fs-4 fw-bold text-primary">{{ $log->minggu_ke }}</span>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">Minggu ke-{{ $log->minggu_ke }}</h6>
                                                <span class="small text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ \Carbon\Carbon::parse($log->tgl_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($log->tgl_selesai)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Status Indikator --}}
                                        <div>
                                            @if($log->status_acc)
                                                <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fw-medium border border-success-subtle">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                                                </span>
                                            @else
                                                <span class="badge bg-warning-soft text-warning px-3 py-1 rounded-pill fw-medium border border-warning-subtle">
                                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu ACC
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </h2>

                            {{-- BODY ACCORDION --}}
                            <div id="collapse{{ $log->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#accordionLogbook">
                                <div class="accordion-body p-4 border-top bg-light-soft">
                                    
                                    {{-- GRID HARIAN (2 KOLOM BERSAMPINGAN) --}}
                                    <div class="row g-3">
                                        @foreach($log->isi_logbook as $hari => $data)
                                            @php
                                                try {
                                                    \Carbon\Carbon::setLocale('id');
                                                    $formattedHari = \Carbon\Carbon::parse($hari)->translatedFormat('l, d M Y');
                                                } catch(\Exception $e) {
                                                    $formattedHari = $hari;
                                                }
                                                
                                                $kegiatan = $data['kegiatan'] ?? '-';
                                                $kendala = $data['permasalahan'] ?? '-';
                                                $solusi = $data['solusi'] ?? '-';
                                            @endphp

                                            {{-- Gunakan col-md-6 agar bersampingan di layar medium ke atas --}}
                                            <div class="col-md-6">
                                                <div class="daily-log-card bg-white p-3 rounded-4 shadow-xs border border-light h-100 d-flex flex-column">
                                                    
                                                    {{-- Judul Hari --}}
                                                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-calendar2-event text-primary me-2"></i>
                                                            <span class="fw-bold text-dark small text-capitalize">{{ $formattedHari }}</span>
                                                        </div>
                                                        <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">Logbook</span>
                                                    </div>

                                                    {{-- Rincian Kegiatan --}}
                                                    <div class="log-content small flex-grow-1 mb-2">
                                                        <span class="text-muted d-block fw-semibold mb-1" style="font-size:0.75rem;">
                                                             Rincian Kegiatan:
                                                        </span>
                                                        <p class="text-secondary mb-0 ps-2 border-2 border-primary lh-base">
                                                            {!! nl2br(e($kegiatan)) !!}
                                                        </p>
                                                    </div>

                                                    {{-- Seksi Kendala & Solusi (Ditumpuk vertikal karena lebar card sempit) --}}
                                                    @if($kendala !== '-' || $solusi !== '-')
                                                        <div class="d-flex flex-column gap-2 mt-auto pt-2 border-top">
                                                            @if($kendala !== '-')
                                                                <div class="p-2  rounded-3 border border-opacity-10">
                                                                    <span class="d-block fw-semibold mb-1" style="font-size:0.7rem;">
                                                                         Kendala:
                                                                    </span>
                                                                    <p class="text-danger-emphasis mb-0 small">{{ $kendala }}</p>
                                                                </div>
                                                            @endif

                                                            @if($solusi !== '-')
                                                                <div class="p-2 rounded-3 border  border-opacity-10">
                                                                    <span class="d-block fw-semibold mb-1" style="font-size:0.7rem;">
                                                                        Solusi:
                                                                    </span>
                                                                    <p class="text-success-emphasis mb-0 small">{{ $solusi }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- FORM KOMENTAR & ACC DOSEN --}}
                                    <div class="mt-4 bg-white p-4 rounded-4 border shadow-xs">
                                        <form action="{{ route('dosen.bimbingan.logbook.review', $log->id) }}" method="POST">
                                            @csrf
                                            <label class="fw-bold small text-dark mb-2">
                                                <i class="bi bi-chat-square-text text-primary me-1"></i> Catatan & Evaluasi Pembimbing
                                            </label>
                                            
                                            <textarea name="komentar_dosen" class="form-control bg-light-soft mb-3 text-dark" rows="3" placeholder="Tuliskan catatan, revisi, atau umpan balik untuk logbook minggu ini..." {{ $log->status_acc ? 'readonly' : '' }}>{{ $log->komentar_dosen }}</textarea>
                                            
                                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                                <small class="text-muted fst-italic" style="font-size:0.75rem;">
                                                    * Catatan ini akan langsung terlihat di dasbor mahasiswa.
                                                </small>
                                                
                                                @if($log->status_acc)
                                                    <button type="button" class="btn btn-light text-success fw-medium btn-sm px-3 py-2" disabled>
                                                        <i class="bi bi-check-all me-1"></i> ACC Selesai
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-primary btn-sm fw-medium shadow-xs px-4 py-2">
                                                        <i class="bi bi-check-circle me-1"></i> Simpan & Setujui (ACC)
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- STATE KOSONG --}}
                <div class="card border-0 shadow-xs rounded-4 py-5 text-center bg-white">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-journal-x display-3 text-muted opacity-25"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Belum Ada Catatan Logbook</h6>
                        <p class="text-muted small mb-0 max-w-sm mx-auto">Mahasiswa ini belum mengisi laporan aktivitas mingguan magang mereka.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    /* Kustomisasi Warna Background Subtil */
    .bg-light-soft { background-color: #f8fafc; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-success-soft { background-color: #f0fdf4; }
    .bg-danger-soft { background-color: #fef2f2; }
    .bg-warning-soft { background-color: #fffbeb; }

    /* Utilitas Visual */
    .shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    
    /* Kustomisasi Accordion */
    .custom-accordion .accordion-button {
        transition: background-color 0.2s ease;
    }
    .custom-accordion .accordion-button:not(.collapsed) {
        background-color: #ffffff;
        color: inherit;
        box-shadow: none;
    }
    .custom-accordion .accordion-button:focus { 
        box-shadow: none; 
    }
    
    /* Badge Minggu di Header Accordion */
    .minggu-badge {
        background-color: #f8fafc;
        border-radius: 10px;
        padding: 8px 16px;
        text-align: center;
        min-width: 70px;
    }

    /* Animasi Hover Card Harian */
    .daily-log-card { 
        transition: all 0.2s ease; 
    }
    .daily-log-card:hover { 
        border-color: #cbd5e1 !important; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endsection