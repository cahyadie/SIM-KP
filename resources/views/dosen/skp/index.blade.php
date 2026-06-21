@extends('layouts.app')

@section('title', 'Jadwal SKP Mahasiswa')

@section('content')

    <style>
        :root {
            --primary-color: #004b23;
            --primary-light: #e8f5e9;
            --dark-text: #111827;
            --muted-text: #6b7280;
        }

        body {
            background-color: #f8fafc;
        }

        .dashboard-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        }

        .table-modern thead th {
            background: #f8fafc;
            border: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            padding: 16px;
            text-transform: uppercase;
        }

        .table-modern tbody td {
            border-top: 1px solid #f1f5f9;
            padding: 18px 16px;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background-color: #fafafa;
            transition: 0.2s;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 3.5rem;
            color: #cbd5e1;
        }

        .badge-custom {
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .bg-warning-soft {
            background: #fff7ed;
            color: #ea580c;
        }

        .bg-success-soft {
            background: #ecfdf3;
            color: #16a34a;
        }

        .bg-danger-soft {
            background: #fef2f2;
            color: #dc2626;
        }

        .bg-secondary-soft {
            background: #f1f5f9;
            color: #475569;
        }
    </style>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- MAIN CARD --}}
    <div class="dashboard-card card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Tempat Magang</th>
                            <th>Status Pengajuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuanSkp as $skp)
                            @php
                                // Check if internship date has passed
                                $isSelesaiMagang = \Carbon\Carbon::parse($skp->tanggal_selesai)->isPast();
                            @endphp
                            <tr>
                                {{-- KOLOM MAHASISWA --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-light text-primary rounded-circle d-flex justify-content-center align-items-center fw-bold me-3"
                                            style="width: 45px; height: 45px;">
                                            {{ substr($skp->mahasiswa->user->name ?? 'M', 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0" style="color: var(--dark-text);">
                                                {{ $skp->mahasiswa->user->name ?? 'Nama Tidak Ditemukan' }}
                                            </h6>
                                            <small class="text-muted">NIM: {{ $skp->mahasiswa->nim }}</small>
                                        </div>
                                    </div>
                                </td>

                                {{-- KOLOM PERUSAHAAN --}}
                                <td>
                                    <div class="fw-semibold" style="color: var(--dark-text);">
                                        {{ $skp->perusahaan->nama_perusahaan ?? '-' }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ \Carbon\Carbon::parse($skp->tanggal_mulai)->format('M Y') }} -
                                        {{ \Carbon\Carbon::parse($skp->tanggal_selesai)->format('M Y') }}
                                    </small>
                                </td>

                                {{-- KOLOM STATUS --}}
                                <td>
                                    @if($skp->status_jadwal_skp == 'menunggu')
                                        <span class="badge-custom bg-warning-soft d-inline-flex align-items-center">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Respon
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 11px;">Butuh konfirmasi Anda</div>

                                    @elseif($skp->status_jadwal_skp == 'disetujui')
                                        <span class="badge-custom bg-success-soft d-inline-flex align-items-center">
                                            <i class="bi bi-check-circle me-1"></i> Disetujui
                                        </span>
                                        <div class="small fw-bold text-success mt-1" style="font-size: 12px;">
                                            {{ \Carbon\Carbon::parse($skp->jadwal_terpilih)->format('d M Y, H:i') }}
                                        </div>
                                        <!-- <div class="small text-danger fw-bold" style="font-size: 11px;">
                                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $skp->ruangan_skp }}
                                                    </div> -->

                                    @elseif($skp->status_jadwal_skp == 'ditolak')
                                        <span class="badge-custom bg-danger-soft d-inline-flex align-items-center">
                                            <i class="bi bi-x-circle me-1"></i> Ditolak
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 11px;">Menunggu mhs ajukan ulang</div>

                                    @elseif($skp->status_jadwal_skp == 'belum' && $isSelesaiMagang)
                                        <span class="badge-custom bg-secondary-soft d-inline-flex align-items-center">
                                            <i class="bi bi-journal-x me-1"></i> Magang Selesai
                                        </span>
                                        <div class="small text-muted mt-1" style="font-size: 11px;">Belum mendaftar SKP</div>
                                    @endif
                                </td>

                                {{-- KOLOM AKSI DENGAN NOTIFICATION BADGE --}}
                                <td class="text-center">
                                    <a href="{{ route('dosen.skp.respon', $skp->id) }}"
                                        class="btn btn-sm btn-primary rounded-3 px-3 shadow-sm fw-bold position-relative">
                                        @if($skp->status_jadwal_skp == 'menunggu')
                                            <i class="bi bi-calendar-check me-1"></i> Respon

                                            {{-- RED NOTIFICATION BADGE TRIGGER --}}
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                                <span class="visually-hidden">Pengajuan Baru</span>
                                            </span>
                                        @else
                                            <i class="bi bi-eye me-1"></i> Lihat Detail
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar2-x"></i>
                                        <h5 class="fw-bold mt-3" style="color: var(--dark-text);">Belum Ada Pengajuan</h5>
                                        <p class="text-muted">Saat ini belum ada mahasiswa bimbingan yang selesai magang atau
                                            mengajukan jadwal SKP.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pengajuanSkp->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-2">
                {{ $pengajuanSkp->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

@endsection