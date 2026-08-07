@extends('layouts.app')

@section('title', 'Seminar Kerja Praktek')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10">

                {{-- HEADER HALAMAN --}}
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('mahasiswa.dashboard') }}"
                        class="btn btn-white border shadow-sm rounded-3 px-3 py-2 text-decoration-none me-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">Seminar Kerja Praktek (SKP)</h4>
                        <p class="text-muted small mb-0">Kelola pengajuan jadwal dan penyerahan berkas nilai.</p>
                    </div>
                </div>

                {{-- ALERT SUKSES UMUM --}}
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4 rounded-4 p-3">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                        <div class="fw-medium text-dark">{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- ALERT ERROR UMUM --}}
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4 p-3 d-flex">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Terjadi Kesalahan</h6>
                            <ul class="mb-0 ps-3 small text-dark">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- ==========================================
                BAGIAN 1: PENGAJUAN JADWAL KE DOSEN
                ========================================== --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-dark text-white py-3 px-4 border-bottom-0">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i> 1. Pengajuan Jadwal Seminar</h6>
                    </div>
                    <div class="card-body p-4 p-lg-5">

                        @php
                            $statusJadwal = $magang->status_jadwal_skp ?? 'belum'; 
                        @endphp

                        {{-- PESAN JIKA DITOLAK --}}
                        @if($statusJadwal == 'ditolak')
                            <div class="alert bg-danger-soft border border-danger-subtle shadow-sm mb-4 rounded-4 p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-x-circle-fill text-danger fs-5 me-2"></i>
                                    <h6 class="fw-bold text-danger mb-0">Pengajuan Jadwal Sebelumnya Ditolak</h6>
                                </div>
                                <p class="mb-0 small text-dark ms-4">Dosen pembimbing meminta Anda mengajukan ulang jadwal.
                                    Keterangan: <strong>{{ $magang->keterangan_tolak_jadwal }}</strong></p>
                            </div>
                        @endif

                        @if($statusJadwal == 'belum' || $statusJadwal == 'ditolak')
                            {{-- FORM PENGAJUAN (BELUM / DITOLAK) --}}

                            <form action="{{ route('mahasiswa.seminar.ajukan_jadwal') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                {{-- GENERATOR JADWAL OTOMATIS --}}
                                <div class="card bg-primary-soft border-primary border-opacity-25 shadow-sm rounded-4 mb-4 p-4">
                                    <h6 class="fw-bold text-primary mb-2">
                                        <i class="bi bi-magic me-2"></i> Buat Jadwal Otomatis (1 Minggu)
                                    </h6>
                                    <p class="small text-muted mb-3">Pilih tanggal awal (Senin) dan jam seminar. Sistem akan
                                        otomatis mengisi 7 opsi hari ke bawah secara berurutan. Anda tetap bisa mengubahnya
                                        secara manual.</p>

                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-dark">Tanggal Mulai <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="masterDate"
                                                class="form-control border-primary-subtle rounded-3">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-dark">Jam Default <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" id="masterTime"
                                                class="form-control border-primary-subtle rounded-3">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary fw-bold w-100 shadow-sm rounded-3"
                                                onclick="generateWeek()">
                                                <i class="bi bi-arrow-down-circle me-2"></i> Generate ke Bawah
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="text-muted opacity-10 mb-4">

                                {{-- 7 OPSI JADWAL (Akan terisi otomatis) --}}
                                <div class="row g-4 mb-4">
                                    <div class="col-12 mb-1">
                                        <label class="small text-muted fw-bold text-uppercase tracking-wide">Hasil Opsi
                                            Jadwal</label>
                                    </div>

                                    @for($i = 1; $i <= 7; $i++)
                                        <div class="col-md-4 col-lg-3">
                                            <label class="form-label fw-bold small text-muted">Opsi Jadwal {{ $i }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="datetime-local" name="jadwal_opsi_{{ $i }}" id="opsi_{{ $i }}"
                                                class="form-control bg-light rounded-3 transition-all" required
                                                value="{{ old('jadwal_opsi_' . $i) }}">
                                        </div>
                                    @endfor

                                    {{-- UPLOAD SURAT SELESAI --}}
                                    <div class="col-md-4 col-lg-3">
                                        <label class="form-label fw-bold small text-muted">Surat Selesai (PDF) <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="bi bi-file-earmark-pdf text-danger"></i></span>
                                            <input type="file" name="surat_selesai_magang"
                                                class="form-control border-start-0 ps-0" accept="application/pdf" {{ $magang->surat_selesai_magang ? '' : 'required' }}>
                                        </div>
                                        @if($magang->surat_selesai_magang)
                                            <small class="text-success mt-1 d-block fw-medium" style="font-size: 0.75rem;"><i
                                                    class="bi bi-check-circle me-1"></i> File tersimpan. Abaikan jika tak
                                                diubah.</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                    <button type="submit"
                                        class="btn btn-success fw-bold shadow-sm px-4 py-2 rounded-3 w-100 w-md-auto hover-elevate">
                                        <i class="bi bi-send-fill me-2"></i> Ajukan Jadwal & Surat ke Dospem
                                    </button>
                                </div>
                            </form>

                        @elseif($statusJadwal == 'menunggu')
                            {{-- STATUS MENUNGGU --}}
                            <div class="text-center py-4">
                                <div class="spinner-border text-warning mb-3" role="status"
                                    style="width: 3rem; height: 3rem; border-width: 0.25em;"></div>
                                <h5 class="fw-bold text-dark">Menunggu Persetujuan Dosen</h5>
                                <p class="text-muted small mx-auto" style="max-width: 500px;">Anda telah mengajukan opsi jadwal
                                    1 minggu dan berkas Surat Selesai Magang. Harap tunggu dosen pembimbing Anda memilih jadwal
                                    yang sesuai.</p>

                                <div class="d-flex justify-content-center flex-wrap gap-2 mt-4 mb-4">
                                    @for($i = 1; $i <= 7; $i++)
                                        @php $opsiField = 'jadwal_opsi_' . $i; @endphp
                                        <span class="badge bg-light text-dark border p-2 shadow-xs fw-medium rounded-3">
                                            <i class="bi bi-calendar-event text-primary me-1"></i> Opsi {{ $i }}:
                                            {{ \Carbon\Carbon::parse($magang->$opsiField)->format('d M Y, H:i') }}
                                        </span>
                                    @endfor
                                </div>

                                @if($magang->surat_selesai_magang)
                                    <a href="{{ asset('storage/' . $magang->surat_selesai_magang) }}" target="_blank"
                                        class="btn btn-outline-secondary rounded-pill px-4 shadow-sm hover-elevate">
                                        <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> Lihat Berkas Surat Selesai
                                    </a>
                                @endif
                            </div>

                        @elseif($statusJadwal == 'disetujui')
                            {{-- JADWAL DISETUJUI --}}
                            <div class="bg-success-soft border border-success-subtle shadow-sm text-center py-5 px-4 rounded-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow"
                                    style="width: 80px; height: 80px;">
                                    <i class="bi bi-calendar2-check-fill display-4"></i>
                                </div>
                                <h4 class="fw-bold text-success mb-2">Jadwal Disetujui!</h4>
                                <p class="text-dark mb-4">Seminar Kerja Praktek Anda akan dilaksanakan pada jadwal berikut:</p>

                                <div class="d-inline-block bg-white border border-success shadow-sm px-4 py-3 rounded-4 mb-4">
                                    <h3 class="fw-bold text-success mb-0 tracking-wide">
                                        {{ \Carbon\Carbon::parse($magang->jadwal_terpilih)->format('l, d F Y') }} <br
                                            class="d-md-none">
                                        <span class="text-dark mx-2 d-none d-md-inline">|</span>
                                        <i class="bi bi-clock text-muted fs-4"></i>
                                        {{ \Carbon\Carbon::parse($magang->jadwal_terpilih)->format('H:i') }} WIB
                                    </h3>
                                </div>

                                <div>
                                    @if($magang->surat_selesai_magang)
                                        <a href="{{ asset('storage/' . $magang->surat_selesai_magang) }}" target="_blank"
                                            class="btn btn-success bg-white text-success border-success rounded-pill px-4 fw-medium shadow-sm hover-elevate">
                                            <i class="bi bi-file-check-fill me-2"></i> Surat Selesai Magang Terlampir
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ==========================================
                BAGIAN 2: INPUT NILAI & BERKAS (TANPA VERIFIKASI)
                ========================================== --}}
                <div
                    class="card shadow-sm border-0 rounded-4 overflow-hidden {{ $statusJadwal != 'disetujui' ? 'opacity-75' : '' }}">
                    <div class="card-header bg-primary text-white py-3 px-4 border-bottom-0">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-bookmark-fill me-2"></i> 2. Form Penyerahan Berkas
                            & Nilai SKP</h6>
                    </div>

                    <div class="card-body p-4 p-lg-5">
                        @if($statusJadwal != 'disetujui')
                            {{-- DIKUNCI JIKA JADWAL BELUM DISETUJUI --}}
                            <div class="text-center py-5 bg-light-soft rounded-4 border">
                                <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 70px; height: 70px;">
                                    <i class="bi bi-lock-fill text-muted fs-2"></i>
                                </div>
                                <h5 class="fw-bold text-muted">Tahap Ini Terkunci</h5>
                                <p class="text-muted small mb-0 max-w-sm mx-auto">Anda baru bisa menginput nilai dan mengupload
                                    berkas laporan akhir setelah jadwal seminar disetujui dan seminar selesai dilaksanakan.</p>
                            </div>
                        @else
                            {{-- TAMPILKAN HASIL JIKA SUDAH INPUT --}}
                            @if($magang->status_skp == 'sudah')
                                <div class="bg-primary-soft border border-primary-subtle shadow-sm text-center py-5 rounded-4">
                                    <i class="bi bi-patch-check-fill text-primary display-3 mb-3"></i>
                                    <h4 class="fw-bold text-dark">SKP Selesai & Terbit</h4>
                                    <p class="text-muted mb-4">Nilai dan berkas laporan Anda telah berhasil tersimpan ke dalam
                                        sistem.</p>

                                    <div
                                        class="d-inline-flex align-items-center justify-content-center border shadow-sm px-4 py-3 rounded-4 bg-white mb-4 gap-3">
                                        <div class="text-end border-end pe-3">
                                            <span class="text-muted small d-block text-uppercase fw-bold">Nilai Akhir</span>
                                        </div>
                                        <div>
                                            <span class="display-5 fw-bold text-primary lh-1">{{ $magang->nilai_seminar }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        @if($magang->file_seminar)
                                            <a href="{{ asset('storage/' . $magang->file_seminar) }}" target="_blank"
                                                class="btn btn-outline-primary rounded-pill px-4 shadow-sm hover-elevate">
                                                <i class="bi bi-file-earmark-pdf-fill me-2"></i> Lihat Berkas Laporan & Berita Acara
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                {{-- FORM INPUT NILAI --}}
                                <form action="{{ route('mahasiswa.seminar.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">Nilai Seminar (Sesuai Berita Acara) <span
                                                    class="text-danger">*</span></label>
                                            <select name="nilai_seminar"
                                                class="form-select form-select-lg fw-bold border-primary shadow-sm rounded-3"
                                                required>
                                                <option value="" disabled selected>-- Pilih Nilai Akhir --</option>
                                                <option value="A">A (Sangat Baik)</option>
                                                <option value="B">B (Baik)</option>
                                                <option value="C">C (Cukup)</option>
                                                <option value="D">D (Kurang)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark">Laporan & Berita Acara (PDF) <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                                <input type="file" name="file_seminar" class="form-control" accept="application/pdf"
                                                    required>
                                                <span class="input-group-text bg-light border-start-0"><i
                                                        class="bi bi-file-earmark-pdf text-danger"></i></span>
                                            </div>
                                            <small class="text-muted mt-2 d-block"><i class="bi bi-info-circle me-1"></i>Gabungkan
                                                laporan akhir dan berita acara nilai menjadi 1 file PDF.</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                                        <button type="submit"
                                            class="btn btn-primary btn-lg fw-bold shadow-sm px-4 rounded-3 hover-elevate">
                                            <i class="bi bi-save-fill me-2"></i> Simpan Nilai & Selesaikan SKP
                                        </button>
                                    </div>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPT UNTUK GENERATE TANGGAL 1 MINGGU --}}
    <script>
        function generateWeek() {
            const startDate = document.getElementById('masterDate').value;
            const time = document.getElementById('masterTime').value;

            if (!startDate || !time) {
                alert('Mohon lengkapi pilihan "Tanggal Mulai" dan "Jam Default" terlebih dahulu!');
                return;
            }

            let baseDate = new Date(startDate);

            for (let i = 1; i <= 7; i++) {
                // Buat copy dari tanggal awal
                let currentDate = new Date(baseDate);
                // Tambahkan hari sesuai urutan iterasi (i-1 agar opsi 1 sama dengan hari H)
                currentDate.setDate(baseDate.getDate() + (i - 1));

                // Format ke YYYY-MM-DD
                let year = currentDate.getFullYear();
                let month = String(currentDate.getMonth() + 1).padStart(2, '0');
                let day = String(currentDate.getDate()).padStart(2, '0');

                // Gabungkan tanggal dan jam untuk format standard datetime-local HTML5
                let datetimeString = `${year}-${month}-${day}T${time}`;

                // Masukkan ke input & berikan efek visual highlight
                let targetInput = document.getElementById(`opsi_${i}`);
                if (targetInput) {
                    targetInput.value = datetimeString;

                    // Animasi UX agar user sadar form telah terisi
                    targetInput.classList.remove('bg-light');
                    targetInput.classList.add('bg-success-subtle');
                    setTimeout(() => {
                        targetInput.classList.remove('bg-success-subtle');
                        targetInput.classList.add('bg-light');
                    }, 800);
                }
            }
        }
    </script>

    {{-- KUSTOMISASI CSS UI --}}
    <style>
        /* Utility Background Colors */
        .bg-light-soft {
            background-color: #f8fafc;
        }

        .bg-primary-soft {
            background-color: #eff6ff;
        }

        .bg-success-soft {
            background-color: #f0fdf4;
        }

        .bg-danger-soft {
            background-color: #fef2f2;
        }

        .bg-success-subtle {
            background-color: #d1e7dd !important;
        }

        /* Typography & Effects */
        .tracking-wide {
            letter-spacing: 0.5px;
        }

        .shadow-xs {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        /* Hover Interactions */
        .hover-elevate {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-elevate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        /* Responsiveness Max Width */
        .max-w-sm {
            max-width: 400px;
        }
    </style>
@endsection