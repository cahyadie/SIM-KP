@extends('layouts.app')

@section('title', 'Respon Jadwal SKP')

@section('content')

    <style>
        .peer:checked+.card {
            border-color: #004b23 !important;
            background-color: #e8f5e9 !important;
            box-shadow: 0 0 0 3px rgba(0, 75, 35, 0.15);
            transform: translateY(-2px);
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .cursor-pointer {
            cursor: pointer;
        }
        
        /* Animasi transisi yang lebih halus */
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-11 col-lg-9">

            {{-- HEADER KEMBALI --}}
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('dosen.skp.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm me-3 px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <div>
                    <h4 class="mb-0 fw-bold" style="color: #111827;">Respon Pengajuan SKP</h4>
                    <p class="text-muted small mb-0">Pilih salah satu jadwal yang diajukan atau minta pengajuan ulang.</p>
                </div>
            </div>

            {{-- INFO MAHASISWA --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center fw-bold me-3 fs-4"
                        style="width: 55px; height: 55px;">
                        {{ substr($magang->mahasiswa->user->name ?? 'M', 0, 1) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ $magang->mahasiswa->user->name ?? 'Nama Tidak Ditemukan' }}</h5>
                        <p class="mb-0 text-muted small">
                            NIM: {{ $magang->mahasiswa->nim }} | Tempat Magang: <span
                                class="fw-semibold text-dark">{{ $magang->perusahaan->nama_perusahaan }}</span>
                        </p>

                        {{-- TOMBOL LIHAT SURAT SELESAI MAGANG --}}
                        <div class="mt-2">
                            @if($magang->surat_selesai_magang)
                                <a href="{{ asset('storage/' . $magang->surat_selesai_magang) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Buka Surat Selesai Magang
                                </a>
                            @else
                                <span class="badge bg-warning-soft text-warning border border-warning-subtle rounded-pill px-3 py-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i> Surat Selesai Magang Belum Diunggah
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- AREA RESPON JADWAL --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom-0 px-4">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i> Pilihan Opsi Jadwal SKP
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">

                    @if($magang->status_jadwal_skp == 'menunggu')
                        <div class="alert alert-info border-0 rounded-3 mb-4 small">
                            <i class="bi bi-info-circle-fill me-2"></i> Mahasiswa telah mengajukan 7 opsi waktu seminar (1 minggu). Silakan pilih salah satu yang sesuai dengan jadwal Anda.
                        </div>

                        <form action="{{ route('dosen.bimbingan.skp.approve', $magang->id) }}" method="POST">
                            @csrf
                            
                            {{-- GRID OPSI JADWAL (Diperkecil & Dirapikan) --}}
                            <div class="row g-2 mb-4">
                                @for($i = 1; $i <= 7; $i++)
                                    @php
                                        // Dinamis mengambil field jadwal_opsi_1, jadwal_opsi_2, dst.
                                        $opsiField = 'jadwal_opsi_' . $i; 
                                        $jadwal = $magang->$opsiField;
                                    @endphp
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <label class="w-100 cursor-pointer h-100">
                                            <input type="radio" name="pilihan_opsi" value="{{ $i }}" class="d-none peer" required>
                                            <div class="card border-2 h-100 transition-all hover-shadow">
                                                <div class="card-body text-center p-3 d-flex flex-column justify-content-center">
                                                    <span class="badge bg-secondary mb-2 mx-auto" style="font-size: 0.65rem;">OPSI {{ $i }}</span>
                                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                                                        {{ \Carbon\Carbon::parse($jadwal)->format('d M Y') }}
                                                    </h6>
                                                    <span class="text-primary fw-bold" style="font-size: 0.9rem;">
                                                        {{ \Carbon\Carbon::parse($jadwal)->format('H:i') }} <span style="font-size: 0.7rem;">WIB</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endfor
                            </div>

                            <hr class="mb-4 opacity-10">

                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-danger fw-bold px-4" data-bs-toggle="modal"
                                    data-bs-target="#modalTolakJadwal">
                                    <i class="bi bi-x-circle me-1"></i> Tolak & Minta Ulang
                                </button>
                                <button type="submit" class="btn btn-success fw-bold px-4">
                                    <i class="bi bi-check-circle-fill me-1"></i> Setujui Opsi Terpilih
                                </button>
                            </div>
                        </form>

                    @else
                        {{-- JIKA SUDAH DIRESPON --}}
                        <div class="text-center py-5">
                            @if($magang->status_jadwal_skp == 'disetujui')
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                <h4 class="fw-bold mt-3 text-dark">Jadwal Telah Disetujui</h4>
                                <p class="text-muted">Jadwal yang dipilih:
                                    <br>
                                    <strong class="fs-5 text-success">{{ \Carbon\Carbon::parse($magang->jadwal_terpilih)->format('d F Y, H:i') }} WIB</strong>
                                </p>
                            @elseif($magang->status_jadwal_skp == 'ditolak')
                                <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                                <h4 class="fw-bold mt-3 text-dark">Pengajuan Ditolak</h4>
                                <p class="text-muted">Menunggu mahasiswa mengirimkan 7 opsi jadwal (1 minggu) yang baru.</p>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TOLAK JADWAL --}}
    @if($magang->status_jadwal_skp == 'menunggu')
        <div class="modal fade" id="modalTolakJadwal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-bottom-0 pb-0 mt-2 px-4">
                        <h5 class="modal-title fw-bold text-dark">Tolak Pengajuan Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('dosen.bimbingan.skp.reject', $magang->id) }}" method="POST">
                        @csrf
                        <div class="modal-body px-4">
                            <p class="text-muted small mb-3">Berikan alasan mengapa 7 jadwal di atas tidak bisa Anda hadiri agar
                                mahasiswa dapat menyesuaikan.</p>
                            <div>
                                <textarea name="keterangan_tolak" class="form-control bg-light rounded-3 p-3" rows="3"
                                    placeholder="Contoh: Saya sedang dinas minggu depan, tolong ajukan di bulan depan..."
                                    required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger fw-bold px-4">Kirim Penolakan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection