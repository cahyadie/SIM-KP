@extends('layouts.app')
@section('title', 'Tambah Pengumuman')
@section('content')
<div class="card border-0 shadow-sm max-w-700 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Buat Info Lowongan Baru</h5>
    </div>
    <div class="card-body">
        
        {{-- BLOK PENAMPIL ERROR TAMBAHAN --}}
        @if ($errors->any())
            <div class="alert alert-danger pb-0">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pengumuman.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Perusahaan <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control" placeholder="Cth:Telkom Indonesia" value="{{ old('judul') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi / Teks Pembuka <span class="text-danger">*</span></label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Cth: Telkom membuka peluang Magang yang bisa dikonversi menjadi TA..." required>{{ old('deskripsi') }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" placeholder="Cth: Jakarta atau Bandung" value="{{ old('lokasi') }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold d-block">Info Gaji</label>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="info_gaji" id="gajiUnpaid" value="" {{ old('info_gaji') == '' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gajiUnpaid">Unpaid (Tidak ada gaji)</label>
                    </div>
                    <div class="form-check form-check-inline mt-1">
                        <input class="form-check-input" type="radio" name="info_gaji" id="gajiPaid" value="Paid" {{ old('info_gaji') == 'Paid' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gajiPaid">Paid (Berbayar)</label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fasilitas Tempat Tinggal</label>
                    <input type="text" name="info_fasilitas" class="form-control" placeholder="Cth: Tidak ada fasilitas" value="{{ old('info_fasilitas') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Syarat Khusus / Diutamakan</label>
                    <input type="text" name="syarat_tambahan" class="form-control" placeholder="Cth: Diutamakan yang tinggal di Jakarta" value="{{ old('syarat_tambahan') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Target Mahasiswa</label>
                <input type="text" name="target_angkatan" class="form-control" placeholder="Cth: Ini khusus mahasiswa 2022" value="{{ old('target_angkatan') }}">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Link Pendaftaran <span class="text-danger">*</span></label>
                <input type="url" name="link_pendaftaran" class="form-control" placeholder="Cth: https://docs.google.com/..." value="{{ old('link_pendaftaran') }}" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Sebarkan Pengumuman</button>
        </form>
    </div>
</div>
@endsection