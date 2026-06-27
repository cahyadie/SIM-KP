@extends('layouts.app')

@section('title', 'Riwayat Magang')

@section('content')
<style>
    :root {
        --primary-color: #004b23;
        --primary-light: #e8f5e9;
        --primary-gradient: linear-gradient(135deg, #004b23, #007135);
        --dark-text: #111827;
        --muted-text: #6b7280;
    }

    .page-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .page-header {
        background: var(--primary-gradient);
        padding: 20px 24px;
    }

    .page-header h5 {
        color: white;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .page-header p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        padding: 16px;
    }

    .table-modern tbody td {
        border-top: 1px solid #f1f5f9;
        padding: 18px 16px;
        vertical-align: middle;
    }

    .table-modern tbody tr:hover {
        background-color: #fafafa;
    }

    .company-name {
        font-weight: 600;
        color: var(--dark-text);
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
    }

    .empty-state p {
        margin-top: 10px;
        color: var(--muted-text);
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-11 col-lg-11">

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="page-card card">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h5><i class="bi bi-clock-history me-2"></i>Riwayat Magang</h5>
                    <p>Daftar seluruh aktivitas magang Anda.</p>
                </div>
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
                    {{ $riwayat_magang->count() }} Data
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Perusahaan</th>
                                <th>Periode</th>
                                <th>Dosen Pembimbing</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat_magang as $m)
                                @php
                                    $hari_ini = \Carbon\Carbon::now()->startOfDay();
                                    $mulai = \Carbon\Carbon::parse($m->tanggal_mulai)->startOfDay();
                                    $selesai = \Carbon\Carbon::parse($m->tanggal_selesai)->startOfDay();

                                    if ($m->status_kegiatan == 'selesai') {
                                        $badgeColor = 'bg-secondary';
                                        $statusTxt = 'Selesai';
                                    } elseif ($m->status_kegiatan == 'skp') {
                                        $badgeColor = 'bg-warning text-dark';
                                        $statusTxt = 'Belum SKP';
                                    } elseif ($hari_ini->gte($mulai) && $hari_ini->lte($selesai)) {
                                        $badgeColor = 'bg-success';
                                        $statusTxt = 'Aktif';
                                    } elseif ($hari_ini->lt($mulai)) {
                                        $badgeColor = 'bg-info';
                                        $statusTxt = 'Belum Mulai';
                                    } else {
                                        $badgeColor = 'bg-secondary';
                                        $statusTxt = 'Selesai';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="company-name">{{ $m->perusahaan->nama_perusahaan }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ \Carbon\Carbon::parse($m->tanggal_mulai)->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">
                                            s/d {{ \Carbon\Carbon::parse($m->tanggal_selesai)->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ $m->dosen->name ?? 'Belum Ditentukan' }}
                                    </td>
                                    <td>
                                        <span class="badge-status {{ $badgeColor }}">{{ $statusTxt }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('mahasiswa.riwayat-magang.edit', $m->id) }}"
                                           class="btn btn-outline-primary btn-sm rounded-3 px-3 shadow-sm">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-folder-x"></i>
                                            <p>Belum ada riwayat magang.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection