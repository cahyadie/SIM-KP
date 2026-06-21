@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="container-fluid px-4">
    {{-- NAVIGASI ATAS --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-medium">
            <i class="bi bi-info-circle me-1"></i> Informasi Magang Mahasiswa
        </span>
    </div>

    <div class="row g-4 justify-content-center">
        {{-- SISI KIRI: PROFIL & DATA MAGANG --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="bg-primary py-4 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: 90px; height: 90px;">
                            <i class="bi bi-person-fill text-primary display-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-white mt-3 mb-0">{{ $magang->mahasiswa->user->name }}</h5>
                    <small class="text-white-50">{{ $magang->mahasiswa->nim }}</small>
                </div>
                <div class="card-body p-4 pt-3">
                    <div class="mb-4">
                        <label class="small text-muted fw-bold text-uppercase opacity-75 style-label">Data Mahasiswa</label>
                        
                        {{-- Program Studi --}}
                        <div class="d-flex align-items-center mt-2 p-2 bg-light rounded-3 mb-2">
                            <i class="bi bi-mortarboard text-primary fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block">Program Studi</small>
                                <span class="fw-bold small">{{ $magang->mahasiswa->prodi }}</span>
                            </div>
                        </div>

                        {{-- Kontak WhatsApp --}}
                        <div class="d-flex align-items-center p-2 bg-light rounded-3">
                            <i class="bi bi-whatsapp text-success fs-4 me-3"></i>
                            <div>
                                <small class="text-muted d-block">No. WhatsApp</small>
                                @if($magang->mahasiswa->no_hp)
                                    @php
                                        $waLink = preg_replace('/[^0-9]/', '', $magang->mahasiswa->no_hp);
                                        if(substr($waLink, 0, 1) == '0') {
                                            $waLink = '62' . substr($waLink, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $waLink }}" target="_blank" class="fw-bold small text-success text-decoration-none">
                                        {{ $magang->mahasiswa->no_hp }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 0.7rem;"></i>
                                    </a>
                                @else
                                    <span class="small fst-italic text-muted">Belum diisi</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="opacity-10">

                    <div class="mb-3">
                        <label class="small text-muted fw-bold text-uppercase opacity-75 style-label">Detail Magang</label>
                        <div class="mt-2">
                            <div class="d-flex mb-3">
                                <i class="bi bi-building text-primary me-3 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Perusahaan</small>
                                    <span class="fw-bold small">{{ $magang->perusahaan->nama_perusahaan }}</span>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-tag-fill text-primary me-3 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Tema / Topik Magang</small>
                                    <span class="fw-bold small text-dark">{{ $magang->tema_magang ?? 'Belum ditentukan' }}</span>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-geo-alt text-primary me-3 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Alamat</small>
                                    <span class="text-muted small lh-sm d-block">{{ $magang->perusahaan->alamat }}</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="bi bi-calendar-range text-primary me-3 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Periode Magang</small>
                                    <span class="fw-bold small">
                                        {{ \Carbon\Carbon::parse($magang->tanggal_mulai)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($magang->tanggal_selesai)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: BERKAS AKHIR --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-files me-2 text-primary"></i>Dokumen Akhir</h6>
                </div>
                <div class="list-group list-group-flush">
                    {{-- Laporan --}}
                    @if($magang->file_seminar)
                        <a href="{{ asset('storage/'.$magang->file_seminar) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-light">
                            <div class="icon-box bg-danger-soft text-danger rounded-3 p-2 me-3">
                                <i class="bi bi-file-earmark-pdf fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="d-block small fw-bold">Laporan Akhir KP</span>
                                <small class="text-muted">Klik untuk melihat file</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    @else
                        <div class="list-group-item py-3 border-light d-flex align-items-center opacity-50">
                            <i class="bi bi-file-earmark-pdf text-muted fs-4 me-3"></i>
                            <span class="small fst-italic">Laporan belum diupload</span>
                        </div>
                    @endif

                    {{-- Nilai Lapangan --}}
                    @if($magang->file_nilai_lapangan ?? false)
                        <a href="{{ asset('storage/'.$magang->file_nilai_lapangan) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-light">
                            <div class="icon-box bg-primary-soft text-primary rounded-3 p-2 me-3">
                                <i class="bi bi-file-earmark-text fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="d-block small fw-bold">Nilai dari Instansi</span>
                                <small class="text-muted">Klik untuk melihat file</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    @else
                        <div class="list-group-item py-3 border-light d-flex align-items-center opacity-50">
                            <i class="bi bi-file-earmark-text text-muted fs-4 me-3"></i>
                            <span class="small fst-italic">Nilai belum diupload</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-soft { background-color: #f8fafc; }
    .bg-primary-soft { background-color: #eff6ff; }
    .bg-danger-soft { background-color: #fef2f2; }
    .style-label { font-size: 0.7rem; letter-spacing: 0.5px; }
    .icon-box { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; }
</style>
@endsection