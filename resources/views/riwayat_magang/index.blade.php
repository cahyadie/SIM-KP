@extends('layouts.app')

@section('title', 'Riwayat Magang Mahasiswa')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">

            {{-- 1. BAGIAN PENCARIAN (Sesuai dengan gambar contoh) --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.5rem;">
                <div class="card-body p-2">
                    <form action="{{ url()->current() }}" method="GET" class="m-0">
                        {{-- Simpan parameter filter yang sedang aktif agar tidak hilang saat mencari --}}
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        @if(request('bulan')) <input type="hidden" name="bulan" value="{{ request('bulan') }}"> @endif
                        @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                        
                        <div class="input-group align-items-center border-0">
                            <span class="input-group-text bg-white border-0 text-muted ps-3 pe-2">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-0 shadow-none py-2" 
                                placeholder="Cari berdasarkan nama mahasiswa, dosen, atau perusahaan..." 
                                value="{{ request('search') }}" style="font-size: 0.95rem;">
                            <button class="btn text-white px-4 py-2" type="submit" 
                                style="background-color: #014f31; border-radius: 0.3rem;">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 2. BAGIAN TOMBOL FILTER & EXPORT --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    {{-- Indikator Filter Aktif --}}
                    @if(request('bulan') || request('tahun') || request('status') || request('search'))
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="text-muted small fw-bold">Menampilkan:</span>
                            @if(request('search')) 
                                <span class="badge bg-light text-dark border"><i class="bi bi-search me-1"></i> "{{ request('search') }}"</span> 
                            @endif
                            @if(request('bulan') || request('tahun'))
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-calendar me-1"></i> 
                                    {{ request('bulan') ? date('F', mktime(0, 0, 0, request('bulan'), 10)) : 'Semua Bulan' }}
                                    {{ request('tahun', date('Y')) }}
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-funnel me-1"></i> {{ ucfirst(request('status')) }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    {{-- Tombol Filter --}}
                    <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm px-3" data-bs-toggle="modal"
                        data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>

                    {{-- Tombol Export (HANYA MUNCUL UNTUK ADMIN) --}}
                    @if(Auth::user()->role === 'admin')
                        <button type="button" class="btn btn-primary btn-sm shadow-sm px-3" data-bs-toggle="modal"
                            data-bs-target="#exportModal">
                            <i class="bi bi-download me-1"></i> Export Data
                        </button>
                    @endif
                </div>
            </div>

            {{-- Modal Filter --}}
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold" id="filterModalLabel">Filter Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ url()->current() }}" method="GET">
                            {{-- Simpan parameter search agar tidak hilang saat memfilter --}}
                            @if(request('search')) 
                                <input type="hidden" name="search" value="{{ request('search') }}"> 
                            @endif

                            <div class="modal-body py-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Status Magang</label>
                                    <select name="status" class="form-select border-0 shadow-sm"
                                        style="background-color: var(--bg-body);">
                                        <option value="">Semua Status</option>
                                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif Magang</option>
                                        <option value="seminar" {{ request('status') == 'seminar' ? 'selected' : '' }}>Belum SKP</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai (Lulus SKP)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Bulan Mulai</label>
                                    <select name="bulan" class="form-select border-0 shadow-sm"
                                        style="background-color: var(--bg-body);">
                                        <option value="">Semua Bulan</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
                                    <select name="tahun" class="form-select border-0 shadow-sm"
                                        style="background-color: var(--bg-body);">
                                        @php $currentYear = date('Y'); @endphp
                                        <option value="">Semua Tahun</option>
                                        @for($y = $currentYear + 1; $y >= $currentYear - 3; $y--)
                                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                @if(request('bulan') || request('tahun') || request('status') || request('search'))
                                    <a href="{{ url()->current() }}" class="btn btn-light btn-sm w-100 mb-2 text-danger">Reset
                                        Semua Filter</a>
                                @endif
                                <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Export (KHUSUS ADMIN) --}}
            @if(Auth::user()->role === 'admin')
                <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold" id="exportModalLabel">Format Export</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-4">
                                <div class="d-grid gap-3">
                                    {{-- Oper semua parameter filter (termasuk search) ke tombol export --}}
                                    <a href="{{ route('admin.riwayat-magang.export.excel', request()->all()) }}"
                                        class="btn btn-outline-success py-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-excel fs-4 me-2"></i>
                                        <div class="text-start">
                                            <div class="fw-bold">Format Excel</div>
                                            <small class="text-muted" style="font-size: 0.7rem;">(.xlsx)</small>
                                        </div>
                                    </a>
                                    <a href="{{ route('admin.riwayat-magang.export.pdf', request()->all()) }}"
                                        class="btn btn-outline-danger py-3 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-pdf fs-4 me-2"></i>
                                        <div class="text-start">
                                            <div class="fw-bold">Format PDF</div>
                                            <small class="text-muted" style="font-size: 0.7rem;">(.pdf)</small>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. TABEL DATA RIWAYAT --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="bi bi-card-list me-2"></i>Data Riwayat Magang</h6>
                    <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill">{{ $riwayat->total() }} Data Ditemukan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Mahasiswa</th>
                                    <th>Dosen Pembimbing</th>
                                    <th>Perusahaan & Tema</th>
                                    <th>Tanggal Magang</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $p)
                                    @php
                                        // LOGIKA PENENTUAN STATUS (Tanpa Background Badge)
                                        $tglSelesai = \Carbon\Carbon::parse($p->tanggal_selesai)->endOfDay();
                                        if ($p->status_skp == 'sudah') {
                                            $statusBadge = '<span class="text-dark"><i class="bi bi-check-circle me-1"></i>Selesai</span>';
                                        } elseif ($tglSelesai->isPast()) {
                                            // Menggunakan warna custom agak gelap agar mudah dibaca di layar putih
                                            $statusBadge = '<span class="text-dark"><i class="bi bi-hourglass-split me-1"></i>Belum SKP</span>';
                                        } else {
                                            $statusBadge = '<span class="text-dark"><i class="bi bi-activity me-1"></i>Aktif Magang</span>';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ $p->mahasiswa->user->name }}</div>
                                            <small class="text-muted">NIM: {{ $p->mahasiswa->nim }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $p->dosen->name ?? 'Belum Ditentukan' }}</div>
                                        </td>
                                        <td>
                                            <strong>{{ $p->perusahaan->nama_perusahaan }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($p->tema_magang, 30) }}</small>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }} - <br>
                                            {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                                        </td>
                                        <td>
                                            {!! $statusBadge !!}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $userRole = Auth::user()->role;
                                                $detailRoute = '#'; // Default fallback

                                                if ($userRole === 'admin') {
                                                    $detailRoute = route('admin.riwayat-magang.show', $p->id);
                                                } elseif ($userRole === 'kaprodi') {
                                                    $detailRoute = route('kaprodi.riwayat-magang.show', $p->id);
                                                } elseif ($userRole === 'dosen') {
                                                    $detailRoute = route('dosen.riwayat-magang.show', $p->id);
                                                }
                                            @endphp

                                            <a href="{{ $detailRoute }}" class="btn btn-info btn-sm text-white shadow-sm"
                                                title="Lihat Detail">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="bi bi-folder-x display-4 text-muted opacity-25"></i>
                                            <p class="text-muted mt-2">Tidak ada data riwayat magang yang ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($riwayat->hasPages())
                        <div class="p-3 border-top">
                            {{ $riwayat->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection