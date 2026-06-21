@extends('layouts.app')

@section('title', 'Seminar Kerja Praktek')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">

            {{-- ALERT SUKSES UMUM --}}
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 rounded-4">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- ALERT ERROR UMUM --}}
            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ==========================================
            BAGIAN 1: PENGAJUAN JADWAL KE DOSEN
            ========================================== --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i> 1. Pengajuan Jadwal Seminar</h6>
                </div>
                <div class="card-body p-4 p-lg-5">

                    @php
                        $statusJadwal = $magang->status_jadwal_skp ?? 'belum'; 
                    @endphp

                    @if($statusJadwal == 'ditolak')
                        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4">
                            <h6 class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i> Pengajuan Jadwal Ditolak</h6>
                            <p class="mb-0 small">Dosen pembimbing meminta Anda mengajukan ulang jadwal. Keterangan:
                                <strong>{{ $magang->keterangan_tolak_jadwal }}</strong>
                            </p>
                        </div>
                    @endif

                    @if($statusJadwal == 'belum' || $statusJadwal == 'ditolak')
                        {{-- FORM PENGAJUAN 3 OPSI JADWAL & SURAT SELESAI --}}
                        <div class="alert alert-info border-0 rounded-4 mb-4 small">
                            <i class="bi bi-info-circle-fill me-2"></i> Silakan ajukan 3 opsi waktu yang berbeda dan unggah
                            Surat Selesai Magang. Dosen pembimbing akan memilih salah satu jadwal yang paling sesuai.
                        </div>

                        <form action="{{ route('mahasiswa.seminar.ajukan_jadwal') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Opsi Jadwal 1 <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="jadwal_opsi_1" class="form-control" required
                                        value="{{ old('jadwal_opsi_1') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Opsi Jadwal 2 <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="jadwal_opsi_2" class="form-control" required
                                        value="{{ old('jadwal_opsi_2') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Opsi Jadwal 3 <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="jadwal_opsi_3" class="form-control" required
                                        value="{{ old('jadwal_opsi_3') }}">
                                </div>

                                <!-- <div class="col-md-6 mt-4">
                                                <label class="form-label fw-bold small text-muted">Rencana Ruangan Pelaksanaan <span class="text-danger">*</span></label>
                                                <div class="input-group shadow-sm">
                                                    <span class="input-group-text bg-white"><i class="bi bi-geo-alt text-danger"></i></span>
                                                    <input type="text" name="ruangan_skp" class="form-control border-start-0 ps-0" placeholder="Contoh: Ruang Sidang TI / Lab Komputer" required value="{{ old('ruangan_skp', $magang->ruangan_skp) }}">
                                                </div>
                                            </div> -->

                                <div class="col-md-6 mt-4">
                                    <label class="form-label fw-bold small text-muted">Surat Selesai Magang (PDF) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white"><i
                                                class="bi bi-file-earmark-pdf text-danger"></i></span>
                                        <input type="file" name="surat_selesai_magang" class="form-control border-start-0 ps-0"
                                            accept="application/pdf" {{ $magang->surat_selesai_magang ? '' : 'required' }}>
                                    </div>
                                    @if($magang->surat_selesai_magang)
                                        <small class="text-success mt-1 d-block"><i class="bi bi-check-circle"></i> File sebelumnya
                                            sudah terupload. Abaikan jika tidak ingin mengganti.</small>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                                <i class="bi bi-send-fill me-1"></i> Ajukan Jadwal & Surat ke Dospem
                            </button>
                        </form>

                    @elseif($statusJadwal == 'menunggu')
                        {{-- STATUS MENUNGGU --}}
                        <div class="text-center py-4">
                            <div class="spinner-border text-warning mb-3" role="status" style="width: 3rem; height: 3rem;">
                            </div>
                            <h5 class="fw-bold">Menunggu Persetujuan Dosen</h5>
                            <p class="text-muted small">Anda telah mengajukan 3 opsi jadwal dan berkas Surat Selesai Magang.
                                Menunggu dosen pembimbing memilih jadwal.</p>

                            <div class="d-flex justify-content-center flex-wrap gap-2 mt-3 mb-3">
                                <span class="badge bg-light text-dark border p-2"><i class="bi bi-calendar"></i> Opsi 1:
                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_1)->format('d M Y, H:i') }}</span>
                                <span class="badge bg-light text-dark border p-2"><i class="bi bi-calendar"></i> Opsi 2:
                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_2)->format('d M Y, H:i') }}</span>
                                <span class="badge bg-light text-dark border p-2"><i class="bi bi-calendar"></i> Opsi 3:
                                    {{ \Carbon\Carbon::parse($magang->jadwal_opsi_3)->format('d M Y, H:i') }}</span>
                            </div>

                            @if($magang->surat_selesai_magang)
                                <a href="{{ asset('storage/' . $magang->surat_selesai_magang) }}" target="_blank"
                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Berkas Surat Selesai Magang
                                </a>
                            @endif
                        </div>

                    @elseif($statusJadwal == 'disetujui')
                        {{-- JADWAL DISETUJUI --}}
                        <div class="alert alert-success border-0 shadow-sm text-center py-4 mb-0 rounded-4">
                            <i class="bi bi-calendar2-check-fill text-success" style="font-size: 2.5rem;"></i>
                            <h5 class="fw-bold text-success mt-2">Jadwal Disetujui!</h5>
                            <p class="text-dark mb-0">Seminar Anda akan dilaksanakan pada:</p>

                            <h4
                                class="fw-bold mt-2 mb-2 border border-success d-inline-block px-4 py-2 rounded-3 bg-white text-success">
                                {{ \Carbon\Carbon::parse($magang->jadwal_terpilih)->format('l, d F Y - H:i') }} WIB
                            </h4>

                            <!-- <div class="mt-2 mb-3">
                                            <span class="badge bg-light text-danger border border-danger px-3 py-2 fs-6 rounded-pill">
                                                <i class="bi bi-geo-alt-fill me-1"></i> Lokasi: {{ $magang->ruangan_skp }}
                                            </span>
                                        </div> -->

                            @if($magang->surat_selesai_magang)
                                <a href="{{ asset('storage/' . $magang->surat_selesai_magang) }}" target="_blank"
                                    class="btn btn-sm btn-outline-success rounded-pill mt-2">
                                    <i class="bi bi-file-earmark-check me-1"></i> Surat Selesai Magang Terkirim
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- ==========================================
            BAGIAN 2: INPUT NILAI & BERKAS (TANPA VERIFIKASI)
            ========================================== --}}
            <div
                class="card shadow-sm border-0 rounded-4 overflow-hidden {{ $statusJadwal != 'disetujui' ? 'opacity-50' : '' }}">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-journal-bookmark-fill me-2"></i> 2. Form Penyerahan Berkas &
                        Nilai SKP</h6>
                </div>

                <div class="card-body p-4 p-lg-5">

                    @if($statusJadwal != 'disetujui')
                        {{-- DIKUNCI JIKA JADWAL BELUM DISETUJUI --}}
                        <div class="text-center py-5">
                            <i class="bi bi-lock-fill text-muted" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold mt-3 text-muted">Form Terkunci</h5>
                            <p class="text-muted small mb-0">Anda baru bisa menginput nilai dan mengupload berkas akhir setelah
                                jadwal seminar disetujui dan seminar dilaksanakan.</p>
                        </div>
                    @else
                        {{-- TAMPILKAN HASIL JIKA SUDAH INPUT --}}
                        @if($magang->status_skp == 'sudah')
                            <div class="alert alert-success border-0 shadow-sm text-center py-4 mb-0 rounded-4">
                                <i class="bi bi-patch-check-fill text-success" style="font-size: 3rem;"></i>
                                <h4 class="fw-bold text-success mt-2">SKP Selesai & Terbit</h4>
                                <p class="text-muted mb-3">Nilai dan berkas laporan Anda telah tersimpan ke dalam sistem.</p>
                                <div class="d-inline-block border px-4 py-2 rounded-3 bg-white">
                                    <span class="text-muted small d-block">Nilai Akhir</span>
                                    <span class="fs-1 fw-bold text-primary">{{ $magang->nilai_seminar }}</span>
                                </div>
                                @if($magang->file_seminar)
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/' . $magang->file_seminar) }}" target="_blank"
                                            class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-pdf me-1"></i> Lihat
                                            Berkas Laporan Seminar</a>
                                    </div>
                                @endif
                            </div>
                        @else
                            {{-- FORM INPUT NILAI --}}
                            <form action="{{ route('mahasiswa.seminar.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Nilai Seminar (Sesuai Berkas) <span
                                            class="text-danger">*</span></label>
                                    <select name="nilai_seminar"
                                        class="form-select form-select-lg fw-bold text-center border-primary" required>
                                        <option value="" disabled selected>-- Pilih Nilai --</option>
                                        <option value="A">A (Sangat Baik)</option>
                                        <option value="B">B (Baik)</option>
                                        <option value="C">C (Cukup)</option>
                                        <option value="D">D (Kurang)</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Upload Laporan & Berita Acara (PDF) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="file" name="file_seminar" class="form-control" accept="application/pdf"
                                            required>
                                        <span class="input-group-text bg-light"><i class="bi bi-file-earmark-pdf"></i></span>
                                    </div>
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                        <i class="bi bi-save-fill me-2"></i> Simpan Nilai & Terbitkan SKP
                                    </button>
                                </div>
                            </form>
                        @endif

                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection