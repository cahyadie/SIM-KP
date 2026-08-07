@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0">Notifikasi</h4>
            <small class="text-muted">Semua aktivitas mahasiswa bimbingan Anda</small>
        </div>
        @if($notifikasi->total() > 0)
            <form action="{{ route('dosen.notifikasi.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm">
                    <i class="bi bi-check2-all me-1"></i> Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            @forelse($notifikasi as $n)
                @php $nData = (array) $n->data; @endphp
                <a href="{{ route('dosen.notifikasi.go', $n->id) }}"
                   class="notification-list-item {{ is_null($n->read_at) ? 'unread' : '' }}">
                    <span class="notification-icon">
                        <i class="bi {{ $nData['icon'] ?? 'bi-bell' }}"></i>
                    </span>
                    <span class="notification-body">
                        <span class="notification-text">{{ $nData['pesan'] ?? '' }}</span>
                        <span class="notification-time">
                            {{ $n->created_at->format('d M Y, H:i') }}
                            @if(is_null($n->read_at))
                                <span class="badge bg-primary-subtle text-primary rounded-pill ms-1">Baru</span>
                            @endif
                        </span>
                    </span>
                    <i class="bi bi-chevron-right notification-arrow"></i>
                </a>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bell-slash" style="font-size: 2.5rem;"></i>
                    <p class="mt-2 mb-0">Belum ada notifikasi</p>
                </div>
            @endforelse
        </div>
        @if($notifikasi->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $notifikasi->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
