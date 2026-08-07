<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIM-KP</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    <script>
        // Cek LocalStorage sebelum halaman selesai dirender
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            document.documentElement.classList.add('sb-sidenav-toggled'); // Tambah class sementara ke HTML
        }
    </script>
    <style>
        /* Helper agar class dari HTML turun ke Body */
        html.sb-sidenav-toggled body { visibility: hidden; } /* Cegah FOUC kasar */
    </style>

    @yield('styles')
</head>

<body>
    <script>
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            document.body.classList.add('sb-sidenav-toggled');
            document.documentElement.classList.remove('sb-sidenav-toggled');
        }
    </script>

    <div id="wrapper">
        
        @include('layouts.sidebar')

        <div id="page-content-wrapper">
            
            <nav class="navbar navbar-expand bg-white border-bottom shadow-sm mb-3" style="height: 72px; padding: 0 1rem !important; position: sticky; top: 0; z-index: 1020;">
                
                <div class="d-flex align-items-center me-auto">
                    
                    <button class="btn btn-link text-dark p-0 border-0 shadow-none me-3 d-lg-none" id="mobileToggle">
                        <i class="bi bi-list" style="font-size: 2rem;"></i>
                    </button>
                    
                    <h4 class="mb-0 fw-bold text-dark d-none d-lg-block">@yield('title', 'Dashboard')</h4>
                    
                    <h5 class="mb-0 fw-bold text-dark d-lg-none text-truncate" style="max-width: 180px;">@yield('title', 'Dashboard')</h5>
                </div>

                <div class="d-flex align-items-center">
                    @if(Auth::user()->role === 'dosen')
                        @php
                            $notifCount = Auth::user()->unreadNotifications()->count();
                            $notifs = Auth::user()->unreadNotifications()->latest()->limit(10)->get();
                        @endphp
                        <div class="dropdown me-3">
                            <div class="notification-bell" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
                                <i class="bi bi-bell"></i>
                                @if($notifCount > 0)
                                    <span class="notification-badge">{{ $notifCount > 99 ? '99+' : $notifCount }}</span>
                                @endif
                            </div>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown border-0 shadow mt-3">
                                <div class="d-flex justify-content-between align-items-center px-3 py-2 notification-header">
                                    <span class="fw-bold">Notifikasi</span>
                                    @if($notifCount > 0)
                                        <form action="{{ route('dosen.notifikasi.read-all') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-link p-0 notification-readall">Tandai semua dibaca</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="notification-scroll">
                                    @forelse($notifs as $n)
                                        @php $nData = (array) $n->data; @endphp
                                        <a href="{{ route('dosen.notifikasi.go', $n->id) }}" class="notification-item">
                                            <span class="notification-icon"><i class="bi {{ $nData['icon'] ?? 'bi-bell' }}"></i></span>
                                            <span class="notification-body">
                                                <span class="notification-text">{{ $nData['pesan'] ?? '' }}</span>
                                                <span class="notification-time">{{ $n->created_at->diffForHumans() }}</span>
                                            </span>
                                            @if(is_null($n->read_at))
                                                <span class="notification-dot"></span>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="notification-empty">
                                            <i class="bi bi-bell-slash"></i>
                                            <span>Belum ada notifikasi</span>
                                        </div>
                                    @endforelse
                                </div>
                                <a href="{{ route('dosen.notifikasi.index') }}" class="notification-footer">Lihat Semua Notifikasi</a>
                            </div>
                        </div>
                    @endif

                    <div class="user-info d-none d-md-block me-3 text-end">
                        <div class="fw-bold small">{{ Auth::user()->name }}</div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.7rem;">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>

                    <div class="dropdown">
                        <div class="user-avatar" data-bs-toggle="dropdown" style="cursor: pointer; width: 40px; height: 40px;">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="User" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-success text-white fw-bold rounded-circle">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3">
                            @if(Auth::user()->role === 'mahasiswa')
                                <li><a class="dropdown-item" href="{{ route('mahasiswa.profile.edit') }}">Profil Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                    Logout
                                </a>
                                <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </div>
                </div>

            </nav>

            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebar = document.getElementById('sidebar-wrapper');

            // 1. Fungsi Toggle Sidebar & Simpan Status
            const toggleSidebar = (e) => {
                if(e) e.preventDefault();
                
                body.classList.toggle('sb-sidenav-toggled');
                
                // Simpan status ke LocalStorage (True = Tertutup/Mini)
                const isToggled = body.classList.contains('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', isToggled);

                // Trigger resize event untuk update chart/maps jika ada
                setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
            };

            // 2. Event Listeners
            if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
            if (mobileToggle) mobileToggle.addEventListener('click', toggleSidebar);

            // 3. Menutup Sidebar Mobile saat klik di luar (Overlay)
            document.addEventListener('click', (e) => {
                const isMobile = window.innerWidth < 992;
                const isToggled = body.classList.contains('sb-sidenav-toggled');
                const clickedInsideSidebar = sidebar && sidebar.contains(e.target);
                const clickedToggle = (mobileToggle && mobileToggle.contains(e.target));
                const clickedDesktopToggle = (sidebarToggle && sidebarToggle.contains(e.target));

                // Logika Mobile: Jika terbuka, dan klik di luar sidebar/tombol -> Tutup
                if (isMobile && isToggled && !clickedInsideSidebar && !clickedToggle && !clickedDesktopToggle) {
                    body.classList.remove('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', 'false'); // Perbarui state
                }
            });

            // 4. [BARU] Menutup Sidebar Mobile saat klik link menu (kecuali toggle collapse)
            const sidebarLinks = document.querySelectorAll('#sidebar-wrapper .list-group-item:not([data-bs-toggle="collapse"])');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    const isMobile = window.innerWidth < 992;
                    if (isMobile) {
                        body.classList.remove('sb-sidenav-toggled');
                        localStorage.setItem('sb|sidebar-toggle', 'false'); // Agar navigasi halaman selanjutnya dimuat tertutup
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>