<div id="sidebar-wrapper">
    
    {{-- HEADER --}}
    <div class="sidebar-heading">
        <div class="logo-wrapper">
            <div class="logo-icon">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <span class="logo-text">SIM-KP</span>
        </div>
        <button id="sidebarToggle" title="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>

    {{-- MENU --}}
    <div class="sidebar-menu">
        <div class="list-group list-group-flush">
            
            @php 
                $user = Auth::user();
                $role = $user->role;
            @endphp

            {{-- DASHBOARD UMUM (Admin & Kaprodi) --}}
            @if($role === 'admin' || $role === 'kaprodi')
                <a href="{{ $role === 'kaprodi' ? route('kaprodi.dashboard') : route('admin.dashboard') }}" 
                   class="list-group-item {{ request()->routeIs('admin.dashboard', 'kaprodi.dashboard') ? 'active' : '' }}"
                   title="Dashboard">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            @endif

            {{-- MENU KAPRODI --}}
            @if($role === 'kaprodi')
                <a href="{{ route('kaprodi.pantauan-skp') }}" 
                   class="list-group-item {{ request()->routeIs('kaprodi.pantauan-skp') ? 'active' : '' }}"
                   title="Pantauan SKP (>30 Hari)">
                    <i class="bi bi-calendar-x"></i>
                    <span>Pantauan SKP</span>
                </a>

                {{-- RIWAYAT MAGANG KAPRODI --}}
                <a href="{{ route('kaprodi.riwayat-magang.index') }}" 
                   class="list-group-item {{ request()->routeIs('kaprodi.riwayat-magang.*') ? 'active' : '' }}"
                   title="Riwayat Magang">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Magang</span>
                </a>
            @endif

            {{-- MENU ADMIN --}}
            @if($role === 'admin')
                
                {{-- MENU COLLAPSE: MANAJEMEN DATA --}}
                <a href="#collapseManajemenData" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('admin.riwayat-magang.*', 'admin.skp*') ? 'true' : 'false' }}" 
                   aria-controls="collapseManajemenData"
                   class="list-group-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.riwayat-magang.*', 'admin.skp*') ? 'text-primary fw-bold' : '' }}">
                    <div>
                        <i class="bi bi-folder2-open"></i> <span>Manajemen Data</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
                </a>

                {{-- ISI COLLAPSE --}}
                <div class="collapse {{ request()->routeIs('admin.riwayat-magang.*', 'admin.skp*') ? 'show' : '' }}" id="collapseManajemenData">
                    <div class="list-group list-group-flush bg-light">
                        
                        {{-- RIWAYAT MAGANG ADMIN --}}
                        <a href="{{ route('admin.riwayat-magang.index') }}" 
                           class="list-group-item ps-5 {{ request()->routeIs('admin.riwayat-magang.*') ? 'active' : '' }}"
                           style="border-left: 3px solid transparent; {{ request()->routeIs('admin.riwayat-magang.*') ? 'border-left-color: var(--bs-primary);' : '' }}">
                            <i class="bi bi-clock-history"></i> <span>Riwayat Magang</span>
                        </a>
                        
                        {{-- DATA SKP ADMIN --}}
                        <a href="{{ route('admin.skp') }}" 
                           class="list-group-item ps-5 {{ request()->routeIs('admin.skp', 'admin.skp.show') ? 'active' : '' }}"
                           style="border-left: 3px solid transparent; {{ request()->routeIs('admin.skp*') ? 'border-left-color: var(--bs-primary);' : '' }}">
                            <i class="bi bi-file-earmark-check"></i> <span>Data SKP</span>
                        </a>

                    </div>
                </div>
                {{-- END MENU COLLAPSE --}}

                {{-- PUSAT INFORMASI (Kelola) --}}
                <a href="{{ route('admin.pengumuman.index') }}" 
                   class="list-group-item {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}"
                   title="Kelola Pengumuman">
                    <i class="bi bi-megaphone"></i> <span>Kelola Pengumuman</span>
                </a>
            @endif

            {{-- MENU MAHASISWA --}}
            @if($role === 'mahasiswa')
                <a href="{{ route('mahasiswa.dashboard') }}" class="list-group-item {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> <span>Dashboard Mhs</span></a>
                
                {{-- MENU COLLAPSE: MAGANG (MAHASISWA) --}}
                <a href="#collapseMagangMhs" 
                   data-bs-toggle="collapse" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs('magang.create', 'logbook.*', 'mahasiswa.riwayat-magang.*') ? 'true' : 'false' }}" 
                   aria-controls="collapseMagangMhs"
                   class="list-group-item d-flex justify-content-between align-items-center {{ request()->routeIs('magang.create', 'logbook.*', 'mahasiswa.riwayat-magang.*') ? 'text-primary fw-bold' : '' }}">
                    <div>
                        <i class="bi bi-journal-bookmark-fill"></i> <span>Magang</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
                </a>

                {{-- ISI COLLAPSE MAGANG --}}
                <div class="collapse {{ request()->routeIs('magang.create', 'logbook.*', 'mahasiswa.riwayat-magang.*') ? 'show' : '' }}" id="collapseMagangMhs">
                    <div class="list-group list-group-flush bg-light">
                        <a href="{{ route('magang.create') }}" 
                           class="list-group-item ps-5 {{ request()->routeIs('magang.create') ? 'active' : '' }}"
                           style="border-left: 3px solid transparent; {{ request()->routeIs('magang.create') ? 'border-left-color: var(--bs-primary);' : '' }}">
                            <i class="bi bi-pencil-square"></i> <span>Daftar Magang</span>
                        </a>

                        @php 
                            $mhs = $user->mahasiswa;
                            $aktif = $mhs?->magangs()->where('status_validasi', 'diterima')->latest()->first(); 
                        @endphp
                        
                        @if($aktif)
                            <a href="{{ route('logbook.index', $aktif->id) }}" 
                               class="list-group-item ps-5 {{ request()->routeIs('logbook.*') ? 'active' : '' }}"
                               style="border-left: 3px solid transparent; {{ request()->routeIs('logbook.*') ? 'border-left-color: var(--bs-primary);' : '' }}">
                                <i class="bi bi-journal-text"></i> <span>Isi Logbook</span>
                            </a>
                        @endif

                        <a href="{{ route('mahasiswa.riwayat-magang.index') }}" 
                           class="list-group-item ps-5 {{ request()->routeIs('mahasiswa.riwayat-magang.*') ? 'active' : '' }}"
                           style="border-left: 3px solid transparent; {{ request()->routeIs('mahasiswa.riwayat-magang.*') ? 'border-left-color: var(--bs-primary);' : '' }}">
                            <i class="bi bi-clock-history"></i> <span>Riwayat Magang</span>
                        </a>
                    </div>
                </div>
                {{-- END MENU COLLAPSE MAGANG --}}

                <a href="{{ route('mahasiswa.seminar') }}" class="list-group-item {{ request()->routeIs('mahasiswa.seminar') ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> <span>SKP</span></a>
            @endif

            {{-- MENU DOSEN --}}
            @if($role === 'dosen')
                <a href="{{ route('dosen.dashboard') }}" class="list-group-item {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill"></i> <span>Dashboard</span></a>
                <a href="{{ route('dosen.bimbingan.index') }}" class="list-group-item {{ request()->routeIs('dosen.bimbingan.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> <span>Bimbingan</span></a>
                <a href="{{ route('dosen.skp.index') }}" class="list-group-item {{ request()->routeIs('dosen.skp.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> <span>Jadwal SKP</span>
                </a>
                
                {{-- RIWAYAT MAGANG DOSEN --}}
                <a href="{{ route('dosen.riwayat-magang.index') }}" class="list-group-item {{ request()->routeIs('dosen.riwayat-magang.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> <span>Riwayat Magang</span>
                </a>
            @endif

            {{-- =========================================
                 DIREKTORI (MENU UMUM UNTUK SEMUA ROLE)
                 ========================================= --}}
            
            <a href="{{ route('lowongan.index') }}" 
               class="list-group-item {{ request()->routeIs('lowongan.index') ? 'active' : '' }}"
               title="Info Lowongan Magang dari Prodi">
                <i class="bi bi-briefcase"></i> <span>Info Lowongan Magang</span>
            </a>

            <a href="{{ route('perusahaan.index') }}" 
               class="list-group-item {{ request()->routeIs('perusahaan.*') ? 'active' : '' }}"
               title="Tempat Magang">
                <i class="bi bi-buildings"></i> <span>Riwayat Tempat Magang</span>
            </a>

            {{-- =========================================
                 MENU BAWAH (PROFIL & PENGATURAN)
                 ========================================= --}}
                 
            {{-- PENGATURAN AKUN (Admin) --}}
            @if($role === 'admin')
                <a href="{{ route('admin.users.index') }}" 
                   class="list-group-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   title="Manajemen Akun">
                    <i class="bi bi-people-fill"></i> <span>Manajemen Akun</span>
                </a>
            @endif

            {{-- PROFIL SAYA (Mahasiswa) --}}
            @if($role === 'mahasiswa')
                <a href="{{ route('profile.edit') }}" class="list-group-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> <span>Profil Saya</span>
                </a>
            @endif

        </div>
    </div>

    {{-- FOOTER --}}
    <div class="sidebar-footer">
        <a href="{{ route('logout') }}" class="btn-logout" 
           onclick="event.preventDefault(); document.getElementById('logout-form-sb').submit();">
            <i class="bi bi-box-arrow-left"></i> <span>Logout</span>
        </a>
        <form id="logout-form-sb" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>

</div>