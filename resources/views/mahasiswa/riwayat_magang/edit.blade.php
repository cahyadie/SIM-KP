@extends('layouts.app')

@section('title', 'Edit Data Magang')

@section('content')
<style>
    .edit-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }

    .edit-header {
        background: linear-gradient(135deg, #004b23, #007135);
        padding: 20px 24px;
    }

    .edit-header h5 {
        color: white;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .edit-header p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        margin-bottom: 0;
    }

    .form-label {
        margin-bottom: 0.2rem;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .company-badge {
        background: #e8f5e9;
        color: #004b23;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('mahasiswa.riwayat-magang.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="edit-card card">
            <div class="edit-header">
                <h5><i class="bi bi-pencil-square me-2"></i>Edit Data Magang</h5>
                <p>{{ $magang->perusahaan->nama_perusahaan }}</p>
            </div>

            <div class="card-body p-4">

                <div class="company-badge mb-4 d-flex align-items-center gap-2">
                    <i class="bi bi-building"></i>
                    {{ $magang->perusahaan->nama_perusahaan }}
                    <span class="text-muted fw-normal ms-2" style="font-size: 0.8rem;">
                        ({{ $magang->perusahaan->alamat }})
                    </span>
                </div>

                <form action="{{ route('mahasiswa.riwayat-magang.update', $magang->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <div class="col-12">
                            <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-calendar-range me-1"></i> Durasi Magang
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_mulai" class="form-control"
                                           value="{{ old('tanggal_mulai', $magang->tanggal_mulai->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_selesai" class="form-control"
                                           value="{{ old('tanggal_selesai', $magang->tanggal_selesai->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle me-1"></i> Detail Magang
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tema Magang <span class="text-danger">*</span></label>
                                    <input type="text" name="tema_magang" class="form-control"
                                           value="{{ old('tema_magang', $magang->tema_magang) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Gaji <span class="text-danger">*</span></label>
                                    <select name="status_gaji" class="form-select" required>
                                        <option value="paid" {{ $magang->status_gaji == 'paid' ? 'selected' : '' }}>Paid (Digaji)</option>
                                        <option value="unpaid" {{ $magang->status_gaji == 'unpaid' ? 'selected' : '' }}>Unpaid (Sukarela)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-12">
                            <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-person-check me-1"></i> Dosen Pembimbing
                            </h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label">Pilih Dosen <span class="text-danger">*</span></label>
                                    <select name="dosen_id" class="form-select" required>
                                        <option value="" disabled>-- Pilih Dosen --</option>
                                        @foreach($daftar_dosen as $dosen)
                                            <option value="{{ $dosen->id }}" {{ $magang->dosen_id == $dosen->id ? 'selected' : '' }}>
                                                {{ $dosen->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> -->

                        <div class="col-12 text-end border-top pt-4 mt-4">
                            <a href="{{ route('mahasiswa.riwayat-magang.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i> Simpan Perubahan
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection