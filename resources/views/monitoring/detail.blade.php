@extends('layouts.app')

@section('title', 'Detail Logbook Mingguan')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-outline-secondary btn-sm me-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-0">Logbook: {{ $magang->mahasiswa->user->name }}</h4>
            <small class="text-muted">{{ $magang->perusahaan->nama_perusahaan }}</small>
        </div>
    </div>

    @forelse($magang->logbooks as $log)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Minggu Ke-{{ $log->minggu_ke }}</span>
                <small>{{ \Carbon\Carbon::parse($log->tgl_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($log->tgl_selesai)->format('d M Y') }}</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th width="15%">Hari</th>
                                <th width="30%">Kegiatan</th>
                                <th width="25%">Permasalahan</th>
                                <th width="30%">Solusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($log->isi_logbook as $hari => $data)
                            <tr>
                                <td class="fw-bold">{{ $hari }}</td>
                                <td class="text-start text-pre-wrap">{{ $data['kegiatan'] ?? '-' }}</td>
                                <td class="text-start text-pre-wrap text-danger">{{ $data['permasalahan'] ?? '-' }}</td>
                                <td class="text-start text-pre-wrap text-success">{{ $data['solusi'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <h5 class="text-muted">Belum ada logbook yang diisi.</h5>
        </div>
    @endforelse
</div>
@endsection