@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')

    <style>
        :root {
            --primary-color: #004b23;
            --primary-light: #e8f5e9;
            --primary-gradient: linear-gradient(135deg, #004b23, #007135);
            --success-light: #ecfdf3;
            --warning-light: #fff7ed;
            --dark-text: #111827;
            --muted-text: #6b7280;
            --border-color: #e5e7eb;
        }

        body {
            background-color: #f8fafc;
        }

        .dashboard-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            transition: 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .dashboard-header h3 {
            font-size: 2rem;
            color: var(--dark-text);
        }

        .dashboard-header p {
            color: var(--muted-text);
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .status-card {
            background: var(--primary-gradient);
            border-radius: 24px;
            color: white;
            overflow: hidden;
            position: relative;
        }

        .status-card::before {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            top: -60px;
            right: -60px;
        }

        .status-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .table-modern thead th {
            background: #f8fafc;
            border: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            padding: 16px;
        }

        .table-modern tbody td {
            border-top: 1px solid #f1f5f9;
            padding: 18px 16px;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background-color: #fafafa;
        }

        .company-name {
            font-weight: 600;
            color: var(--dark-text);
        }

        .badge-pending {
            background: var(--warning-light);
            color: #ea580c;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }

        .guide-step {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
        }

        .guide-step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .guide-step h6 {
            margin-bottom: 2px;
            font-weight: 700;
            color: var(--dark-text);
            font-size: 14px;
        }

        .guide-step p {
            margin-bottom: 0;
            color: var(--muted-text);
            font-size: 12.5px;
            line-height: 1.4;
        }

        .section-title {
            font-weight: 700;
            color: var(--dark-text);
            font-size: 1.1rem;
        }

        .mini-badge {
            background: var(--primary-light);
            color: var(--primary-color);
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
        }

        .empty-state p {
            margin-top: 10px;
            color: var(--muted-text);
        }
    </style>

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- STATUS CARD --}}
    @if($magang)
        <div class="status-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-4 position-relative">

                <div>
                    <span class="mini-badge mb-3 d-inline-block">
                        Status Magang Saat Ini
                    </span>

                    @php
                        $hari_ini = \Carbon\Carbon::now()->startOfDay();
                        $tgl_mulai = \Carbon\Carbon::parse($magang->tanggal_mulai)->startOfDay();
                        $tgl_selesai = \Carbon\Carbon::parse($magang->tanggal_selesai)->startOfDay();
                    @endphp

                    <h3 class="fw-bold mb-2">
                        {{-- PERUBAHAN LOGIKA: Cek DB Status dulu, baru fallback ke perbandingan tanggal --}}
                        @if($magang->status_kegiatan == 'selesai')
                            Selesai Magang
                        @elseif($magang->status_kegiatan == 'skp')
                            Tahap Seminar KP
                        @elseif($hari_ini->gt($tgl_selesai))
                            Selesai Magang
                        @elseif($magang->status_kegiatan == 'magang' || ($hari_ini->gte($tgl_mulai) && $hari_ini->lte($tgl_selesai)))
                            Sedang Magang
                        @else
                            Belum Mulai
                        @endif
                    </h3>

                    <p class="mb-0 text-white-50">
                        {{ $magang->perusahaan->nama_perusahaan ?? '-' }}
                    </p>
                </div>

                <div class="status-icon">
                    <i class="bi bi-briefcase"></i>
                </div>

            </div>
        </div>
    @endif

    {{-- ALERT AGENDA SKP MENDATANG --}}
    @if($magang && $magang->status_jadwal_skp == 'disetujui' && $magang->status_skp == 'belum')
        <div
            class="alert bg-white border-0 shadow-sm d-flex align-items-center mb-4 rounded-4 border-start border-5 border-primary">
            <div class="bg-primary-light text-primary rounded-circle d-flex justify-content-center align-items-center me-3"
                style="width: 48px; height: 48px; flex-shrink:0;">
                <i class="bi bi-calendar-event fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold text-primary mb-1">Agenda Seminar (SKP) Mendatang</h6>
                <p class="mb-0 text-secondary small">
                    Jadwal: <strong
                        class="text-dark">{{ \Carbon\Carbon::parse($magang->jadwal_terpilih)->format('l, d F Y - H:i') }}
                        WIB</strong>
                    <br>
                    <!-- Lokasi: <span class="badge bg-light text-danger border border-danger ms-1"><i class="bi bi-geo-alt-fill me-1"></i>{{ $magang->ruangan_skp }}</span> -->
                </p>
                <p class="text small mb-0">
                    Jangan Lupa Mengabarkan Dosen Pembimbing Untuk Ruangannya yaa!!
                </p>
            </div>
        </div>
    @endif

    {{-- RIWAYAT MAGANG --}}
    <div class="dashboard-card card mb-5">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="section-title mb-1">Riwayat Magang</h5>
                    <p class="text-muted small mb-0">
                        Daftar seluruh aktivitas magang mahasiswa.
                    </p>
                </div>

                <span class="mini-badge">
                    {{ $riwayat_magang->count() }} Data
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Perusahaan</th>
                            <th>Periode</th>
                            <th>Dosen Pembimbing</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($riwayat_magang as $m)
                            <tr>

                                <td>
                                    <div class="company-name">
                                        {{ $m->perusahaan->nama_perusahaan }}
                                    </div>

                                    @php
                                        // PERUBAHAN LOGIKA: Cek dinamis untuk tabel riwayat
                                        $m_hari_ini = \Carbon\Carbon::now()->startOfDay();
                                        $m_mulai = \Carbon\Carbon::parse($m->tanggal_mulai)->startOfDay();
                                        $m_selesai = \Carbon\Carbon::parse($m->tanggal_selesai)->startOfDay();

                                        $status_tampil = ucfirst($m->status_kegiatan);

                                        // Buat penamaan status lebih rapi & terstruktur
                                        if ($m->status_kegiatan == 'skp') {
                                            $status_tampil = 'Tahap Seminar KP';
                                        } elseif ($m->status_kegiatan == 'selesai') {
                                            $status_tampil = 'Selesai Magang';
                                        } else {
                                            if ($m_hari_ini->between($m_mulai, $m_selesai) || $m->status_kegiatan == 'magang') {
                                                $status_tampil = 'Sedang Magang';
                                            } elseif ($m_hari_ini->gt($m_selesai)) {
                                                $status_tampil = 'Selesai Magang';
                                            } elseif ($m_hari_ini->lt($m_mulai)) {
                                                $status_tampil = 'Belum Mulai';
                                            }
                                        }
                                    @endphp

                                    <small class="text-muted">
                                        Status : {{ $status_tampil }}
                                    </small>
                                </td>

                                <td>
                                    <div class="fw-semibold text-dark">
                                        {{ \Carbon\Carbon::parse($m->tanggal_mulai)->format('d M Y') }}
                                    </div>

                                    <small class="text-muted">
                                        sampai
                                        {{ \Carbon\Carbon::parse($m->tanggal_selesai)->format('d M Y') }}
                                    </small>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ $m->dosen->name ?? 'Belum Ditentukan' }}
                                    </div>
                                </td>

                                <td class="text-center">

                                    @if($m->status_validasi == 'diterima')

                                        <a href="{{ route('logbook.index', $m->id) }}"
                                            class="btn btn-primary btn-sm rounded-3 px-3 shadow-sm">

                                            <i class="bi bi-journal-text me-1"></i>
                                            Logbook
                                        </a>

                                    @else

                                        <span class="badge-pending">
                                            Menunggu Validasi
                                        </span>

                                    @endif

                                    <a href="{{ route('mahasiswa.riwayat-magang.edit', $m->id) }}"
                                        class="btn btn-outline-secondary btn-sm rounded-3 px-3 shadow-sm ms-1">

                                        <i class="bi bi-pencil me-1"></i>
                                        Edit
                                    </a>

                                </td>

                            </tr>
                        @empty

                            <tr>
                                <td colspan="4">

                                    <div class="empty-state">
                                        <i class="bi bi-folder-x"></i>
                                        <p>Belum ada riwayat magang.</p>
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- PANDUAN --}}
    <div class="row g-4">

        {{-- ALUR MAGANG --}}
        <div class="col-lg-6">
            <div class="dashboard-card card h-100">
                <div class="card-body p-3">

                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <h5 class="section-title mb-1">Alur Magang</h5>
                        </div>
                    </div>

                    @php
                        $alurMagang = [
                            ['title' => 'Mencari Tempat Magang', 'desc' => 'Mahasiswa mencari perusahaan secara mandiri.'],
                            ['title' => 'Pendaftaran ke TU Teknik', 'desc' => 'Melakukan pendaftaran magang ke admin TU Teknik.'],
                            ['title' => 'Persetujuan Dosen', 'desc' => 'Meminta persetujuan dan tanda tangan dosen pembimbing.'],
                            ['title' => 'Validasi Berkas', 'desc' => 'Menyerahkan kembali berkas yang telah disetujui ke TU Teknik.'],
                            ['title' => 'Surat Kaprodi', 'desc' => 'Mendapatkan surat izin pelaksanaan kerja praktek.'],
                            ['title' => 'Tanda Tangan Kaprodi', 'desc' => 'Meminta tanda tangan dari kaprodi setelah mendapatkan surat sebelumnya.'],
                            ['title' => 'Pelaksanaan Magang', 'desc' => 'Mulai magang dan mengisi logbook secara rutin setiap Minggu.'],
                        ];
                    @endphp

                    @foreach($alurMagang as $index => $item)
                        <div class="guide-step">
                            <div class="step-number">{{ $index + 1 }}</div>
                            <div>
                                <h6>{{ $item['title'] }}</h6>
                                <p>{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- PANDUAN SKP --}}
        <div class="col-lg-6">
            <div class="dashboard-card card h-100">
                <div class="card-body p-3">

                    <div class="d-flex align-items-center mb-3">

                        <div>
                            <h5 class="section-title mb-1">Panduan Seminar Kerja Praktik (SKP)</h5>
                        </div>
                    </div>

                    @php
                        $alurSkp = [
                            ['title' => 'Surat Selesai Magang', 'desc' => 'Mengumpulkan surat selesai magang ke TU Teknik.'],
                            ['title' => 'Mendapatkan Surat Pinjam Ruangan Dan Berita Acara', 'desc' => 'Setelah Step 1 akan mendapatkan surat pinjam ruangan dan berita acara.'],
                            ['title' => 'Konfirmasi ke Dosen Pembimbing', 'desc' => 'Konfirmasi dan Cocokkan Jadwal Dengan Dosen Pembimbing Menggunakan Surat Berita Acara.'],
                            ['title' => 'Peminjaman Ruangan', 'desc' => 'Setelah Jadwal di Tentukan Lakukan Pinjam Ruangak Ke Faportek .'],
                            ['title' => 'Pelaksanaan Seminar', 'desc' => 'Melaksanakan seminar kerja praktek sesuai jadwal.'],
                            ['title' => 'Penyerahan Nilai', 'desc' => 'Menyerahkan nilai dan daftar kehadiran seminar ke Tu Prodi. '],
                        ];
                    @endphp

                    @foreach($alurSkp as $index => $item)
                        <div class="guide-step">
                            <div class="step-number">{{ $index + 1 }}</div>
                            <div>
                                <h6>{{ $item['title'] }}</h6>
                                <p>{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>

@endsection