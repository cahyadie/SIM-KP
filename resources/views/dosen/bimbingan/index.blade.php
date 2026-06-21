@extends('layouts.app')

@section('title', 'Daftar Bimbingan')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-5">Mahasiswa</th>
                                <th>Tema magang</th>
                                <th>Perusahaan</th>
                                <th>Periode</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bimbingan as $m)
                                <tr>
                                    <td class="ps-5">
                                        <div class="fw-bold">{{ $m->mahasiswa->user->name }}</div>
                                        <small class="text-muted">{{ $m->mahasiswa->nim }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $m->tema_magang }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $m->perusahaan->nama_perusahaan }}</div>
                                        <small class="text-muted">{{ Str::limit($m->perusahaan->alamat, 30) }}</small>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($m->tanggal_mulai)->format('d M Y') }} s/d
                                        {{ \Carbon\Carbon::parse($m->tanggal_selesai)->format('d M Y') }}
                                    </td>
                                    <td class="text-center">
                                        {{-- Status diubah menjadi teks biasa tanpa warna background --}}
                                        <div class="fw-bold text-dark">Aktif Magang</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            {{-- TOMBOL 1: DETAIL MAHASISWA --}}
                                            <a href="{{ route('dosen.bimbingan.detail', $m->id) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-person-lines-fill"></i> Detail
                                            </a>

                                            {{-- TOMBOL 2: LOGBOOK DENGAN BADGE NOTIFIKASI --}}
                                            <a href="{{ route('dosen.bimbingan.logbook', $m->id) }}"
                                                class="btn btn-sm btn-outline-primary position-relative">
                                                <i class="bi bi-journal-text"></i> Logbook

                                                {{-- Hitung jumlah logbook yang belum di-ACC --}}
                                                @php
                                                    $pendingAcc = $m->logbooks ? $m->logbooks->where('status_acc', false)->count() : 0;
                                                @endphp

                                                {{-- Munculkan badge HANYA jika ada logbook belum di-ACC --}}
                                                @if($pendingAcc > 0)
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                        style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                                                        {{ $pendingAcc }}
                                                        <span class="visually-hidden">Logbook belum di-ACC</span>
                                                    </span>
                                                @endif
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada mahasiswa bimbingan yang aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($bimbingan->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-2">
                    {{ $bimbingan->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection