<?php

namespace App\Http\Controllers;

use App\Exports\DataSkpExport;
use App\Exports\RiwayatMagangExport;
use App\Models\Magang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function showValidasi($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])->findOrFail($id);

        return view('riwayat_magang.show', compact('magang'));
    }

    // -------------------------------------------------------------------------
    // MONITORING LOGBOOK
    // -------------------------------------------------------------------------
    public function monitoring()
    {
        $sedang_magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->withCount('logbooks')
            ->diterima()
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
            'logbooks' => fn ($query) => $query->orderBy('tgl_mulai', 'desc'),
        ])->findOrFail($id);

        return view('monitoring.detail', compact('magang'));
    }

    // -------------------------------------------------------------------------
    // MANAJEMEN SKP & SEMINAR
    // -------------------------------------------------------------------------
    public function skp(Request $request)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->diterima()
            ->when($request->filled('bulan'), fn ($query) => $query->whereMonth('updated_at', $request->bulan))
            ->when($request->filled('tahun'), fn ($query) => $query->whereYear('updated_at', $request->tahun))
            ->orderBy('status_skp', 'asc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin.skp', compact('magang'));
    }

    public function showSkp($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])->findOrFail($id);

        return view('admin.skp_detail', compact('magang'));
    }

    public function updateSkp(Request $request, $id)
    {
        $magang = Magang::findOrFail($id);

        if ($request->action === 'verifikasi') {
            if (! $magang->nilai_seminar) {
                return back()->with('error', 'Gagal verifikasi. Mahasiswa belum menginput nilai.');
            }

            $magang->update([
                'status_skp' => 'sudah',
                'keterangan_revisi' => null,
            ]);

            return redirect()->route('admin.skp')->with('success', 'Nilai diverifikasi. SKP Diterbitkan.');
        }

        if ($request->action === 'tolak') {
            $request->validate([
                'keterangan_revisi' => 'required|string|min:5',
            ]);

            $magang->update([
                'status_skp' => 'belum',
                'keterangan_revisi' => $request->keterangan_revisi,
            ]);

            return back()->with('warning', 'Catatan revisi telah dikirim ke mahasiswa.');
        }

        if ($request->action === 'batal') {
            $magang->update(['status_skp' => 'belum']);

            return back()->with('success', 'Status SKP dibatalkan.');
        }

        return back();
    }

    public function exportSkpExcel(Request $request)
    {
        $namaBulan = $request->bulan ?: 'semua';
        $namaTahun = $request->tahun ?: 'semua';

        return Excel::download(
            new DataSkpExport($request->bulan, $request->tahun),
            "Laporan-Dokumen-SKP-{$namaBulan}-{$namaTahun}.xlsx"
        );
    }

    public function exportSkpPdf(Request $request)
    {
        $skpData = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->diterima()
            ->when($request->filled('bulan'), fn ($query) => $query->whereMonth('updated_at', $request->bulan))
            ->when($request->filled('tahun'), fn ($query) => $query->whereYear('updated_at', $request->tahun))
            ->orderBy('updated_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.export_skp_pdf', compact('skpData', 'bulan', 'tahun'))
            ->setPaper('A4', 'landscape');

        $namaBulan = $request->bulan ?: 'semua';
        $namaTahun = $request->tahun ?: 'semua';

        return $pdf->download("Laporan-Dokumen-SKP-{$namaBulan}-{$namaTahun}.pdf");
    }

    // -------------------------------------------------------------------------
    // RIWAYAT MAGANG (Admin & Kaprodi)
    // -------------------------------------------------------------------------
    public function riwayatMagang(Request $request)
    {
        $riwayat = $this->riwayatQuery($request)->paginate(10);

        return view('riwayat_magang.index', compact('riwayat'));
    }

    public function exportRiwayatExcel(Request $request)
    {
        $namaBulan = $request->bulan ?: 'semua';
        $namaTahun = $request->tahun ?: 'semua';
        $namaStatus = $request->status ?: 'semua-status';

        return Excel::download(
            new RiwayatMagangExport($request->bulan, $request->tahun, $request->status),
            "Data-Riwayat-Magang-{$namaStatus}-{$namaBulan}-{$namaTahun}.xlsx"
        );
    }

    public function exportRiwayatPdf(Request $request)
    {
        $riwayat = $this->riwayatQuery($request)->get();

        $pdf = Pdf::loadView('riwayat_magang.export_pdf', [
            'riwayat' => $riwayat,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'status' => $request->status,
        ])->setPaper('A4', 'landscape');

        $namaBulan = $request->bulan ?: 'semua';
        $namaTahun = $request->tahun ?: 'semua';
        $namaStatus = $request->status ?: 'semua-status';

        return $pdf->download("Data-Riwayat-Magang-{$namaStatus}-{$namaBulan}-{$namaTahun}.pdf");
    }

    private function riwayatQuery(Request $request): Builder
    {
        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa.user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('dosen', fn ($dosen) => $dosen->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('perusahaan', fn ($perusahaan) => $perusahaan->where('nama_perusahaan', 'like', "%{$search}%"));
            });
        }

        $query
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('tanggal_mulai', $request->bulan))
            ->when($request->filled('tahun'), fn ($q) => $q->whereYear('tanggal_mulai', $request->tahun))
            ->when($request->filled('status'), function ($q) use ($request) {
                match ($request->status) {
                    'selesai' => $q->skpSudah(),
                    'seminar' => $q->skpBelum()->where('tanggal_selesai', '<', now()),
                    'aktif' => $q->skpBelum()->where('tanggal_selesai', '>=', now()),
                    default => null,
                };
            });

        return $query->latest('created_at');
    }
}
