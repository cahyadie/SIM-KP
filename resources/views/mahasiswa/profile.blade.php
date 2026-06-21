@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid px-4">
    
    {{-- Header & Tombol Kembali --}}
    <div class="d-flex align-items-center mb-4">
    </div>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="row g-0">
            
            {{-- KOLOM KIRI: TAMPILAN PROFIL (Visual) --}}
            <div class="col-md-4 col-lg-3 bg-primary text-white d-flex flex-column align-items-center justify-content-center p-5 text-center position-relative">
                {{-- Dekorasi Background --}}
                <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" 
                     style="background: radial-gradient(circle at top left, rgba(255,255,255,0.3), transparent);">
                </div>
                
                <div class="position-relative z-1">
                    <div class="mb-3">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="User Avatar" class="rounded-circle border border-4 border-white shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle shadow-sm" style="width: 110px; height: 110px; font-size: 2.5rem; font-weight: bold;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-white-50 mb-3 small">{{ $user->email }}</p>
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-mortarboard-fill me-1"></i> Mahasiswa
                    </span>
                </div>
            </div>

            {{-- KOLOM KANAN: FORM INPUT --}}
            <div class="col-md-8 col-lg-9 p-4 p-lg-5 bg-white">
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm bg-danger-soft mb-4 py-2">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row g-4">
                        
                        {{-- Data Akademik --}}
                        <div class="col-12">
                            <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-card-heading me-1"></i> Data Akademik
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">NIM <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-postcard"></i></span>
                                        <input type="text" name="nim" class="form-control bg-white" value="{{ $mahasiswa->nim ?? '' }}" required placeholder="Contoh: 2022014xxxx">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Angkatan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-event"></i></span>
                                        <input type="number" name="angkatan" class="form-control bg-white" value="{{ $mahasiswa->angkatan ?? '' }}" required placeholder="Contoh: 2022">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">Program Studi</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-mortarboard"></i></span>
                                        <input type="text" class="form-control bg-light text-secondary border-start-0" value="Teknologi Informasi" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kontak --}}
                        <div class="col-12">
                            <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                <i class="bi bi-telephone me-1"></i> Kontak
                            </h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-muted">No. HP / WhatsApp <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                        <input type="text" name="no_hp" class="form-control bg-white" value="{{ $mahasiswa->no_hp ?? '' }}" required placeholder="0812xxxx">
                                    </div>
                                    <div class="form-text small">Opsional</div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="col-12 text-end border-top pt-3 mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
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