@extends('layouts.app')

@section('title', 'Detail Riwayat Magang')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        @php
            $tglSelesai = \Carbon\Carbon::parse($magang->tanggal_selesai)->endOfDay();
            if ($magang->status_skp == 'sudah') {
                $statusColor = 'success';
                $statusText = 'Selesai (Lulus SKP)';
                $icon = 'bi-check-circle';
            } elseif ($tglSelesai->isPast()) {
                $statusColor = 'warning text-dark';
                $statusText = 'Belum SKP';
                $icon = 'bi-hourglass-split';
            } else {
                $statusColor = 'primary';
                $statusText = 'Aktif Magang';
                $icon = 'bi-activity';
            }
        @endphp

        {{-- STATUS BANNER --}}
        <div class="alert alert-{{ str_replace(' text-dark', '', $statusColor) }} shadow-sm d-flex align-items-center mb-4 border-0 border-start border-5 border-{{ str_replace(' text-dark', '', $statusColor) }}">
            <i class="bi {{ $icon }} fs-3 me-3 text-{{ str_replace(' text-dark', '', $statusColor) }}"></i>
            <div>
                <h6 class="mb-0 fw-bold">Status Magang Saat Ini</h6>
                <span class="badge bg-{{ $statusColor }} mt-1 fs-6">{{ $statusText }}</span>
            </div>
        </div>

        <div class="row g-4">
            {{-- CARD MAHASISWA & DOSEN --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 fw-bold text-primary">
                        <i class="bi bi-person-badge me-2"></i>Informasi Akademik
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="40%" class="text-muted">Nama Mahasiswa</td>
                                <td class="fw-bold">: {{ $magang->mahasiswa->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIM</td>
                                <td class="fw-bold">: {{ $magang->mahasiswa->nim }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Prodi / Angkatan</td>
                                <td>: {{ $magang->mahasiswa->prodi }} ({{ $magang->mahasiswa->angkatan }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. WhatsApp</td>
                                <td>: 
                                    @if($magang->mahasiswa->no_hp)
                                        @php
                                            $waLink = preg_replace('/[^0-9]/', '', $magang->mahasiswa->no_hp);
                                            if(substr($waLink, 0, 1) == '0') {
                                                $waLink = '62' . substr($waLink, 1);
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ $waLink }}" target="_blank" class="text-success text-decoration-none fw-bold">
                                            <i class="bi bi-whatsapp"></i> {{ $magang->mahasiswa->no_hp }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">Belum diisi</span>
                                    @endif
                                </td>
                            </tr>
                            <tr><td colspan="2"><hr class="my-2 text-muted"></td></tr>
                            <tr>
                                <td class="text-muted">Dosen Pembimbing</td>
                                <td class="fw-bold">: {{ $magang->dosen->name ?? 'Belum Ditentukan' }}</td>
                            </tr>
                            @if($magang->dosen)
                            <tr>
                                <td class="text-muted">NIP/NIK Dosen</td>
                                <td>: {{ $magang->dosen->nomor_induk ?? '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- CARD PERUSAHAAN --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 fw-bold text-primary">
                        <i class="bi bi-building me-2"></i>Informasi Perusahaan
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="40%" class="text-muted">Nama Instansi</td>
                                <td class="fw-bold text-primary">: {{ $magang->perusahaan->nama_perusahaan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat</td>
                                <td>: {{ $magang->perusahaan->alamat }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tema Magang</td>
                                <td class="fw-bold">: {{ $magang->tema_magang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Gaji</td>
                                <td>: <span class="badge bg-{{ $magang->status_gaji == 'paid' ? 'success' : 'secondary' }}">{{ strtoupper($magang->status_gaji) }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Periode Magang</td>
                                <td class="fw-bold text-danger">: {{ \Carbon\Carbon::parse($magang->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($magang->tanggal_selesai)->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- CARD DOKUMEN & SEMINAR --}}
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 fw-bold text-primary">
                        <i class="bi bi-folder-check me-2"></i>Dokumen & Hasil Evaluasi
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold mb-3">Dokumen Pendaftaran</h6>
                                @if($magang->file_surat_kaprodi)
                                    <a href="{{ asset('storage/' . $magang->file_surat_kaprodi) }}" target="_blank" class="btn btn-outline-info w-100 mb-2">
                                        <i class="bi bi-file-earmark-pdf"></i> Lihat Surat Balasan Instansi
                                    </a>
                                @else
                                    <div class="alert alert-light text-center border text-muted">Belum ada surat balasan.</div>
                                @endif
                            </div>
                            <div class="col-md-6 ps-4">
                                <h6 class="fw-bold mb-3">Seminar & SKP</h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td width="40%" class="text-muted">Nilai Seminar</td>
                                        <td class="fw-bold fs-5 text-primary">: {{ $magang->nilai_seminar ?? 'Belum Dinilai' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Laporan Seminar</td>
                                        <td>: 
                                            @if($magang->file_seminar)
                                                <a href="{{ asset('storage/' . $magang->file_seminar) }}" target="_blank" class="text-decoration-none">
                                                    <i class="bi bi-file-earmark-pdf text-danger"></i> Lihat Dokumen
                                                </a>
                                            @else
                                                <span class="text-muted italic">Belum diunggah</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection