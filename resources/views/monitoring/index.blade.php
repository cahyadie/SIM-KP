@extends('layouts.app')

@section('title', 'Monitoring Logbook')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-end mb-4">
        <span class="badge bg-info text-white">{{ $sedang_magang->total() }} Mahasiswa Aktif</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-activity me-2"></i>Daftar Mahasiswa Magang</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mahasiswa</th>
                            <th>Perusahaan</th>
                            <th>Periode</th>
                            <th class="text-center">Total Logbook</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sedang_magang as $m)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $m->mahasiswa->user->name }}</div>
                                <small class="text-muted">{{ $m->mahasiswa->nim }}</small>
                            </td>
                            <td>
                                {{ $m->perusahaan->nama_perusahaan }}
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($m->tanggal_mulai)->format('d M Y') }} <br>
                                    s/d {{ \Carbon\Carbon::parse($m->tanggal_selesai)->format('d M Y') }}
                                </small>
                            </td>
                            <td class="text-center">
                                @if($m->logbooks_count > 0)
                                    <span class="badge bg-success rounded-pill px-3">{{ $m->logbooks_count }} Entri</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3">0 Entri</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.monitoring.show', $m->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Lihat Logbook
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Tidak ada mahasiswa yang sedang magang saat ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($sedang_magang->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-2">
                {{ $sedang_magang->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection