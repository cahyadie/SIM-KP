@extends('layouts.app')

@section('title', 'Pantauan Lama SKP')

@section('content')
    <div class="container-fluid px-4 py-3">
 
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Seminar Praktek Magang terlewat > 30 Hari</h4>
            
            {{-- Wrapper untuk tombol-tombol sebelah kanan --}}
            <div class="d-flex gap-2">

                <a href="{{ route('kaprodi.pantauan-skp.pdf') }}" class="btn btn-sm shadow-sm px-3 text-white" style="background-color: #005f33; border-color: #005f33;">
                    <i class="bi bi-download me-1"></i> Export Data
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Dosen Pembimbing</th> {{-- KOLOM BARU --}}
                                <th>Tempat Magang</th>
                                <th>Tanggal Selesai Magang</th>
                                <th>Terlewat </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswaOverdue as $index => $mhs)
                                @php
                                    // Mengambil data magang terakhir yang diterima
                                    $magang = $mhs->magangs->where('status_validasi', 'diterima')->sortByDesc('tanggal_selesai')->first();

                                    // PERBAIKAN 1: Membulatkan angka hari menggunakan floor() agar mutlak bulat
                                    $hariTerlewat = floor(\Carbon\Carbon::parse($magang->tanggal_selesai)->diffInDays(\Carbon\Carbon::now()));
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $mhs->nim }}</td>

                                    {{-- PERBAIKAN 2: Mengambil nama dari relasi tabel user --}}
                                    <td class="fw-semibold">{{ $mhs->user->name ?? 'Tidak diketahui' }}</td>

                                    {{-- Menampilkan Nama Dosen Pembimbing --}}
                                    <td>{{ $magang->dosen->name ?? 'Belum Ditentukan' }}</td>

                                    <td>{{ $magang->perusahaan->nama_perusahaan ?? 'Tidak diketahui' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($magang->tanggal_selesai)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger px-2 py-1" style="font-size: 0.8rem;">
                                            Terlewat {{ $hariTerlewat }} Hari
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- PERBAIKAN 3: Ubah colspan dari 6 menjadi 7 --}}
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Tidak ada mahasiswa yang terlambat mendaftar SKP lebih dari 30 hari.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($mahasiswaOverdue->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-2">
                    {{ $mahasiswaOverdue->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection