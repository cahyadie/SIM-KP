@extends('layouts.app')

@section('title', 'Respon Jadwal SKP')

@section('content')

    <style>
        .peer:checked+.card {
            border-color: #004b23 !important;
            background-color: #e8f5e9 !important;
            box-shadow: 0 0 0 4px rgba(0, 75, 35, 0.2);
            transform: translateY(-2px);
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

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
                        style="width: 60px; height: 60px;">
                        {{ substr($magang->mahasiswa->user->name ?? 'M', 0, 1) }}
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">{{ $magang->mahasiswa->user->name ?? 'Nama Tidak Ditemukan' }}</h5>
                        <p class="mb-0 text-muted">
                            NIM: {{ $magang->mahasiswa->nim }} | Tempat Magang: <span
                                class="fw-semibold text-dark">{{ $magang->perusahaan->nama_perusahaan }}</span>
                            <br>
                            {{-- MENAMPILKAN RUANGAN YANG DIAJUKAN MAHASISWA --}}
                            <!-- Rencana Lokasi/Ruangan: <span class="fw-bold text-danger"><i class="bi bi-geo-alt-fill"></i> {{ $magang->ruangan_skp ?? 'Belum ditentukan' }}</span> -->
                        </p>

                        {{-- TOMBOL LIHAT SURAT SELESAI MAGANG --}}
                        <div class="mt-2">
                            @if($magang->surat_selesai_magang)
                                <a href="{{ asset('storage/' . $magang->surat_selesai_magang) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary rounded-pill mt-1">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Buka Surat Selesai Magang
                                </a>
                            @else
                                <span class="badge bg-warning text-dark mt-1"><i class="bi bi-exclamation-triangle me-1"></i>
                                    Surat Selesai Magang Belum Diunggah</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- AREA RESPON JADWAL --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i> Pilihan Opsi Jadwal SKP
                    </h6>
                </div>
                <div class="card-body p-4 p-lg-5">

                    @if($magang->status_jadwal_skp == 'menunggu')
                        <div class="alert alert-info border-0 rounded-3 mb-4 small">
                            <i class="bi bi-info-circle-fill me-2"></i> Mahasiswa telah mengajukan 3 opsi waktu seminar. Silakan
                            pilih salah satu yang sesuai dengan jadwal Anda.
                        </div>

                        <form action="{{ route('dosen.bimbingan.skp.approve', $magang->id) }}" method="POST">
                            @csrf
                            <div class="row g-3 mb-5">
                                {{-- OPSI 1 --}}
                                <div class="col-md-4">
                                    <label class="w-100 cursor-pointer">
                                        <input type="radio" name="pilihan_opsi" value="1" class="d-none peer" required>
                                        <div class="card border-2 h-100 transition-all hover-shadow">
                                            <div class="card-body text-center p-4">
                                                <span class="badge bg-secondary mb-3">OPSI 1</span>
                                                <h5 class="fw-bold text-dark mb-1">
                                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_1)->format('d M Y') }}</h5>
                                                <h6 class="mb-0 text-primary fw-bold">
                                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_1)->format('H:i') }} WIB</h6>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                {{-- OPSI 2 --}}
                                <div class="col-md-4">
                                    <label class="w-100 cursor-pointer">
                                        <input type="radio" name="pilihan_opsi" value="2" class="d-none peer" required>
                                        <div class="card border-2 h-100 transition-all hover-shadow">
                                            <div class="card-body text-center p-4">
                                                <span class="badge bg-secondary mb-3">OPSI 2</span>
                                                <h5 class="fw-bold text-dark mb-1">
                                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_2)->format('d M Y') }}</h5>
                                                <h6 class="mb-0 text-primary fw-bold">
                                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_2)->format('H:i') }} WIB</h6>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                {{-- OPSI 3 --}}
                                <div class="col-md-4">
                                    <label class="w-100 cursor-pointer">
                                        <input type="radio" name="pilihan_opsi" value="3" class="d-none peer" required>
                                        <div class="card border-2 h-100 transition-all hover-shadow">
                                            <div class="card-body text-center p-4">
                                                <span class="badge bg-secondary mb-3">OPSI 3</span>
                                                <h5 class="fw-bold text-dark mb-1">
                                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_3)->format('d M Y') }}</h5>
                                                <h6 class="mb-0 text-primary fw-bold">
                                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_3)->format('H:i') }} WIB</h6>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-4">

                            <div class="d-flex flex-column flex-md-row gap-3 justify-content-end">
                                <button type="button" class="btn btn-outline-danger fw-bold px-4 py-2" data-bs-toggle="modal"
                                    data-bs-target="#modalTolakJadwal">
                                    <i class="bi bi-x-circle me-1"></i> Tolak & Minta Ulang
                                </button>
                                <button type="submit" class="btn btn-success fw-bold px-4 py-2">
                                    <i class="bi bi-check-circle-fill me-1"></i> Setujui Opsi Terpilih
                                </button>
                            </div>
                        </form>

                    @else
                        {{-- JIKA SUDAH DIRESPON --}}
                        <div class="text-center py-5">
                            @if($magang->status_jadwal_skp == 'disetujui')
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                                <h4 class="fw-bold mt-3">Jadwal Telah Disetujui</h4>
                                <p class="text-muted">Jadwal yang dipilih:
                                    <strong>{{ \Carbon\Carbon::parse($magang->jadwal_terpilih)->format('d F Y, H:i') }} WIB</strong>
                                </p>
                            @elseif($magang->status_jadwal_skp == 'ditolak')
                                <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                                <h4 class="fw-bold mt-3">Pengajuan Ditolak</h4>
                                <p class="text-muted">Menunggu mahasiswa mengirimkan 3 opsi jadwal yang baru.</p>
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
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Tolak Pengajuan Jadwal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('dosen.bimbingan.skp.reject', $magang->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="text-muted small">Berikan alasan mengapa 3 jadwal di atas tidak bisa Anda hadiri agar
                                mahasiswa dapat menyesuaikan.</p>
                            <div class="mb-3">
                                <textarea name="keterangan_tolak" class="form-control" rows="4"
                                    placeholder="Contoh: Saya sedang dinas minggu depan, tolong ajukan di bulan depan..."
                                    required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger fw-bold">Tolak & Minta Ajukan Ulang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection