@extends('layouts.app')

@section('title', 'Detail SKP')

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ Auth::user()->role === 'kaprodi' ? route('kaprodi.skp') : route('admin.skp') }}" class="btn btn-outline-secondary btn-sm me-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h3 class="fw-bold mb-0">Detail SKP & Seminar</h3>
    </div>

    <div class="row">
        
        {{-- KOLOM KIRI: INFO & FILE --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-badge me-2"></i>Informasi Magang</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="30%" class="text-muted fw-bold text-uppercase small">Nama Mahasiswa</td>
                            <td class="fw-bold">{{ $magang->mahasiswa->user->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold text-uppercase small">NIM</td>
                            <td>{{ $magang->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold text-uppercase small">Perusahaan</td>
                            <td>{{ $magang->perusahaan->nama_perusahaan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold text-uppercase small">Status SKP</td>
                            <td>
                                @if($magang->status_skp == 'sudah')
                                    <span class="badge bg-success">Terbit</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Input</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-earmark-text me-2"></i>Laporan Seminar</h5>
                </div>
                <div class="card-body text-center p-5">
                    @if($magang->file_seminar)
                        <i class="bi bi-file-pdf text-danger" style="font-size: 4rem;"></i>
                        <p class="mt-3 fw-bold">{{ basename($magang->file_seminar) }}</p>
                        <a href="{{ asset('storage/' . $magang->file_seminar) }}" target="_blank" class="btn btn-success text-white px-4 shadow-sm">
                            <i class="bi bi-eye me-2"></i> Lihat Berkas
                        </a>
                    @else
                        <div class="alert alert-warning mb-0 border-warning">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Mahasiswa belum upload berkas.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: NILAI --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-success"><i class="bi bi-check-circle me-2"></i>Nilai Akhir Seminar</h5>
                </div>
                <div class="card-body text-center p-5">
                    
                    <p class="text-muted text-uppercase small fw-bold mb-1">Nilai SKP</p>
                    @if($magang->nilai_seminar)
                        <h1 class="display-1 fw-bold text-primary mb-3">{{ $magang->nilai_seminar }}</h1>
                        <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Tersimpan Otomatis</span>
                    @else
                        <h3 class="text-muted fst-italic py-4 mb-0">Belum Diinput</h3>
                        <p class="text-muted small mt-2">Nilai akan muncul otomatis setelah diinput oleh mahasiswa.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection