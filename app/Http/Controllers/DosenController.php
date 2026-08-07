<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Magang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    // -------------------------------------------------------------------------
    // DASHBOARD (Statistik & Peta)
    // -------------------------------------------------------------------------
    public function index()
    {
        $dosenId = Auth::id();
        $hariIni = Carbon::now();

        $bimbingan = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->bimbingan($dosenId)
            ->diterima()
            ->get();

        $lokasi_magang = $bimbingan->map(function ($m) {
            $selesai = Carbon::parse($m->tanggal_selesai)->endOfDay();

            $status = match (true) {
                $m->status_skp === 'sudah' => 'Selesai (Lulus SKP)',
                $selesai->isPast() => 'Proses Seminar',
                default => 'Aktif Magang',
            };

            return [
                'id' => $m->id,
                'nama_mhs' => $m->mahasiswa->user->name,
                'nim' => $m->mahasiswa->nim,
                'perusahaan' => $m->perusahaan->nama_perusahaan,
                'lat' => $m->perusahaan->latitude,
                'lng' => $m->perusahaan->longitude,
                'tanggal_mulai' => $m->tanggal_mulai,
                'tanggal_selesai' => $m->tanggal_selesai,
                'status' => $status,
            ];
        });

        $marker_locations = $bimbingan->groupBy('perusahaan_id')->map(function ($magangs) {
            $first = $magangs->first();
            $perusahaan = $first->perusahaan;

            $hasActive = $magangs->contains(function ($m) {
                return $m->status_skp === 'belum'
                    && Carbon::parse($m->tanggal_selesai)->endOfDay()->isFuture();
            });

            return [
                'nama_mhs' => $magangs->pluck('mahasiswa.user.name')->toArray(),
                'perusahaan' => $perusahaan->nama_perusahaan,
                'lat' => $perusahaan->latitude,
                'lng' => $perusahaan->longitude,
                'status' => $hasActive ? 'Aktif Magang' : 'Proses Seminar',
            ];
        })->values();

        $stat = [
            'total' => $bimbingan->count(),
            'aktif' => $bimbingan->filter(fn ($m) => $m->status_skp === 'belum' && $m->tanggal_selesai >= $hariIni->toDateString())->count(),
            'selesai_magang' => $bimbingan->filter(fn ($m) => $m->status_skp === 'belum' && $m->tanggal_selesai < $hariIni->toDateString())->count(),
            'sudah_skp' => $bimbingan->where('status_skp', 'sudah')->count(),
        ];

        $agendaSkp = Magang::with('mahasiswa.user')
            ->where('dosen_id', $dosenId)
            ->where('status_jadwal_skp', 'disetujui')
            ->where('status_skp', 'belum')
            ->orderBy('jadwal_terpilih', 'asc')
            ->get();

        return view('dosen.dashboard', compact('lokasi_magang', 'stat', 'marker_locations', 'agendaSkp'));
    }

    // -------------------------------------------------------------------------
    // BIMBINGAN
    // -------------------------------------------------------------------------
    public function bimbingan()
    {
        $bimbingan = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->bimbingan(Auth::id())
            ->diterima()
            ->skpBelum()
            ->where('tanggal_selesai', '>=', now())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dosen.bimbingan.index', compact('bimbingan'));
    }

    public function detail($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->bimbingan(Auth::id())
            ->findOrFail($id);

        return view('dosen.bimbingan.detail', compact('magang'));
    }

    public function logbook($id)
    {
        $magang = Magang::with([
            'mahasiswa.user',
            'logbooks' => fn ($query) => $query->orderBy('minggu_ke', 'asc'),
        ])
            ->bimbingan(Auth::id())
            ->findOrFail($id);

        return view('dosen.bimbingan.logbook', compact('magang'));
    }

    public function reviewLogbook(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->update([
            'komentar_dosen' => $request->input('komentar_dosen'),
            'status_acc' => true,
        ]);

        return back()->with('success', 'Logbook berhasil di-ACC dan komentar telah disimpan.');
    }

    // -------------------------------------------------------------------------
    // SKP: Pengajuan Jadwal
    // -------------------------------------------------------------------------
    public function skpIndex()
    {
        $pengajuanSkp = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->bimbingan(Auth::id())
            ->diterima()
            ->where(function ($query) {
                $query->where('tanggal_selesai', '<', now())
                    ->orWhere('status_jadwal_skp', '!=', 'belum');
            })
            ->where(function ($query) {
                $query->where('status_jadwal_skp', '!=', 'disetujui')
                    ->orWhere(function ($q) {
                        $q->where('status_jadwal_skp', 'disetujui')
                            ->where('jadwal_terpilih', '>=', now());
                    });
            })
            ->orderByRaw("CASE WHEN status_jadwal_skp = 'menunggu' THEN 1 ELSE 2 END ASC")
            ->orderBy('tanggal_selesai', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('dosen.skp.index', compact('pengajuanSkp'));
    }

    public function skpRespon($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('id', $id)
            ->where('dosen_id', Auth::id())
            ->firstOrFail();

        return view('dosen.skp.respon', compact('magang'));
    }

    public function approveJadwalSkp(Request $request, $id)
    {
        $request->validate([
            'pilihan_opsi' => 'required|in:1,2,3,4,5,6,7',
        ]);

        $magang = Magang::where('id', $id)->where('dosen_id', Auth::id())->firstOrFail();

        $magang->update([
            'status_jadwal_skp' => 'disetujui',
            'jadwal_terpilih' => $magang->{'jadwal_opsi_'.$request->pilihan_opsi},
            'keterangan_tolak_jadwal' => null,
        ]);

        return redirect()->route('dosen.skp.index')
            ->with('success', 'Jadwal SKP berhasil disetujui. Mahasiswa akan menerima notifikasi.');
    }

    public function rejectJadwalSkp(Request $request, $id)
    {
        $request->validate([
            'keterangan_tolak' => 'required|string|max:500',
        ]);

        $magang = Magang::where('id', $id)->where('dosen_id', Auth::id())->firstOrFail();

        $magang->update([
            'status_jadwal_skp' => 'ditolak',
            'jadwal_terpilih' => null,
            'keterangan_tolak_jadwal' => $request->keterangan_tolak,
        ]);

        return redirect()->route('dosen.skp.index')
            ->with('success', 'Jadwal ditolak. Mahasiswa diminta untuk mengajukan opsi jadwal baru.');
    }

    // -------------------------------------------------------------------------
    // RIWAYAT MAGANG (Hanya yang sudah SKP)
    // -------------------------------------------------------------------------
    public function riwayatMagang(Request $request)
    {
        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->bimbingan(Auth::id())
            ->diterima()
            ->skpSudah();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa.user', fn ($user) => $user->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('perusahaan', fn ($perusahaan) => $perusahaan->where('nama_perusahaan', 'like', "%{$search}%"));
            });
        }

        $riwayat = $query
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('tanggal_mulai', $request->bulan))
            ->when($request->filled('tahun'), fn ($q) => $q->whereYear('tanggal_mulai', $request->tahun))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('riwayat_magang.index', compact('riwayat'));
    }

    public function showRiwayat($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->bimbingan(Auth::id())
            ->findOrFail($id);

        return view('riwayat_magang.show', compact('magang'));
    }
}
