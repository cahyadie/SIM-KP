<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Services\NotifikasiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kaprodi' => redirect()->route('kaprodi.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default => abort(403, 'Unauthorized action.'),
        };
    }

    public function admin()
    {
        $data = $this->getSharedData();

        $data['pendaftar_baru'] = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data['list_skp'] = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->orderBy('status_skp', 'asc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.admin', $data);
    }

    public function kaprodi()
    {
        NotifikasiService::kirimSelesaiMagang();

        $statusSkp = Magang::selectRaw('status_skp, COUNT(*) as total')
            ->diterima()
            ->groupBy('status_skp')
            ->pluck('total', 'status_skp');

        $statusGaji = Magang::selectRaw('status_gaji, COUNT(*) as total')
            ->diterima()
            ->groupBy('status_gaji')
            ->pluck('total', 'status_gaji');

        $totalValid = (int) $statusSkp->sum();

        $top_perusahaan = DB::table('magangs')
            ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
            ->select('perusahaans.nama_perusahaan', 'perusahaans.kategori_industri', DB::raw('count(magangs.id) as total'))
            ->where('magangs.status_validasi', 'diterima')
            ->groupBy('perusahaans.id', 'perusahaans.nama_perusahaan', 'perusahaans.kategori_industri')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $sebaran_bidang = DB::table('magangs')
            ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
            ->select('perusahaans.kategori_industri', DB::raw('count(magangs.id) as total'))
            ->where('magangs.status_validasi', 'diterima')
            ->groupBy('perusahaans.kategori_industri')
            ->get()
            ->map(fn ($item) => (object) [
                'kategori_industri' => $item->kategori_industri,
                'total' => $item->total,
                'persentase' => $totalValid > 0 ? round(($item->total / $totalValid) * 100, 1) : 0,
            ]);

        return view('dashboard.kaprodi', [
            'sedang_magang' => Magang::sedangBerlangsung()->count(),
            'belum_skp' => Magang::menungguSkp()->count(),
            'sudah_skp' => (int) ($statusSkp['sudah'] ?? 0),
            'total_pengajuan' => (int) $statusSkp->sum(),
            'paid' => (int) ($statusGaji['paid'] ?? 0),
            'unpaid' => (int) ($statusGaji['unpaid'] ?? 0),
            'top_perusahaan' => $top_perusahaan,
            'sebaran_bidang' => $sebaran_bidang,
            'magang_aktif' => Magang::with(['mahasiswa.user', 'perusahaan'])
                ->sedangBerlangsung()
                ->orderBy('tanggal_selesai', 'asc')
                ->get(),
            'magang_belum_skp' => Magang::with(['mahasiswa.user', 'perusahaan'])
                ->menungguSkp()
                ->orderBy('tanggal_selesai', 'desc')
                ->get(),
            'magang_lulus_skp' => Magang::with(['mahasiswa.user', 'perusahaan'])
                ->lulusSkp()
                ->orderBy('updated_at', 'desc')
                ->get(),
        ]);
    }

    public function statistikDetail(Request $request, $kategori)
    {
        $role = Auth::user()->role;
        abort_unless(in_array($role, ['kaprodi', 'admin']), 403);

        $filterYear = $request->has('tahun') ? $request->input('tahun') : null;
        $hasYear = $request->has('tahun');
        $titleSuffix = $hasYear ? " ($filterYear)" : ' (Semua Tahun)';

        $title = 'Detail Statistik';
        $chartLabels = [];
        $chartValues = [];
        $chartType = 'bar';
        $total_data = 0;
        $list_mahasiswa = collect();

        switch ($kategori) {
            case 'pengajuan':
                $title = 'Aktif Magang'.$titleSuffix;
                $chartType = 'line';
                [$chartLabels, $chartValues, $total_data] = $this->monthlyChart('created_at', $filterYear);
                break;

            case 'aktif':
                $title = 'Mahasiswa Aktif Magang'.$titleSuffix;
                $list_mahasiswa = Magang::with(['mahasiswa.user', 'perusahaan'])
                    ->diterima()
                    ->when($filterYear, fn ($q) => $q->whereYear('tanggal_mulai', $filterYear))
                    ->whereDate('tanggal_selesai', '>=', now())
                    ->orderBy('tanggal_selesai', 'asc')
                    ->paginate(10);

                [$chartLabels, $chartValues, $total_data] = $this->monthlyChart('tanggal_mulai', $filterYear, fn ($q) => $q->where('status_validasi', 'diterima'));
                break;

            case 'lulus':
            case 'belum-skp':
                $title = ($kategori === 'lulus' ? 'Mahasiswa Lulus (Sudah SKP)' : 'Mahasiswa Menunggu SKP').$titleSuffix;
                $status_skp = $kategori === 'lulus' ? 'sudah' : 'belum';

                $list_mahasiswa = Magang::with(['mahasiswa.user', 'perusahaan'])
                    ->diterima()
                    ->when($filterYear, fn ($q) => $q->whereYear('tanggal_selesai', $filterYear))
                    ->when($kategori === 'belum-skp', fn ($q) => $q->whereDate('tanggal_selesai', '<', now()))
                    ->where('status_skp', $status_skp)
                    ->orderBy('tanggal_selesai', 'desc')
                    ->paginate(10);

                [$chartLabels, $chartValues, $total_data] = $this->monthlyChart('tanggal_selesai', $filterYear, fn ($q) => $q->where('status_validasi', 'diterima')->where('status_skp', $status_skp));
                break;

            case 'gaji':
                $title = 'Status Pendapatan (Paid vs Unpaid)'.$titleSuffix;
                $chartType = 'doughnut';

                $gajiQuery = Magang::diterima()
                    ->when($filterYear, fn ($q) => $q->whereYear('tanggal_mulai', $filterYear));

                $paid = (clone $gajiQuery)->where('status_gaji', 'paid')->count();
                $unpaid = (clone $gajiQuery)->where('status_gaji', 'unpaid')->count();

                $chartLabels = ['Paid (Dibayar)', 'Unpaid (Tidak Dibayar)'];
                $chartValues = [$paid, $unpaid];
                $total_data = $paid + $unpaid;

                $list_mahasiswa = (clone $gajiQuery)
                    ->with(['mahasiswa.user', 'perusahaan'])
                    ->orderBy('status_gaji', 'asc')
                    ->paginate(10);
                break;

            default:
                abort(404);
        }

        return view('dashboard.statistik-detail', compact(
            'title',
            'chartLabels',
            'chartValues',
            'chartType',
            'kategori',
            'total_data',
            'list_mahasiswa'
        ));
    }

    private function getSharedData(): array
    {
        $total_keseluruhan = Magang::count();

        return [
            'role' => Auth::user()->role,
            'total_mahasiswa' => Mahasiswa::count(),
            'sedang_magang' => Magang::whereDate('tanggal_selesai', '>=', now())->count(),
            'belum_skp' => Magang::whereDate('tanggal_selesai', '<', now())->skpBelum()->count(),
            'paid' => Magang::gajiPaid()->count(),
            'unpaid' => Magang::gajiUnpaid()->count(),
            'lokasi_magang' => $this->lokasiMagang(
                Magang::with(['perusahaan', 'mahasiswa.user'])->get()
            ),
            'top_perusahaan' => DB::table('magangs')
                ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
                ->select('perusahaans.nama_perusahaan', DB::raw('count(magangs.id) as total'))
                ->groupBy('perusahaans.nama_perusahaan')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'sebaran_bidang' => DB::table('magangs')
                ->join('perusahaans', 'magangs.perusahaan_id', '=', 'perusahaans.id')
                ->select('perusahaans.kategori_industri', DB::raw('count(magangs.id) as total'))
                ->groupBy('perusahaans.kategori_industri')
                ->get()
                ->map(fn ($item) => (object) [
                    'kategori_industri' => $item->kategori_industri,
                    'total' => $item->total,
                    'persentase' => $total_keseluruhan > 0 ? round(($item->total / $total_keseluruhan) * 100, 1) : 0,
                ]),
        ];
    }

    public function pantauanSkp()
    {
        $mahasiswaOverdue = $this->overdueMahasiswaQuery()->paginate(10);

        return view('monitoring.pantauan-skp', compact('mahasiswaOverdue'));
    }

    public function exportPantauanPdf(Request $request)
    {
        $mahasiswaOverdue = $this->overdueMahasiswaQuery()->get();

        $pdf = Pdf::loadView('monitoring.export_pdf', compact('mahasiswaOverdue'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Laporan-Pantauan-SKP-Terlewat-'.now()->format('Y-m-d').'.pdf');
    }

    private function overdueMahasiswaQuery(): Builder
    {
        return Mahasiswa::select('mahasiswas.*')
            ->join('magangs', 'mahasiswas.id', '=', 'magangs.mahasiswa_id')
            ->where('magangs.status_validasi', 'diterima')
            ->where('magangs.status_skp', 'belum')
            ->where('magangs.tanggal_selesai', '<', now()->subDays(30))
            ->orderBy('magangs.tanggal_selesai', 'desc')
            ->with(['user', 'magangs.perusahaan', 'magangs.dosen']);
    }

    private function lokasiMagang(\Illuminate\Support\Collection $magangs): \Illuminate\Support\Collection
    {
        return $magangs
            ->groupBy('perusahaan_id')
            ->map(function ($items) {
                $aktif = $items->filter(function ($m) {
                    return $m->status_skp === 'belum' && $m->tanggal_selesai?->gte(now()->startOfDay());
                });

                if ($aktif->isEmpty()) {
                    return null;
                }

                $first = $aktif->first();
                $perusahaan = $first->perusahaan;

                return [
                    'nama_mhs' => $aktif->pluck('mahasiswa.user.name')->toArray(),
                    'perusahaan' => $perusahaan->nama_perusahaan,
                    'lat' => $perusahaan->latitude,
                    'lng' => $perusahaan->longitude,
                    'status' => $aktif->pluck('status_gaji')->first(),
                    'status_skp' => 'belum',
                    'is_selesai' => false,
                ];
            })
            ->filter()
            ->values();
    }

    private function monthlyChart(string $dateColumn, ?int $year, ?callable $applyFilters = null): array
    {
        $query = DB::table('magangs')
            ->selectRaw("MONTH({$dateColumn}) as bulan, COUNT(*) as total");

        if ($applyFilters) {
            $applyFilters($query);
        }

        if ($year) {
            $query->whereYear($dateColumn, $year);
        }

        $data = $query->groupBy('bulan')->pluck('total', 'bulan')->toArray();

        $labels = [];
        $values = [];
        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('M', mktime(0, 0, 0, $i, 10));
            $values[] = $data[$i] ?? 0;
        }

        return [$labels, $values, array_sum($values)];
    }
}
