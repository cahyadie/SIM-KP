@extends('layouts.app')

@section('title', 'Manajemen SKP')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- BAGIAN ATAS: Informasi Filter & Tombol Aksi --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                @if(request('bulan') || request('tahun'))
                    <span class="badge bg-info text-dark">
                        Filter aktif: {{ request('bulan') ? date('F', mktime(0, 0, 0, request('bulan'), 10)) : 'Semua Bulan' }} {{ request('tahun', date('Y')) }}
                    </span>
                @endif
            </div>
            
            <div class="d-flex gap-2">
                {{-- Tombol Filter --}}
                <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#filterSkpModal">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>

                {{-- Tombol Export --}}
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#exportSkpModal">
                    <i class="bi bi-download me-1"></i> Export Data
                </button>
            </div>
        </div>

        {{-- MODAL FILTER SKP --}}
        <div class="modal fade" id="filterSkpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Filter Data SKP</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.skp') }}" method="GET">
                        <div class="modal-body py-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted mb-1">Bulan</label>
                                <select name="bulan" class="form-select border-0 shadow-sm" style="background-color: var(--bg-body);">
                                    <option value="">Semua Bulan</option>
                                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $name)
                                        <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
                                <select name="tahun" class="form-select border-0 shadow-sm" style="background-color: var(--bg-body);">
                                    @php $currentYear = date('Y'); @endphp
                                    @for($y = $currentYear + 1; $y >= $currentYear - 2; $y--)
                                        <option value="{{ $y }}" {{ request('tahun', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            @if(request('bulan') || request('tahun') != date('Y'))
                                <a href="{{ route('admin.skp') }}" class="btn btn-light btn-sm w-100 mb-2 text-danger">Reset Filter</a>
                            @endif
                            <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL EXPORT SKP --}}
        <div class="modal fade" id="exportSkpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Pilih Format Laporan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.skp.export.excel', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                               class="btn btn-outline-success py-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-file-earmark-excel fs-4 me-2"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Format Excel</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">(.xlsx)</small>
                                </div>
                            </a>
                            <a href="{{ route('admin.skp.export.pdf', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
                               class="btn btn-outline-danger py-3 d-flex align-items-center justify-content-center">
                                <i class="bi bi-file-earmark-pdf fs-4 me-2"></i>
                                <div class="text-start">
                                    <div class="fw-bold">Format PDF</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">(.pdf)</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm w-100" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL DATA SKP --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-file-earmark-check me-2"></i>Daftar Dokumen SKP Mahasiswa</h6>
                <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $magang->total() }} Data Ditemukan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Mahasiswa</th>
                                <th>Perusahaan</th>
                                <th>Berkas Seminar</th>
                                <th>Status SKP</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($magang as $m)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $m->mahasiswa->user->name }}</td>
                                <td>{{ $m->perusahaan->nama_perusahaan }}</td>
                                <td>
                                    @if($m->file_seminar)
                                        <span class="badge bg-info"><i class="bi bi-file-pdf"></i> Ada Berkas</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Upload</span>
                                    @endif
                                </td>
                                <td>
                                    @if($m->status_skp == 'sudah')
                                        <span class="badge bg-success">Selesai (Nilai: {{ $m->nilai_seminar }})</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.skp.show', $m->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i> Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            @if($magang->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-2">
                    {{ $magang->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection