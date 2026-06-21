<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIM-KP</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
            
            <nav class="navbar navbar-expand-lg navbar-light top-navbar">
                <div class="container-fluid align-items-center">
                    <button class="btn d-lg-none me-2" id="mobileToggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    
                    <div class="page-header-title ms-1 ms-lg-0">
                        <h4 class="mb-0 fw-bold text-dark">@yield('title', 'Dashboard')</h4>
                    </div>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="user-info d-none d-md-block me-3 text-end">
                            <div class="fw-bold small">{{ Auth::user()->name }}</div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.7rem;">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>

                        <div class="dropdown">
                            <div class="user-avatar" data-bs-toggle="dropdown">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="User">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 bg-primary text-white fw-bold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-3">
                                @if(Auth::user()->role === 'mahasiswa')
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil Saya</a></li>
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
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>