@extends('layouts.app')

@section('title', 'Evaluasi Logbook Mahasiswa')

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- NAVIGASI & HEADER --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-white border shadow-sm rounded-3 px-3 py-2 text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <div class="ms-1">
                <h4 class="mb-0 fw-bold text-dark">{{ $magang->mahasiswa->user->name }}</h4>
                <p class="text-muted small mb-0 tracking-wide">NIM: <span class="fw-semibold text-dark">{{ $magang->mahasiswa->nim }}</span></p>
            </div>
        </div>
        <span class="badge bg-primary-soft text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-medium fs-6">
            <i class="bi bi-journal-text me-1"></i> Evaluasi Logbook
        </span>
    </div>

    {{-- ALERT SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center p-3 flash-alert" role="alert">
            <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i> 
            <div class="fw-medium text-dark">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KONTEN UTAMA --}}
    <div class="row justify-content-center">
        <div class="col-lg-12">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-dark mb-0">Riwayat Aktivitas Mingguan</h5>
                <span class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-pill fw-medium">
                    Total: {{ $magang->logbooks->count() }} Minggu
                </span>
            </div>

            @if($magang->logbooks->count() > 0)
                <div class="accordion custom-accordion d-flex flex-column gap-3" id="accordionLogbook">
                    @foreach($magang->logbooks as $index => $log)
                        <div class="accordion-item border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                            
                            {{-- HEADER ACCORDION --}}
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }} p-3 p-md-4 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $log->id }}">
                                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between w-100 pe-3 gap-3">
                                        
                                        {{-- Info Minggu --}}
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="minggu-badge bg-primary-soft text-primary rounded-3 d-flex flex-column align-items-center justify-content-center p-2 shadow-sm" style="min-width: 75px;">
                                                <span class="small text-uppercase fw-bold tracking-wide" style="font-size: 0.65rem;">Week</span>
                                                <span class="fs-4 fw-black lh-1">{{ $log->minggu_ke }}</span>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark fs-5">Minggu ke-{{ $log->minggu_ke }}</h6>
                                                <span class="small text-muted fw-medium">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ \Carbon\Carbon::parse($log->tgl_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($log->tgl_selesai)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Status Indikator --}}
                                        <div>
                                            @if($log->status_acc)
                                                <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-bold border border-success-subtle">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Selsai Diperiksa
                                                </span>
                                            @else
                                                <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill fw-bold border border-warning-subtle">
                                                    <i class="bi bi-hourglass-split me-1"></i> Belum Diperiksa
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                </button>
                            </h2>

                            {{-- BODY ACCORDION --}}
                            <div id="collapse{{ $log->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#accordionLogbook">
                                <div class="accordion-body p-4 border-top bg-light-soft">
                                    
                                    {{-- TABEL HARIAN --}}
                                    <div class="table-responsive bg-white rounded-4 shadow-xs border mb-4">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" class="py-3 px-4 text-uppercase tracking-wide text-muted small fw-bold border-bottom" style="width: 20%; min-width: 150px;">Hari / Tanggal</th>
                                                    <th scope="col" class="py-3 px-4 text-uppercase tracking-wide text-muted small fw-bold border-bottom" style="width: 45%; min-width: 300px;">Rincian Kegiatan</th>
                                                    <th scope="col" class="py-3 px-4 text-uppercase tracking-wide text-muted small fw-bold border-bottom" style="width: 35%; min-width: 250px;">Kendala & Solusi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="border-top-0">
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
                                                    <tr>
                                                        {{-- Kolom Hari --}}
                                                        <td class="py-3 px-4 align-top border-bottom-0 border-end border-light">
                                                            <div class="d-flex align-items-center">
                                                                <div class="bg-primary-soft p-2 rounded-2 me-3 text-primary d-none d-sm-block">
                                                                    <i class="bi bi-calendar-day"></i>
                                                                </div>
                                                                <span class="fw-bold text-dark text-capitalize">{{ $formattedHari }}</span>
                                                            </div>
                                                        </td>
                                                        
                                                        {{-- Kolom Kegiatan --}}
                                                        <td class="py-3 px-4 align-top border-bottom-0 border-end border-light">
                                                            <p class="text-dark mb-0 lh-base" style="font-size: 0.9rem;">
                                                                {!! nl2br(e($kegiatan)) !!}
                                                            </p>
                                                        </td>
                                                        
                                                        {{-- Kolom Kendala & Solusi --}}
                                                        <td class="py-3 px-4 align-top border-bottom-0">
                                                            @if($kendala !== '-' || $solusi !== '-')
                                                                <div class="d-flex flex-column gap-2">
                                                                    @if($kendala !== '-')
                                                                        <div class="bg-danger-soft p-2 rounded-3 border border-danger-subtle">
                                                                            <div class="d-flex align-items-center mb-1">
                                                                                <i class="bi bi-exclamation-triangle-fill text-danger me-2" style="font-size: 0.75rem;"></i>
                                                                                <span class="fw-bold text-danger text-uppercase tracking-wide" style="font-size: 0.7rem;">Kendala</span>
                                                                            </div>
                                                                            <p class="text-dark mb-0 ms-4 lh-sm" style="font-size: 0.85rem;">{{ $kendala }}</p>
                                                                        </div>
                                                                    @endif

                                                                    @if($solusi !== '-')
                                                                        <div class="bg-success-soft p-2 rounded-3 border border-success-subtle">
                                                                            <div class="d-flex align-items-center mb-1">
                                                                                <i class="bi bi-lightbulb-fill text-success me-2" style="font-size: 0.75rem;"></i>
                                                                                <span class="fw-bold text-success text-uppercase tracking-wide" style="font-size: 0.7rem;">Solusi</span>
                                                                            </div>
                                                                            <p class="text-dark mb-0 ms-4 lh-sm" style="font-size: 0.85rem;">{{ $solusi }}</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted fst-italic small"><i class="bi bi-check2 me-1"></i>Tidak ada kendala tercatat</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    
                                                    {{-- Pemisah antar baris tabel untuk estetika --}}
                                                    @if(!$loop->last)
                                                    <tr>
                                                        <td colspan="3" class="p-0 border-bottom border-light"></td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- FORM KOMENTAR & ACC DOSEN --}}
                                    <div class="card border border-primary-subtle shadow-sm rounded-4 bg-white mt-2">
                                        <div class="card-body p-4">
                                            <form action="{{ route('dosen.bimbingan.logbook.review', $log->id) }}" method="POST">
                                                @csrf
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-chat-dots-fill"></i>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-0">Catatan & Evaluasi Pembimbing</h6>
                                                </div>
                                                
                                                <textarea name="komentar_dosen" class="form-control bg-light-soft border-light mb-3 text-dark p-3 rounded-3" rows="3" placeholder="Tuliskan catatan, revisi, atau umpan balik untuk logbook minggu ini..." {{ $log->status_acc ? 'readonly' : '' }}>{{ $log->komentar_dosen }}</textarea>
                                                
                                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 bg-light-soft p-3 rounded-3">
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="bi bi-info-circle me-2"></i>
                                                        <span>Catatan ini akan langsung terlihat di dasbor mahasiswa.</span>
                                                    </div>
                                                    
                                                    @if($log->status_acc)
                                                        <button type="button" class="btn btn-success bg-success text-white fw-bold px-4 py-2 rounded-3 shadow-sm" disabled>
                                                            <i class="bi bi-check-all me-2"></i> Smpan
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm hover-elevate">
                                                            <i class="bi bi-check-circle me-2"></i> Simpan 
                                                        </button>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- STATE KOSONG --}}
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center bg-white mt-4">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <div class="bg-light-soft d-inline-block p-4 rounded-circle">
                                <i class="bi bi-journal-x display-4 text-muted"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark">Belum Ada Catatan Logbook</h5>
                        <p class="text-muted mb-0 max-w-sm mx-auto">Mahasiswa ini belum mengisi laporan aktivitas mingguan magang mereka. Silakan periksa kembali nanti.</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<style>
    /* Palet Warna Subtil */
    .bg-light-soft { background-color: #f8fafc; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-success-soft { background-color: #f0fdf4; }
    .bg-danger-soft { background-color: #fef2f2; }
    .bg-warning-soft { background-color: #fffbeb; }

    /* Tipografi & Spacing */
    .tracking-wide { letter-spacing: 0.5px; }
    .fw-black { font-weight: 900; }
    
    /* Utilitas Visual */
    .shadow-xs { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-elevate:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    
    /* Kustomisasi Accordion Bootstrap */
    .custom-accordion .accordion-button:not(.collapsed) {
        background-color: transparent;
        color: inherit;
        box-shadow: none;
    }
    .custom-accordion .accordion-button:focus { 
        box-shadow: none; 
        border-color: rgba(0,0,0,.125);
    }
    .custom-accordion .accordion-item {
        border: 1px solid #e2e8f0 !important;
    }

    /* Kustomisasi Tabel */
    .table-responsive {
        border-radius: 0.75rem;
    }
    .table-hover tbody tr:hover td {
        background-color: #f8fafc;
    }
</style>
@endsection