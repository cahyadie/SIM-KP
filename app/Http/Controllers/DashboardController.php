<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magang;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    /**
     * Redirector ke dashboard masing-masing role
     */
    public function index()
    {
        $role = Auth::user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kaprodi' => redirect()->route('kaprodi.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default => abort(403, 'Unauthorized action.'),
        };
    }

    /**
     * Dashboard khusus Admin
     */
    public function admin()
    {
        $data = $this->getSharedData();

        // Hapus filter 'diterima' dan limit(10) untuk menampilkan semua data
        $data['pendaftar_baru'] = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->orderBy('created_at', 'desc')
            ->get(); // Mengambil semua data

        $data['list_skp'] = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->orderBy('status_skp', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get(); // Mengambil semua data

        return view('dashboard.admin', $data);
    }

    /**
     * Dashboard khusus Kaprodi
     */
    public function kaprodi()
    {
        $role = Auth::user()->role;

        // 1. Kinerja Mahasiswa (Overview)
        $total_mahasiswa = Mahasiswa::count();
        $sedang_magang = Magang::where('status_validasi', 'diterima')->where('status_skp', 'belum')->whereDate('tanggal_mulai', '<=', now())->whereDate('tanggal_selesai', '>=', now())->count();
        $belum_skp = Magang::where('status_validasi', 'diterima')->whereDate('tanggal_selesai', '<', now())->where('status_skp', 'belum')->count();
        $sudah_skp = Magang::where('status_validasi', 'diterima')->where('status_skp', 'sudah')->count();

        // 2. Statistik Pengajuan Magang
        $total_pengajuan = Magang::count();
        $stat_diterima = Magang::where('status_validasi', 'diterima')->count();
        $stat_pending = Magang::where('status_validasi', 'pending')->count();
        $stat_ditolak = Magang::where('status_validasi', 'ditolak')->count();

        // 3. Status Gaji
        $paid = Magang::where('status_validasi', 'diterima')->where('status_gaji', 'paid')->count();
        $unpaid = Magang::where('status_validasi', 'diterima')->where('status_gaji', 'unpaid')->count();

        // 4. Perusahaan Terfavorit
        $top_perusahaan = DB::table('magangs')
            ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
            ->select('perusahaans.nama_perusahaan', 'perusahaans.kategori_industri', DB::raw('count(magangs.id) as total'))
            ->where('magangs.status_validasi', 'diterima')
            ->groupBy('perusahaans.id', 'perusahaans.nama_perusahaan', 'perusahaans.kategori_industri')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // 5. Sebaran Bidang Industri
        $total_valid = Magang::where('status_validasi', 'diterima')->count();
        $sebaran_bidang = DB::table('magangs')
            ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
            ->select('perusahaans.kategori_industri', DB::raw('count(magangs.id) as total'))
            ->where('magangs.status_validasi', 'diterima')
            ->groupBy('perusahaans.kategori_industri')
            ->get()
            ->map(function ($item) use ($total_valid) {
                $item->persentase = $total_valid > 0 ? round(($item->total / $total_valid) * 100, 1) : 0;
                return $item;
            });

        // 6. Data Peta Lokasi Magang
        $lokasi_magang = Magang::with(['perusahaan', 'mahasiswa.user'])
            ->where('status_validasi', 'diterima')
            ->get()
            ->groupBy('perusahaan_id')
            ->map(function ($magangs) {
                $first = $magangs->first();
                $perusahaan = $first->perusahaan;
                return [
                    'nama_mhs' => $magangs->pluck('mahasiswa.user.name')->toArray(),
                    'perusahaan' => $perusahaan->nama_perusahaan,
                    'lat' => $perusahaan->latitude,
                    'lng' => $perusahaan->longitude,
                    'status_skp' => $magangs->every(fn($m) => $m->status_skp == 'sudah') ? 'sudah' : 'belum',
                    'is_selesai' => $magangs->every(fn($m) => \Carbon\Carbon::parse($m->tanggal_selesai)->isPast()),
                ];
            })->values();

        // ==========================================
        // 7. LIST DATA MAHASISWA UNTUK TABEL BAWAH
        // ==========================================

        // A. Sedang Magang Aktif
        $magang_aktif = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('status_validasi', 'diterima')
            ->where('status_skp', 'belum')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_selesai', 'asc')
            ->get();

        // B. Magang Selesai TAPI Belum SKP
        $magang_belum_skp = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('status_validasi', 'diterima')
            ->where('status_skp', 'belum')
            ->whereDate('tanggal_selesai', '<', now())
            ->orderBy('tanggal_selesai', 'desc')
            ->get();

        // C. Magang Selesai DAN Lulus SKP
        $magang_lulus_skp = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('status_validasi', 'diterima')
            ->where('status_skp', 'sudah')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.kaprodi', compact(
            'role',
            'total_mahasiswa',
            'sedang_magang',
            'belum_skp',
            'sudah_skp',
            'total_pengajuan',
            'stat_diterima',
            'stat_pending',
            'stat_ditolak',
            'paid',
            'unpaid',
            'top_perusahaan',
            'sebaran_bidang',
            'lokasi_magang',
            'magang_aktif',
            'magang_belum_skp',
            'magang_lulus_skp'
        ));
    }

    /**
     * Menampilkan halaman grafik detail saat card di dashboard diklik
     */
    public function statistikDetail(\Illuminate\Http\Request $request, $kategori)
    {
        $role = \Illuminate\Support\Facades\Auth::user()->role;
        if ($role !== 'kaprodi' && $role !== 'admin') {
            abort(403);
        }

        $title = 'Detail Statistik';
        $chartLabels = [];
        $chartValues = [];
        $chartType = 'bar';

        // --- TANGKAP FILTER TAHUN DARI REQUEST ---
        // Jika tidak ada filter, gunakan tahun saat ini
        $filterYear = $request->input('tahun', date('Y'));

        $total_data = 0;
        $list_mahasiswa = collect();

        switch ($kategori) {
            case 'pengajuan':
                $title = 'Aktif Magang (' . ($request->has('tahun') ? $filterYear : 'Semua Tahun') . ')';
                $chartType = 'line';

                $queryData = \Illuminate\Support\Facades\DB::table('magangs')
                    ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total');

                // Jika tahun dipilih, aplikasikan filter
                if ($filterYear) {
                    $queryData->whereYear('created_at', $filterYear);
                }

                $data = $queryData->groupBy('bulan')->pluck('total', 'bulan')->toArray();

                for ($i = 1; $i <= 12; $i++) {
                    $chartLabels[] = date("M", mktime(0, 0, 0, $i, 10));
                    $chartValues[] = $data[$i] ?? 0;
                }
                $total_data = array_sum($chartValues);
                break;

            case 'aktif':
                $title = 'Mahasiswa Aktif Magang (' . ($request->has('tahun') ? $filterYear : 'Semua Tahun') . ')';
                $chartType = 'bar';

                $queryData = \Illuminate\Support\Facades\DB::table('magangs')
                    ->selectRaw('MONTH(tanggal_mulai) as bulan, COUNT(*) as total')
                    ->where('status_validasi', 'diterima');

                if ($filterYear) {
                    $queryData->whereYear('tanggal_mulai', $filterYear);
                }

                $data = $queryData->groupBy('bulan')->pluck('total', 'bulan')->toArray();

                for ($i = 1; $i <= 12; $i++) {
                    $chartLabels[] = date("M", mktime(0, 0, 0, $i, 10));
                    $chartValues[] = $data[$i] ?? 0;
                }
                $total_data = array_sum($chartValues);

                $queryMhs = \App\Models\Magang::with(['mahasiswa.user', 'perusahaan'])
                    ->where('status_validasi', 'diterima')
                    ->whereDate('tanggal_selesai', '>=', now())
                    ->orderBy('tanggal_selesai', 'asc');

                if ($filterYear) {
                    $queryMhs->whereYear('tanggal_mulai', $filterYear);
                }

                $list_mahasiswa = $queryMhs->paginate(10);
                break;

            case 'lulus':
            case 'belum-skp':
                $title = $kategori == 'lulus' ? 'Mahasiswa Lulus (Sudah SKP)' : 'Mahasiswa Menunggu SKP';
                if ($request->has('tahun'))
                    $title .= " ($filterYear)";

                $chartType = 'bar';
                $status_skp = $kategori == 'lulus' ? 'sudah' : 'belum';

                $queryData = \Illuminate\Support\Facades\DB::table('magangs')
                    ->selectRaw('MONTH(tanggal_selesai) as bulan, COUNT(*) as total')
                    ->where('status_validasi', 'diterima')
                    ->where('status_skp', $status_skp);

                if ($filterYear) {
                    $queryData->whereYear('tanggal_selesai', $filterYear);
                }

                $data = $queryData->groupBy('bulan')->pluck('total', 'bulan')->toArray();

                for ($i = 1; $i <= 12; $i++) {
                    $chartLabels[] = date("M", mktime(0, 0, 0, $i, 10));
                    $chartValues[] = $data[$i] ?? 0;
                }
                $total_data = array_sum($chartValues);

                $queryMhs = \App\Models\Magang::with(['mahasiswa.user', 'perusahaan'])
                    ->where('status_validasi', 'diterima')
                    ->where('status_skp', $status_skp);

                if ($filterYear) {
                    $queryMhs->whereYear('tanggal_selesai', $filterYear);
                }

                if ($kategori == 'belum-skp') {
                    $queryMhs->whereDate('tanggal_selesai', '<', now());
                }

                $list_mahasiswa = $queryMhs->orderBy('tanggal_selesai', 'desc')->paginate(10);
                break;

            case 'gaji':
                $title = 'Status Pendapatan (Paid vs Unpaid)';
                if ($request->has('tahun'))
                    $title .= " ($filterYear)";

                $chartType = 'doughnut';

                $queryPaid = \Illuminate\Support\Facades\DB::table('magangs')->where('status_validasi', 'diterima')->where('status_gaji', 'paid');
                $queryUnpaid = \Illuminate\Support\Facades\DB::table('magangs')->where('status_validasi', 'diterima')->where('status_gaji', 'unpaid');

                $queryMhs = \App\Models\Magang::with(['mahasiswa.user', 'perusahaan'])->where('status_validasi', 'diterima');

                if ($filterYear) {
                    $queryPaid->whereYear('tanggal_mulai', $filterYear);
                    $queryUnpaid->whereYear('tanggal_mulai', $filterYear);
                    $queryMhs->whereYear('tanggal_mulai', $filterYear);
                }

                $paid = $queryPaid->count();
                $unpaid = $queryUnpaid->count();

                $chartLabels = ['Paid (Dibayar)', 'Unpaid (Tidak Dibayar)'];
                $chartValues = [$paid, $unpaid];
                $total_data = $paid + $unpaid;

                $list_mahasiswa = $queryMhs->orderBy('status_gaji', 'asc')->paginate(10);
                break;

            default:
                abort(404);
        }

        return view('dashboard.statistik-detail', compact('title', 'chartLabels', 'chartValues', 'chartType', 'kategori', 'total_data', 'list_mahasiswa'));
    }

    /**
     * Mengambil data statistik dan peta yang digunakan bersama oleh Admin dan Kaprodi
     */
    private function getSharedData()
    {
        $role = Auth::user()->role;

        // 1. Total mahasiswa dari semua data
        $total_mahasiswa = Mahasiswa::count();

        // 2. Magang yang sedang berlangsung (tanggal_selesai >= hari ini)
        $sedang_magang = Magang::whereDate('tanggal_selesai', '>=', now())
            ->count();

        // 3. Magang yang sudah selesai TAPI status SKP masih 'belum'
        $belum_skp = Magang::whereDate('tanggal_selesai', '<', now())
            ->where('status_skp', 'belum')
            ->count();

        // 4. Status Gaji (Menghitung total keseluruhan tanpa filter status_validasi)
        $paid = Magang::where('status_gaji', 'paid')->count();
        $unpaid = Magang::where('status_gaji', 'unpaid')->count();

        // 5. Data Peta (Mengambil semua data magang untuk pemetaan)
        $lokasi_magang = Magang::with(['perusahaan', 'mahasiswa.user'])
            ->get()
            ->groupBy('perusahaan_id')
            ->map(function ($magangs) {
                $first = $magangs->first();
                $perusahaan = $first->perusahaan;
                return [
                    'nama_mhs' => $magangs->pluck('mahasiswa.user.name')->toArray(),
                    'perusahaan' => $perusahaan->nama_perusahaan,
                    'lat' => $perusahaan->latitude,
                    'lng' => $perusahaan->longitude,
                    'status' => $magangs->pluck('status_gaji')->first(),
                    'status_skp' => $magangs->every(fn($m) => $m->status_skp == 'sudah') ? 'sudah' : 'belum',
                    'is_selesai' => $magangs->every(fn($m) => \Carbon\Carbon::parse($m->tanggal_selesai)->isPast()),
                ];
            })->values();

        // 6. Top 5 Perusahaan (Berdasarkan jumlah magang terbanyak)
        $top_perusahaan = DB::table('magangs')
            ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
            ->select('perusahaans.nama_perusahaan', DB::raw('count(magangs.id) as total'))
            ->groupBy('perusahaans.nama_perusahaan')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 7. Sebaran Bidang Industri (Dihitung berdasarkan total keseluruhan data)
        $total_keseluruhan = Magang::count();
        $sebaran_bidang = DB::table('magangs')
            ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
            ->select('perusahaans.kategori_industri', DB::raw('count(magangs.id) as total'))
            ->groupBy('perusahaans.kategori_industri')
            ->get()
            ->map(function ($item) use ($total_keseluruhan) {
                $item->persentase = $total_keseluruhan > 0 ? round(($item->total / $total_keseluruhan) * 100, 1) : 0;
                return $item;
            });

        return compact(
            'role',
            'total_mahasiswa',
            'sedang_magang',
            'belum_skp',
            'paid',
            'unpaid',
            'lokasi_magang',
            'top_perusahaan',
            'sebaran_bidang'
        );
    }

    public function pantauanSkp()
    {
        // Batas waktu 30 hari ke belakang dari hari ini
        $batasWaktu = \Carbon\Carbon::now()->subDays(30);

        // Menggunakan join agar bisa melakukan orderBy berdasarkan kolom di tabel magangs
        $mahasiswaOverdue = \App\Models\Mahasiswa::select('mahasiswas.*')
            ->join('magangs', 'mahasiswas.id', '=', 'magangs.mahasiswa_id')
            ->where('magangs.status_validasi', 'diterima')
            ->where('magangs.status_skp', 'belum')
            ->where('magangs.tanggal_selesai', '<', $batasWaktu)
            // Mengurutkan tanggal_selesai Descending (paling baru/mendekati 30 hari di atas, terlama di bawah)
            ->orderBy('magangs.tanggal_selesai', 'desc')
            ->with(['user', 'magangs.perusahaan', 'magangs.dosen'])
            ->paginate(10);

        return view('monitoring.pantauan-skp', compact('mahasiswaOverdue'));
    }

    public function exportPantauanPdf(Request $request)
    {
        // Batas waktu 30 hari ke belakang dari hari ini
        $batasWaktu = \Carbon\Carbon::now()->subDays(30);

        // Query INI DISAMAKAN PERSIS dengan query di pantauanSkp()
        $mahasiswaOverdue = \App\Models\Mahasiswa::select('mahasiswas.*')
            ->join('magangs', 'mahasiswas.id', '=', 'magangs.mahasiswa_id')
            ->where('magangs.status_validasi', 'diterima')
            ->where('magangs.status_skp', 'belum')
            ->where('magangs.tanggal_selesai', '<', $batasWaktu)
            ->orderBy('magangs.tanggal_selesai', 'desc')
            ->with(['user', 'magangs.perusahaan', 'magangs.dosen'])
            ->get(); // Gunakan get() agar semua data terambil

        // Load view PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('monitoring.export_pdf', compact('mahasiswaOverdue'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Laporan-Pantauan-SKP-Terlewat-' . \Carbon\Carbon::now()->format('Y-m-d') . '.pdf');
    }
}