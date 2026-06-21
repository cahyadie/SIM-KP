@extends('layouts.app')

@section('title', 'Daftar Logbook Mingguan')

@section('content')
    
    {{-- KIRI: Tombol Kembali --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted border-start ps-3">
                <i class="bi bi-building me-1"></i> {{ $magang->perusahaan->nama_perusahaan }}
            </small>
        </div>

        {{-- KANAN: Tombol Aksi --}}
        <div class="d-flex gap-2">
            <!-- <a href="{{ asset('templates/template_evaluasi_kp.docx') }}" class="btn btn-success shadow-sm" download>
                <i class="bi bi-file-earmark-word me-1"></i> Unduh Evaluasi
            </a> -->

            <a href="{{ route('logbook.create', $magang->id) }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Isi Logbook
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABEL LOGBOOK --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-4 py-3" width="20%">Minggu Ke</th>
                            <th width="30%">Periode</th>
                            <th width="25%">Status Pengisian</th>
                            <th class="text-center" width="25%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $log)
                            {{-- Baris Utama --}}
                            <tr class="bg-white">
                                <td class="ps-4 fw-bold text-primary">Minggu #{{ $log->minggu_ke }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ \Carbon\Carbon::parse($log->tgl_mulai)->format('d M Y') }}</span>
                                        <span class="text-muted small">s/d {{ \Carbon\Carbon::parse($log->tgl_selesai)->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php $count = is_array($log->isi_logbook) ? count($log->isi_logbook) : 0; @endphp
                                    @if($count > 0)
                                        <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill">
                                            <i class="bi bi-check-circle me-1"></i> {{ $count }} Hari Terisi
                                        </span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger px-3 py-2 rounded-pill">
                                            <i class="bi bi-exclamation-circle me-1"></i> Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary shadow-sm fw-bold px-3 rounded-3" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#detail{{ $log->id }}" aria-expanded="false">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>

                            {{-- Baris Detail (Collapse) --}}
                            <tr>
                                <td colspan="4" class="p-0 border-0">
                                    <div class="collapse bg-light" id="detail{{ $log->id }}">
                                        <div class="p-4">
                                            <h6 class="fw-bold mb-3 text-secondary">
                                                <i class="bi bi-list-task me-2"></i>Rincian Kegiatan Minggu Ke-{{ $log->minggu_ke }}
                                            </h6>

                                            {{-- MUNCULKAN KOMENTAR DOSEN JIKA ADA --}}
                                            @if($log->komentar_dosen)
                                                <div class="alert bg-primary-soft border-0 border-start border-4 border-primary shadow-sm mb-4 rounded-3 p-3">
                                                    <div class="d-flex">
                                                        <i class="bi bi-chat-quote-fill text-primary fs-4 me-3 mt-1"></i>
                                                        <div>
                                                            <h6 class="fw-bold text-primary mb-1">Komentar Pembimbing:</h6>
                                                            <p class="mb-0 small text-dark lh-base">{{ $log->komentar_dosen }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="card border-0 shadow-sm rounded-4">
                                                <div class="table-responsive">
                                                    <table class="table table-sm mb-0">
                                                        <thead class="table-light text-secondary">
                                                            <tr>
                                                                <th class="px-3 py-3" width="20%">Hari & Tanggal</th>
                                                                <th width="30%">Kegiatan</th>
                                                                <th width="25%">Masalah</th>
                                                                <th width="25%">Solusi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if($log->isi_logbook && count($log->isi_logbook) > 0)
                                                                @foreach($log->isi_logbook as $hariKey => $item)
                                                                    @php
                                                                        try {
                                                                            // Set locale ke Indonesia untuk nama hari/bulan
                                                                            \Carbon\Carbon::setLocale('id');
                                                                            // Cek apakah key-nya format tanggal (2026-05-09)
                                                                            $formattedHari = \Carbon\Carbon::parse($hariKey)->translatedFormat('l, d M Y');
                                                                        } catch(\Exception $e) {
                                                                            // Fallback ke teks lama jika isinya "Senin", "Selasa", dll
                                                                            $formattedHari = $hariKey; 
                                                                        }
                                                                    @endphp
                                                                    <tr>
                                                                        <td class="fw-bold px-3 text-capitalize py-3">{{ $formattedHari }}</td>
                                                                        <td class="py-3 text-dark">{{ $item['kegiatan'] ?? '-' }}</td>
                                                                        <td class="py-3 text-danger">{{ $item['permasalahan'] ?? '-' }}</td>
                                                                        <td class="py-3 text-success">{{ $item['solusi'] ?? '-' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="4" class="text-center py-4 text-muted fst-italic">
                                                                        Tidak ada data kegiatan untuk minggu ini.
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="bi bi-journal-x display-4 mb-3 opacity-25"></i>
                                        <h6 class="fw-bold">Belum ada logbook</h6>
                                        <p class="small mb-0">Silakan klik "Isi Logbook" untuk mulai mencatat aktivitas harian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-success-soft { background-color: #ecfdf5; color: #059669; }
        .bg-danger-soft { background-color: #fef2f2; color: #dc2626; }
        .bg-primary-soft { background-color: #e8f5e9; color: #004b23; }
        .table-hover tbody tr:hover { background-color: #f8fafc; transition: 0.2s; }
    </style>
@endsection