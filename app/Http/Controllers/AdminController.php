<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magang;
use App\Models\Mahasiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiwayatMagangExport;
use App\Exports\DataSkpExport;        
use App\Exports\RiwayatSelesaiExport;

class AdminController extends Controller
{
    // -------------------------------------------------------------------------
    // 0. DASHBOARD ADMIN
    // -------------------------------------------------------------------------
    public function index()
    {
        $total_mhs = Mahasiswa::count();

        // Karena validasi ditiadakan, pendaftar 'pending' akan selalu 0
        $pending = 0;

        $aktif = Magang::where('status_validasi', 'diterima')->where('status_skp', 'belum')->count();
        $selesai = Magang::where('status_skp', 'sudah')->count();

        return view('admin.dashboard', compact('total_mhs', 'pending', 'aktif', 'selesai'));
    }


    // Menampilkan Detail Riwayat Magang (Universal)
    public function showValidasi($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])->findOrFail($id);
        
        // Mengarah ke view detail yang baru
        return view('riwayat_magang.show', compact('magang'));
    }

    // -------------------------------------------------------------------------
    // 2. MONITORING LOGBOOK
    // -------------------------------------------------------------------------
    public function monitoring()
    {
        $sedang_magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->withCount('logbooks')
            ->where('status_validasi', 'diterima')
            ->whereDate('tanggal_selesai', '>=', now())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('monitoring.index', compact('sedang_magang'));
    }

    public function monitoringDetail($id)
    {
        $magang = Magang::with([
            'mahasiswa.user',
            'perusahaan',
            'logbooks' => function ($query) {
                $query->orderBy('tgl_mulai', 'desc');
            }
        ])->findOrFail($id);

        return view('admin.monitoring.detail', compact('magang'));
    }

    public function showSkp($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])->findOrFail($id);
        return view('admin.skp_detail', compact('magang'));
    }

    public function updateSkp(Request $request, $id)
    {
        $magang = Magang::findOrFail($id);

        // --- VERIFIKASI (APPROVE) ---
        if ($request->action == 'verifikasi') {
            if (!$magang->nilai_seminar) {
                return back()->with('error', 'Gagal verifikasi. Mahasiswa belum menginput nilai.');
            }

            $magang->update([
                'status_skp' => 'sudah',
                'keterangan_revisi' => null
            ]);

            return redirect()->route('admin.skp')->with('success', 'Nilai diverifikasi. SKP Diterbitkan.');
        }

        // --- TOLAK (REVISI) ---
        elseif ($request->action == 'tolak') {
            $request->validate([
                'keterangan_revisi' => 'required|string|min:5'
            ]);

            $magang->update([
                'status_skp' => 'belum',
                'keterangan_revisi' => $request->keterangan_revisi
            ]);

            return back()->with('warning', 'Catatan revisi telah dikirim ke mahasiswa.');
        }

        // --- BATAL VERIFIKASI ---
        elseif ($request->action == 'batal') {
            $magang->update(['status_skp' => 'belum']);
            return back()->with('success', 'Status SKP dibatalkan.');
        }

        return back();
    }

    // -------------------------------------------------------------------------
    // 4. EXPORT DATA (EXCEL & PDF)
    // -------------------------------------------------------------------------
    
    // Export Data Kolektif Excel (Berdasarkan Filter)
    public function exportExcel(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $namaBulan = $bulan ? $bulan : 'semua';
        $namaTahun = $tahun ? $tahun : 'semua';

        // Mengirimkan parameter filter ke dalam class Export
        return Excel::download(new RiwayatMagangExport($bulan, $tahun), "riwayat-pendaftaran-{$namaBulan}-{$namaTahun}.xlsx");
    }

    // Export Data Kolektif PDF (Berdasarkan Filter)
    public function exportPdf(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('status_validasi', 'diterima');

        if (!empty($bulan)) {
            $query->whereMonth('created_at', $bulan);
        }
        if (!empty($tahun)) {
            $query->whereYear('created_at', $tahun);
        }

        $pendaftar = $query->orderBy('created_at', 'desc')->get();

        // Load view PDF
        $pdf = Pdf::loadView('admin.export_riwayat_pdf', compact('pendaftar'));
        
        $namaBulan = $bulan ? $bulan : 'semua';
        $namaTahun = $tahun ? $tahun : 'semua';

        return $pdf->download("riwayat-pendaftaran-{$namaBulan}-{$namaTahun}.pdf");
    }

    // Export Detail (Satu Mahasiswa) - PDF
    public function exportDetailPdf($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.export_detail_pdf', compact('magang'));
        return $pdf->download('detail-magang-' . $magang->mahasiswa->nim . '.pdf');
    }

    // Export Detail (Satu Mahasiswa) - Excel
    public function exportDetailExcel($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])->findOrFail($id);
        
        // Asumsi Anda akan membuat class SingleRiwayatExport khusus untuk ini
        // php artisan make:export SingleRiwayatExport
        return Excel::download(new \App\Exports\SingleRiwayatExport($magang), 'Detail-Magang-' . $magang->mahasiswa->nim . '.xlsx');
    }

    // -------------------------------------------------------------------------
    // 3. MANAJEMEN SKP & SEMINAR (Updated dengan Filter)
    // -------------------------------------------------------------------------
    public function skp(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));

        $query = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('status_validasi', 'diterima');

        if (!empty($bulan)) {
            $query->whereMonth('updated_at', $bulan);
        }
        if (!empty($tahun)) {
            $query->whereYear('updated_at', $tahun);
        }

        $magang = $query->orderBy('status_skp', 'asc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.skp', compact('magang'));
    }

    // --- LOGIKA EXPORT UNTUK HALAMAN DATA SKP ---
    public function exportSkpExcel(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $namaBulan = $bulan ?: 'semua';
        $namaTahun = $tahun ?: 'semua';

        // Menggunakan class export khusus Data SKP
        return Excel::download(
            new DataSkpExport($bulan, $tahun), 
            "Laporan-Dokumen-SKP-{$namaBulan}-{$namaTahun}.xlsx"
        );
    }

    public function exportSkpPdf(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('status_validasi', 'diterima');

        if (!empty($bulan)) { $query->whereMonth('updated_at', $bulan); }
        if (!empty($tahun)) { $query->whereYear('updated_at', $tahun); }

        $skpData = $query->orderBy('updated_at', 'desc')->get();
        
        // Memanggil view khusus cetak PDF SKP
        $pdf = Pdf::loadView('admin.export_skp_pdf', compact('skpData', 'bulan', 'tahun'))
                  ->setPaper('A4', 'landscape');
        
        $namaBulan = $bulan ?: 'semua';
        $namaTahun = $tahun ?: 'semua';
        return $pdf->download("Laporan-Dokumen-SKP-{$namaBulan}-{$namaTahun}.pdf");
    }


    // --- LOGIKA EXPORT UNTUK HALAMAN RIWAYAT SELESAI ---
    public function exportSelesaiExcel(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $namaBulan = $bulan ?: 'semua';
        $namaTahun = $tahun ?: 'semua';

        // Menggunakan class export khusus Riwayat Selesai
        return Excel::download(
            new RiwayatSelesaiExport($bulan, $tahun), 
            "Riwayat-Magang-Selesai-{$namaBulan}-{$namaTahun}.xlsx"
        );
    }

    public function exportSelesaiPdf(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->where('status_skp', 'sudah');
            
        if (!empty($bulan)) { $query->whereMonth('tanggal_selesai', $bulan); }
        if (!empty($tahun)) { $query->whereYear('tanggal_selesai', $tahun); }

        $riwayat = $query->orderBy('updated_at', 'desc')->get();
        
        // Memanggil view khusus cetak PDF Riwayat Selesai
        $pdf = Pdf::loadView('admin.riwayat_selesai.export_pdf', compact('riwayat', 'bulan', 'tahun'))
                  ->setPaper('A4', 'landscape');
        
        $namaBulan = $bulan ?: 'semua';
        $namaTahun = $tahun ?: 'semua';
        return $pdf->download("Riwayat-Magang-Selesai-{$namaBulan}-{$namaTahun}.pdf");
    }

    // =========================================================================
    // 1. RIWAYAT MAGANG (Gabungan Pendaftaran & Selesai untuk Admin & Kaprodi)
    // =========================================================================
    public function riwayatMagang(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $status = $request->input('status');
        $search = $request->input('search'); // Tangkap input pencarian
        $hariIni = \Carbon\Carbon::now();

        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen']);
           

        // Filter Pencarian (Search) untuk Admin/Kaprodi
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                // Cari berdasarkan nama mahasiswa
                $q->whereHas('mahasiswa.user', function($qMhs) use ($search) {
                    $qMhs->where('name', 'like', "%{$search}%");
                })
                // Cari berdasarkan nama dosen
                ->orWhereHas('dosen', function($qDosen) use ($search) {
                    $qDosen->where('name', 'like', "%{$search}%");
                })
                // Cari berdasarkan nama perusahaan
                ->orWhereHas('perusahaan', function($qPT) use ($search) {
                    $qPT->where('nama_perusahaan', 'like', "%{$search}%");
                });
            });
        }

        // Filter Bulan
        if (!empty($bulan)) {
            $query->whereMonth('tanggal_mulai', $bulan);
        }
        
        // Filter Tahun
        if (!empty($tahun)) {
            $query->whereYear('tanggal_mulai', $tahun);
        }

        // Filter berdasarkan Status Magang
        if (!empty($status)) {
            if ($status == 'selesai') {
                $query->where('status_skp', 'sudah');
            } elseif ($status == 'seminar') {
                $query->where('status_skp', 'belum')
                      ->where('tanggal_selesai', '<', $hariIni);
            } elseif ($status == 'aktif') {
                $query->where('status_skp', 'belum')
                      ->where('tanggal_selesai', '>=', $hariIni);
            }
        }

        // PASTIKAN BAGIAN INI: Mengurutkan berdasarkan tanggal daftar paling baru (teratas)
        $riwayat = $query->latest('created_at')->paginate(10);

        return view('riwayat_magang.index', compact('riwayat'));
    }

    // PASTIKAN juga kamu update nama method Export di Controller (sesuaikan dengan route baru)
    // Export Excel
    public function exportRiwayatExcel(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $status = $request->input('status');
        
        $namaBulan = $bulan ?: 'semua';
        $namaTahun = $tahun ?: 'semua';
        $namaStatus = $status ?: 'semua-status';

        return Excel::download(
            new RiwayatMagangExport($bulan, $tahun, $status), 
            "Data-Riwayat-Magang-{$namaStatus}-{$namaBulan}-{$namaTahun}.xlsx"
        );
    }
    // Export PDF
    // Export PDF
    public function exportRiwayatPdf(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $status = $request->input('status');
        $hariIni = \Carbon\Carbon::now();

        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen']);
            

        if (!empty($bulan)) { $query->whereMonth('tanggal_mulai', $bulan); }
        if (!empty($tahun)) { $query->whereYear('tanggal_mulai', $tahun); }
        
        if (!empty($status)) {
            if ($status == 'selesai') {
                $query->where('status_skp', 'sudah');
            } elseif ($status == 'seminar') {
                $query->where('status_skp', 'belum')->where('tanggal_selesai', '<', $hariIni);
            } elseif ($status == 'aktif') {
                $query->where('status_skp', 'belum')->where('tanggal_selesai', '>=', $hariIni);
            }
        }

        // PASTIKAN BAGIAN INI JUGA: Mengurutkan PDF dari yang paling baru
        $riwayat = $query->latest('created_at')->get();
        
        $pdf = Pdf::loadView('riwayat_magang.export_pdf', compact('riwayat', 'bulan', 'tahun', 'status'))
                  ->setPaper('A4', 'landscape');
        
        $namaBulan = $bulan ?: 'semua';
        $namaTahun = $tahun ?: 'semua';
        $namaStatus = $status ?: 'semua-status';
        
        return $pdf->download("Data-Riwayat-Magang-{$namaStatus}-{$namaBulan}-{$namaTahun}.pdf");
    }
}