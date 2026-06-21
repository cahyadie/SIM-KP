@extends('layouts.app')
@section('title', 'Edit Pengumuman')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm max-w-700 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Edit Info Lowongan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pengumuman.update', $pengumuman->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Judul Pekerjaan / Perusahaan</label>
                <input type="text" name="judul" class="form-control" value="{{ $pengumuman->judul }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi / Teks Pembuka</label>
                <textarea name="deskripsi" class="form-control" rows="3" required>{{ $pengumuman->deskripsi }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="{{ $pengumuman->lokasi }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Info Gaji</label>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="info_gaji" id="gajiUnpaid" value="" {{ (old('info_gaji', $pengumuman->info_gaji) == '' || strtolower($pengumuman->info_gaji) == 'unpaid') ? 'checked' : '' }}>
                        <label class="form-check-label" for="gajiUnpaid">Unpaid (Tidak ada gaji)</label>
                    </div>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="info_gaji" id="gajiPaid" value="Paid" {{ (old('info_gaji', $pengumuman->info_gaji) == 'Paid') ? 'checked' : '' }}>
                        <label class="form-check-label" for="gajiPaid">Paid (Berbayar)</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fasilitas Tempat Tinggal</label>
                    <input type="text" name="info_fasilitas" class="form-control" value="{{ $pengumuman->info_fasilitas }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Syarat Khusus / Diutamakan</label>
                    <input type="text" name="syarat_tambahan" class="form-control" value="{{ $pengumuman->syarat_tambahan }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Target Mahasiswa</label>
                <input type="text" name="target_angkatan" class="form-control" value="{{ $pengumuman->target_angkatan }}">
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Link Pendaftaran</label>
                <input type="url" name="link_pendaftaran" class="form-control" value="{{ $pengumuman->link_pendaftaran }}" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection