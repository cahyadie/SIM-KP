<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use App\Models\Magang;
use App\Models\Logbook;
use App\Models\Review;
use App\Models\Pengumuman;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    private array $kegiatan = [
        'Melakukan analisis kebutuhan sistem dan menyusun dokumentasi teknis bersama tim.',
        'Merancang arsitektur database menggunakan diagram ERD dan normalisasi tabel.',
        'Mengimplementasikan fitur autentikasi dan manajemen pengguna dengan Laravel.',
        'Melakukan unit testing dan integrasi pada modul-modul aplikasi yang dikembangkan.',
        'Menyusun laporan progress mingguan dan mempresentasikannya pada pembimbing lapangan.',
        'Melakukan deployment aplikasi ke server staging melalui pipeline CI/CD.',
        'Melakukan optimasi query database untuk meningkatkan performa halaman aplikasi.',
        'Mengintegrasikan layanan pembayaran pihak ketiga pada modul transaksi sistem.',
        'Melakukan code review dan refactoring pada kode dari pengembang sebelumnya.',
        'Membuat dokumentasi API menggunakan Swagger untuk kebutuhan integrasi frontend.',
        'Mengembangkan fitur notifikasi real-time menggunakan WebSocket dan Laravel Echo.',
        'Melakukan pengujian keamanan aplikasi dasar menggunakan tools OWASP ZAP.',
    ];

    private array $kendala = [
        'Perubahan kebutuhan dari klien menyebabkan penyesuaian jadwal pengerjaan modul.',
        'Koneksi internet tidak stabil saat proses deployment ke server produksi utama.',
        '-',
        'Terdapat konflik pada merger kode dari branch pengembangan yang berbeda.',
        '-',
        'Dokumentasi teknis dari sistem lama tidak lengkap dan sulit dipahami alurnya.',
        '-',
        'Versi library yang digunakan tidak kompatibel dengan sistem operasi server.',
        '-',
        'Data sampel pengujian tidak mencakup seluruh skenario edge case yang dibutuhkan.',
        '-',
        'Kesalahan konfigurasi environment menyebabkan aplikasi gagal berjalan di server.',
    ];

    private array $solusi = [
        'Melakukan iterasi perencanaan dan berkoordinasi ulang dengan klien via meeting.',
        'Menggunakan koneksi internet cadangan 4G dan deploy di luar jam sibuk kantor.',
        '-',
        'Melakukan diskusi tim untuk menyelesaikan konflik secara manual melalui Git.',
        '-',
        'Melakukan reverse engineering pada sistem lama untuk memahami alur bisnisnya.',
        '-',
        'Melakukan upgrade library ke versi stabil terbaru dan uji ulang modul terdampak.',
        '-',
        'Menambahkan skenario pengujian berdasarkan hasil eksplorasi manual tim QA.',
        '-',
        'Memeriksa ulang seluruh konfigurasi environment dan memperbaiki variabel error.',
    ];

    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Logbook::truncate();
        Review::truncate();
        Magang::truncate();
        Perusahaan::truncate();
        Mahasiswa::truncate();
        Pengumuman::truncate();
        User::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ============================================================
        // 1. USERS
        // ============================================================
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $kaprodi = User::create([
            'name' => 'Dr. Ir. Ahmad Zainuddin, M.T.',
            'email' => 'kaprodi@test.com',
            'role' => 'kaprodi',
            'password' => Hash::make('password'),
        ]);

        $dosen = [];
        $dataDosen = [
            ['Dr. Agus Pratomo, S.T., M.Eng.', 'dosen.a@test.com', '198501012010121001'],
            ['Dr. Sri Wahyuni, S.Kom., M.Cs.', 'dosen.b@test.com', '198702032015042002'],
            ['Dr. Dimas Ardiyanto, S.T., M.Kom.', 'dosen.c@test.com', '197803152008121003'],
        ];
        foreach ($dataDosen as $dd) {
            $dosen[] = User::create([
                'name' => $dd[0],
                'email' => $dd[1],
                'role' => 'dosen',
                'nomor_induk' => $dd[2],
                'password' => Hash::make('password'),
            ]);
        }

        // ============================================================
        // 2. MAHASISWA (20)
        // ============================================================
        // Index:     0-4  Aktif,  5-7  SKP lulus,  8-12 Proses Seminar,
        //           13-14 Belum Mulai,  15-16 Multi,  17-19 Tanpa Magang
        $dataMhs = [
            ['Aditya Pratama Nugroho', 'm1@test.com', '20240140001', '2024', '081234560001'],
            ['Bella Ramadhani Putri', 'm2@test.com', '20240140002', '2024', '081234560002'],
            ['Cipto Wibowo Sulistyo', 'm3@test.com', '20240140003', '2024', '081234560003'],
            ['Dinda Ayu Safitri', 'm4@test.com', '20240140004', '2024', '081234560004'],
            ['Eka Wahyuni Lestari', 'm5@test.com', '20240140005', '2024', '081234560005'],
            ['Farhan Maulana Hakim', 'm6@test.com', '20240140006', '2024', '081234560006'],
            ['Gina Permata Sari', 'm7@test.com', '20240140007', '2024', '081234560007'],
            ['Hadi Prasetyo Utomo', 'm8@test.com', '20240140008', '2024', '081234560008'],
            ['Intan Nuraini Rahmawati', 'm9@test.com', '20240140009', '2024', '081234560009'],
            ['Joko Susilo Adi', 'm10@test.com', '20240140010', '2024', '081234560010'],
            ['Kiki Amalia Fitriani', 'm11@test.com', '20240140011', '2024', '081234560011'],
            ['Lintang Puspita Dewi', 'm12@test.com', '20240140012', '2024', '081234560012'],
            ['Mulyadi Saputra Wardhana', 'm13@test.com', '20240140013', '2024', '081234560013'],
            ['Nia Kurnia Sari', 'm14@test.com', '20240140014', '2024', '081234560014'],
            ['Oka Tri Admaja', 'm15@test.com', '20240140015', '2024', '081234560015'],
            ['Putri Zulaikha Hasanah', 'm16@test.com', '20240140016', '2024', '081234560016'],
            ['Qori Akbar Maulana', 'm17@test.com', '20240140017', '2024', '081234560017'],
            ['Rina Fitriyani', 'm18@test.com', '20240140018', '2024', '081234560018'],
            ['Sandi Firmansyah Putra', 'm19@test.com', '20240140019', '2024', '081234560019'],
            ['Tari Lestari Dewi', 'm20@test.com', '20240140020', '2024', '081234560020'],
        ];

        $mhsUsers = [];
        $mahasiswas = [];
        foreach ($dataMhs as $i => $m) {
            $user = User::create([
                'name' => $m[0],
                'email' => $m[1],
                'role' => 'mahasiswa',
                'nomor_induk' => $m[2],
                'password' => Hash::make('password'),
            ]);
            $mhs = Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $m[2],
                'angkatan' => $m[3],
                'prodi' => 'Teknologi Informasi',
                'no_hp' => $m[4],
            ]);
            $mhsUsers[$i] = $user;
            $mahasiswas[$i] = $mhs;
        }

        // ============================================================
        // 3. PERUSAHAAN (5)
        // ============================================================
        $dataPt = [
            ['PT. Nusantara Teknologi Informasi', 'Jl. Adisucipto No. 120, Sleman', -7.7712, 110.4208, 'IT'],
            ['CV. Solusi Digital Kreatif', 'Jl. Kaliurang KM 8, Sleman', -7.7885, 110.4023, 'Start-up'],
            ['PT. Bank Syariah Digital', 'Jl. Malioboro No. 88, Yogyakarta', -7.7988, 110.3701, 'BUMN'],
            ['CV. Inovasi Media Teknologi', 'Jl. Timoho No. 45, Yogyakarta', -7.8085, 110.3889, 'IT'],
            ['PT. Cloud Data Center Indonesia', 'Jl. Laksda Adisucipto Km 6, Sleman', -7.7704, 110.4387, 'Start-up'],
        ];

        $perusahaans = [];
        foreach ($dataPt as $pt) {
            $perusahaans[] = Perusahaan::create([
                'nama_perusahaan' => $pt[0],
                'alamat' => $pt[1],
                'latitude' => $pt[2],
                'longitude' => $pt[3],
                'kategori_industri' => $pt[4],
            ]);
        }

        // ============================================================
        // 4. MAGANG
        // ============================================================
        // Dosen: 0=Agus Pratomo, 1=Sri Wahyuni, 2=Dimas Ardiyanto
        // Perush: 0=Nusantara TI, 1=Solusi Digital, 2=Bank Syariah,
        //         3=Inovasi Media, 4=Cloud DC Indonesia

        $now = Carbon::now();

        // ── M1–M5: Aktif Magang ──
        $aktifData = [
            [0, 0, -2, 3, 'paid', 'Pengembangan Aplikasi Manajemen Inventaris'],
            [1, 1, -1, 4, 'unpaid', 'Implementasi Sistem Monitoring Jaringan IoT'],
            [2, 2, -3, 2, 'paid', 'Perancangan UI/UX Aplikasi Mobile Banking'],
            [3, 0, -2, 1, 'unpaid', 'Sistem Informasi Manajemen Aset Digital'],
            [4, 1, -1, 5, 'paid', 'Analisis Big Data untuk Rekomendasi Produk'],
        ];

        for ($i = 0; $i < 5; $i++) {
            $d = $aktifData[$i];
            Magang::create([
                'mahasiswa_id' => $mahasiswas[$i]->id,
                'perusahaan_id' => $perusahaans[$d[0]]->id,
                'dosen_id' => $dosen[$d[1]]->id,
                'tanggal_mulai' => $now->copy()->addMonths($d[2])->startOfMonth()->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonths($d[3])->endOfMonth()->toDateString(),
                'status_gaji' => $d[4],
                'tema_magang' => $d[5],
                'status_validasi' => 'diterima',
                'status_skp' => 'belum',
                'status_jadwal_skp' => 'belum',
            ]);
        }

        // ── M6–M8: Selesai SKP ──
        $skpData = [
            [0, 2, -10, -7, 'paid', 'Pengembangan REST API untuk Layanan Publik', 'A'],
            [1, 0, -9, -6, 'unpaid', 'Optimasi Basis Data Perusahaan', 'B'],
            [2, 1, -11, -8, 'paid', 'Aplikasi Multimedia Pembelajaran Interaktif', 'A'],
        ];

        for ($i = 0; $i < 3; $i++) {
            $d = $skpData[$i];
            $idx = $i + 5;
            $jadwal = $now->copy()->addMonths($d[3])->addMonth()->setTime(9, 0, 0);
            Magang::create([
                'mahasiswa_id' => $mahasiswas[$idx]->id,
                'perusahaan_id' => $perusahaans[$d[0]]->id,
                'dosen_id' => $dosen[$d[1]]->id,
                'tanggal_mulai' => $now->copy()->addMonths($d[2])->startOfMonth()->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonths($d[3])->endOfMonth()->toDateString(),
                'status_gaji' => $d[4],
                'tema_magang' => $d[5],
                'status_validasi' => 'diterima',
                'status_skp' => 'sudah',
                'nilai_seminar' => $d[6],
                'status_jadwal_skp' => 'disetujui',
                'jadwal_terpilih' => $jadwal->toDateTimeString(),
                'ruangan_skp' => 'Ruang Seminar Lt. ' . (($i % 3) + 2),
            ]);
        }

        // ── M9–M13: Proses Seminar ──
        // M9 (8): selesai > 30 hari, blm ajukan jadwal
        Magang::create([
            'mahasiswa_id' => $mahasiswas[8]->id,
            'perusahaan_id' => $perusahaans[3]->id,
            'dosen_id' => $dosen[2]->id,
            'tanggal_mulai' => $now->copy()->subMonths(8)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subMonths(2)->endOfMonth()->toDateString(),
            'status_gaji' => 'unpaid',
            'tema_magang' => 'Sistem Informasi Pelayanan Klinik Kesehatan',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'belum',
        ]);

        // M10 (9): selesai < 30 hari, blm ajukan jadwal
        Magang::create([
            'mahasiswa_id' => $mahasiswas[9]->id,
            'perusahaan_id' => $perusahaans[4]->id,
            'dosen_id' => $dosen[0]->id,
            'tanggal_mulai' => $now->copy()->subMonths(5)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subDays(7)->toDateString(),
            'status_gaji' => 'paid',
            'tema_magang' => 'Redesain Website Portal Berita Desa',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'belum',
        ]);

        // M11 (10): jadwal menunggu approval
        Magang::create([
            'mahasiswa_id' => $mahasiswas[10]->id,
            'perusahaan_id' => $perusahaans[0]->id,
            'dosen_id' => $dosen[1]->id,
            'tanggal_mulai' => $now->copy()->subMonths(7)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subMonth()->endOfMonth()->toDateString(),
            'status_gaji' => 'unpaid',
            'tema_magang' => 'Pengembangan Sistem Antrian Digital',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'menunggu',
            'jadwal_opsi_1' => $now->copy()->addDays(5)->setTime(9, 0, 0)->toDateTimeString(),
            'jadwal_opsi_2' => $now->copy()->addDays(6)->setTime(13, 0, 0)->toDateTimeString(),
            'jadwal_opsi_3' => $now->copy()->addDays(8)->setTime(10, 0, 0)->toDateTimeString(),
            'ruangan_skp' => 'Ruang Sidang Lt. 4',
        ]);

        // M12 (11): jadwal disetujui
        Magang::create([
            'mahasiswa_id' => $mahasiswas[11]->id,
            'perusahaan_id' => $perusahaans[1]->id,
            'dosen_id' => $dosen[2]->id,
            'tanggal_mulai' => $now->copy()->subMonths(6)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subMonths(2)->endOfMonth()->toDateString(),
            'status_gaji' => 'paid',
            'tema_magang' => 'Implementasi Modul Payment Gateway',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'disetujui',
            'jadwal_opsi_1' => $now->copy()->subMonth()->addDays(3)->setTime(9, 0, 0)->toDateTimeString(),
            'jadwal_opsi_2' => $now->copy()->subMonth()->addDays(4)->setTime(13, 0, 0)->toDateTimeString(),
            'jadwal_opsi_3' => $now->copy()->subMonth()->addDays(6)->setTime(10, 0, 0)->toDateTimeString(),
            'jadwal_terpilih' => $now->copy()->addDays(14)->setTime(9, 0, 0)->toDateTimeString(),
            'ruangan_skp' => 'Ruang Presentasi Lt. 2',
        ]);

        // M13 (12): jadwal ditolak
        Magang::create([
            'mahasiswa_id' => $mahasiswas[12]->id,
            'perusahaan_id' => $perusahaans[2]->id,
            'dosen_id' => $dosen[0]->id,
            'tanggal_mulai' => $now->copy()->subMonths(7)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subMonths(3)->endOfMonth()->toDateString(),
            'status_gaji' => 'unpaid',
            'tema_magang' => 'Sistem Informasi Geografis Pemetaan Wilayah',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'ditolak',
            'jadwal_opsi_1' => $now->copy()->subMonths(2)->addDays(3)->setTime(9, 0, 0)->toDateTimeString(),
            'jadwal_opsi_2' => $now->copy()->subMonths(2)->addDays(4)->setTime(13, 0, 0)->toDateTimeString(),
            'jadwal_opsi_3' => $now->copy()->subMonths(2)->addDays(6)->setTime(10, 0, 0)->toDateTimeString(),
            'keterangan_tolak_jadwal' => 'Jadwal tidak sesuai dengan ketersediaan dosen pembimbing. Silakan ajukan ulang.',
            'ruangan_skp' => 'Ruang Seminar Lt. 3',
        ]);

        // ── M14–M15: Belum Mulai ──
        $belumMulaiData = [
            [3, 1, 'paid', 'Pengembangan Aplikasi Manajemen Proyek'],
            [4, 2, 'unpaid', 'Sistem IoT untuk Monitoring Lingkungan'],
        ];

        for ($i = 0; $i < 2; $i++) {
            $d = $belumMulaiData[$i];
            $idx = $i + 13;
            Magang::create([
                'mahasiswa_id' => $mahasiswas[$idx]->id,
                'perusahaan_id' => $perusahaans[$d[0]]->id,
                'dosen_id' => $dosen[$d[1]]->id,
                'tanggal_mulai' => $now->copy()->addMonths($i + 1)->startOfMonth()->toDateString(),
                'tanggal_selesai' => $now->copy()->addMonths($i + 4)->endOfMonth()->toDateString(),
                'status_gaji' => $d[2],
                'tema_magang' => $d[3],
                'status_validasi' => 'diterima',
                'status_skp' => 'belum',
                'status_jadwal_skp' => 'belum',
            ]);
        }

        // ── M16: Multi Magang — 1 SKP + 1 Aktif ──
        Magang::create([
            'mahasiswa_id' => $mahasiswas[15]->id,
            'perusahaan_id' => $perusahaans[4]->id,
            'dosen_id' => $dosen[0]->id,
            'tanggal_mulai' => $now->copy()->subMonths(13)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subMonths(10)->endOfMonth()->toDateString(),
            'status_gaji' => 'paid',
            'tema_magang' => 'Analisis Kualitas Jaringan Fiber Optik',
            'status_validasi' => 'diterima',
            'status_skp' => 'sudah',
            'nilai_seminar' => 'A',
            'status_jadwal_skp' => 'disetujui',
            'jadwal_terpilih' => $now->copy()->subMonths(9)->setTime(10, 0, 0)->toDateTimeString(),
            'ruangan_skp' => 'Ruang Seminar Lt. 2',
        ]);
        Magang::create([
            'mahasiswa_id' => $mahasiswas[15]->id,
            'perusahaan_id' => $perusahaans[0]->id,
            'dosen_id' => $dosen[0]->id,
            'tanggal_mulai' => $now->copy()->subMonths(2)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->addMonths(2)->endOfMonth()->toDateString(),
            'status_gaji' => 'unpaid',
            'tema_magang' => 'Pengembangan Fitur Chatbot Layanan Pelanggan',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'belum',
        ]);

        // ── M17: Multi Magang — 1 SKP + 1 Aktif ──
        Magang::create([
            'mahasiswa_id' => $mahasiswas[16]->id,
            'perusahaan_id' => $perusahaans[3]->id,
            'dosen_id' => $dosen[1]->id,
            'tanggal_mulai' => $now->copy()->subMonths(12)->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->subMonths(9)->endOfMonth()->toDateString(),
            'status_gaji' => 'unpaid',
            'tema_magang' => 'Digitalisasi Arsip Pemerintahan Daerah',
            'status_validasi' => 'diterima',
            'status_skp' => 'sudah',
            'nilai_seminar' => 'B',
            'status_jadwal_skp' => 'disetujui',
            'jadwal_terpilih' => $now->copy()->subMonths(8)->setTime(11, 0, 0)->toDateTimeString(),
            'ruangan_skp' => 'Ruang Seminar Lt. 3',
        ]);
        Magang::create([
            'mahasiswa_id' => $mahasiswas[16]->id,
            'perusahaan_id' => $perusahaans[1]->id,
            'dosen_id' => $dosen[1]->id,
            'tanggal_mulai' => $now->copy()->subMonth()->startOfMonth()->toDateString(),
            'tanggal_selesai' => $now->copy()->addMonths(3)->endOfMonth()->toDateString(),
            'status_gaji' => 'paid',
            'tema_magang' => 'Dashboard Analitik Bisnis Real-time',
            'status_validasi' => 'diterima',
            'status_skp' => 'belum',
            'status_jadwal_skp' => 'belum',
        ]);

        // M18–M20: Tanpa Magang — no records

        // ============================================================
        // 5. LOGBOOKS
        // ============================================================
        $buatLogbook = function ($magangId, $tglMulai, $jumlahMinggu, $accMingguTerakhir = null, $komentarAwal = null) {
            $keg = $this->kegiatan;
            $ken = $this->kendala;
            $sol = $this->solusi;
            $total = count($keg);

            for ($w = 1; $w <= $jumlahMinggu; $w++) {
                $tglAwal = Carbon::parse($tglMulai)->addWeeks($w - 1);
                $isi = [];
                for ($d = 0; $d < 5; $d++) {
                    $tgl = $tglAwal->copy()->addDays($d);
                    $idx = ($w * 3 + $d * 7) % $total;
                    $isi[$tgl->toDateString()] = [
                        'kegiatan' => $keg[$idx],
                        'permasalahan' => $ken[($idx + 2) % $total],
                        'solusi' => $sol[($idx + 2) % $total],
                    ];
                }

                $isAcc = $accMingguTerakhir !== null ? $w <= $accMingguTerakhir : ($w < $jumlahMinggu);
                $komentar = null;
                if ($isAcc && $komentarAwal) {
                    $komentar = $w === 1 ? $komentarAwal :
                        ($w === 2 ? 'Pertahankan konsistensi.' :
                            'Progress baik, dokumentasikan kendala.');
                }

                Logbook::create([
                    'magang_id' => $magangId,
                    'minggu_ke' => $w,
                    'tgl_mulai' => $tglAwal->toDateString(),
                    'tgl_selesai' => $tglAwal->copy()->addDays(4)->toDateString(),
                    'isi_logbook' => $isi,
                    'komentar_dosen' => $komentar,
                    'status_acc' => $isAcc,
                ]);
            }
        };

        $allMagang = Magang::where('status_validasi', 'diterima')->where('status_skp', 'belum')->get();
        foreach ($allMagang as $m) {
            $weeks = max(1, Carbon::parse($m->tanggal_mulai)->diffInWeeks(Carbon::now()));
            $buatLogbook($m->id, $m->tanggal_mulai, min($weeks, 5), $weeks >= 3 ? 2 : null, 'Catat setiap detail pekerjaan dengan rapi.');
        }

        // ============================================================
        // 6. REVIEWS
        // ============================================================
        $reviewData = [
            [0, 0, 5, 'Lingkungan kerja profesional dan mendukung perkembangan skill.'],
            [1, 1, 4, 'Tim solid dengan penggunaan teknologi terkini yang relevan.'],
            [2, 5, 5, 'Proyek menantang dan mentor sangat kompeten di bidangnya.'],
            [3, 3, 3, 'Suasana kerja formal dengan birokrasi yang cukup ketat.'],
            [4, 4, 4, 'Infrastruktur IT lengkap untuk belajar dan bereksperimen.'],
            [0, 6, 4, 'Pengalaman berharga dalam pengembangan API dan dokumentasi.'],
            [1, 8, 5, 'Sangat direkomendasikan untuk magang bidang data analitik.'],
            [2, 10, 2, 'Kurang bimbingan teknis dari pembimbing lapangan secara rutin.'],
            [3, 13, 5, 'Kantor kreatif dan memberikan banyak relasi profesional baru.'],
            [4, 15, 4, 'Belajar banyak tentang cloud infrastructure dan DevOps.'],
        ];

        foreach ($reviewData as $rv) {
            Review::create([
                'perusahaan_id' => $perusahaans[$rv[0]]->id,
                'mahasiswa_id' => $mahasiswas[$rv[1]]->id,
                'rating' => $rv[2],
                'komentar' => $rv[3],
            ]);
        }

        // ============================================================
        // 7. PENGUMUMAN
        // ============================================================
        $pengumumanData = [
            [
                'judul' => 'Lowongan Magang — Fullstack Developer (Laravel + React)',
                'deskripsi' => 'Magang fullstack developer. Terlibat dalam pengembangan REST API, database, dan frontend ReactJS.',
                'lokasi' => 'Yogyakarta (WFO)',
                'info_gaji' => 'Rp 1.500.000 - 2.000.000 /bln',
                'info_fasilitas' => 'Sertifikat, BPJS, makan siang, asuransi',
                'syarat_tambahan' => 'Mahasiswa aktif min. semester 4, paham OOP PHP & ReactJS dasar',
                'target_angkatan' => '2022, 2023, 2024',
                'link_pendaftaran' => '#',
            ],
            [
                'judul' => 'Lowongan Magang — Data Analyst (Python)',
                'deskripsi' => 'Magang data analyst. Terlibat dalam pengolahan data, visualisasi, dan pembuatan laporan analitik.',
                'lokasi' => 'Sleman (Hybrid)',
                'info_gaji' => 'Honorarium Rp 800.000 - 1.200.000 /bln',
                'info_fasilitas' => 'Sertifikat, mentoring, akses platform belajar data',
                'syarat_tambahan' => 'Menguasai Python pandas, SQL dasar, dan Excel',
                'target_angkatan' => '2022, 2023',
                'link_pendaftaran' => '#',
            ],
            [
                'judul' => 'Lowongan Magang — Network Engineer',
                'deskripsi' => 'Magang network engineer. Terlibat dalam konfigurasi router, monitoring jaringan, dan troubleshooting ISP.',
                'lokasi' => 'Ngaglik, Sleman',
                'info_gaji' => 'Honorarium',
                'info_fasilitas' => 'Sertifikat, bimbingan mentor, sertifikasi jaringan',
                'syarat_tambahan' => 'Paham dasar TCP/IP, routing, dan Cisco CLI',
                'target_angkatan' => '2023, 2024',
                'link_pendaftaran' => '#',
            ],
        ];

        foreach ($pengumumanData as $pgn) {
            Pengumuman::create($pgn);
        }
    }
}
