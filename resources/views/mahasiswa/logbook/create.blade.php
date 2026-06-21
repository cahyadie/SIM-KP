@extends('layouts.app')

@section('title', 'Isi Logbook Mingguan')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('logbook.index', $magang->id) }}" class="btn btn-outline-secondary shadow-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form action="{{ route('logbook.store', $magang->id) }}" method="POST">
            @csrf

            <div class="card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-body p-4">
                    <div class="alert alert-light border border-info border-start border-5 mb-4 small">
                        <i class="bi bi-info-circle-fill text-info me-2"></i> Sistem telah secara otomatis menentukan jadwal
                        untuk <strong>Minggu Ke-{{ $minggu_ke }}</strong> berdasarkan tanggal mulai magang Anda. Silakan isi
                        kegiatan harian di bawah ini.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Minggu Ke-</label>
                            {{-- Dibuat readonly agar tidak bisa diubah --}}
                            <input type="number" name="minggu_ke" class="form-control bg-light" value="{{ $minggu_ke }}"
                                readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Mulai (Awal Minggu)</label>
                            {{-- Dibuat readonly agar tidak bisa diubah --}}
                            <input type="date" name="tgl_mulai" class="form-control bg-light fw-bold text-primary"
                                value="{{ $tgl_mulai_minggu->format('Y-m-d') }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Selesai (Akhir Minggu)</label>
                            {{-- Dibuat readonly agar tidak bisa diubah --}}
                            <input type="date" name="tgl_selesai" class="form-control bg-light fw-bold text-primary"
                                value="{{ $tgl_selesai_minggu->format('Y-m-d') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WADAH FORM HARIAN (Otomatis Di-generate dari Controller) --}}
            <div id="logbook-container">
                @foreach($hari_minggu_ini as $date)
                    @php
                        \Carbon\Carbon::setLocale('id');
                        $dateKey = $date->format('Y-m-d'); // Key JSON di Database
                        $displayDate = $date->translatedFormat('l, d F Y'); // Format tampilan: Senin, 01 Mei 2026
                    @endphp

                    <div class="card shadow-sm border-0 mb-3 rounded-4">
                        <div class="card-header bg-light fw-bold text-primary py-3">
                            <i class="bi bi-calendar-day me-2"></i> {{ $displayDate }}
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                {{-- Kolom Kegiatan --}}
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Kegiatan <span
                                            class="text-danger">*</span></label>
                                    <textarea name="log[{{ $dateKey }}][kegiatan]"
                                        class="form-control bg-light @error('log.' . $dateKey . '.kegiatan') is-invalid @enderror"
                                        rows="3" placeholder="Apa yang dikerjakan..."
                                        required>{{ old('log.' . $dateKey . '.kegiatan') }}</textarea>
                                    @error('log.' . $dateKey . '.kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Kolom Permasalahan --}}
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Permasalahan <span
                                            class="text-danger">*</span></label>
                                    <textarea name="log[{{ $dateKey }}][permasalahan]"
                                        class="form-control bg-light @error('log.' . $dateKey . '.permasalahan') is-invalid @enderror"
                                        rows="3" placeholder="Kendala yang dihadapi (Isi '-' jika tidak ada)..."
                                        required>{{ old('log.' . $dateKey . '.permasalahan') }}</textarea>
                                    @error('log.' . $dateKey . '.permasalahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Kolom Solusi --}}
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-bold">Solusi <span
                                            class="text-danger">*</span></label>
                                    <textarea name="log[{{ $dateKey }}][solusi]"
                                        class="form-control bg-light @error('log.' . $dateKey . '.solusi') is-invalid @enderror"
                                        rows="3" placeholder="Solusi penyelesaian (Isi '-' jika tidak ada)..."
                                        required>{{ old('log.' . $dateKey . '.solusi') }}</textarea>
                                    @error('log.' . $dateKey . '.solusi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>>

            <div class="d-grid mb-5 mt-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold rounded-3 shadow-sm">
                    <i class="bi bi-save me-2"></i> SIMPAN LOGBOOK MINGGUAN
                </button>
            </div>
        </form>
    </div>
@endsection