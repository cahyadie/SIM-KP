@extends('layouts.app')
@section('title', 'Kelola Pengumuman')
@section('content')
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('admin.pengumuman.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah
            Pengumuman</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Judul Pengumuman</th>
                        <th>Target Angkatan</th>
                        <th>Tanggal Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengumuman as $p)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $p->judul }}</td>
                            <td>{{ $p->target_angkatan ?? 'Semua' }}</td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.pengumuman.edit', $p->id) }}"
                                        class="btn btn-warning btn-sm text-white">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    {{-- Form Hapus --}}
                                    <form action="{{ route('admin.pengumuman.destroy', $p->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($pengumuman->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-2">
                {{ $pengumuman->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection