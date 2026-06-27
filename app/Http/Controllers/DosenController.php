<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Magang;
use Carbon\Carbon;

class DosenController extends Controller
{
    // =========================================================================
    // 1. DASHBOARD UMUM (Statistik & Peta)
    // =========================================================================
    public function index()
    {
        $dosenId = Auth::id();
        $hariIni = \Carbon\Carbon::now();

        $bimbingan = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('dosen_id', $dosenId)
            ->where('status_validasi', 'diterima')
            ->get();

        // 1. DATA PETA DENGAN 3 STATUS TRANSISI YANG KONSISTEN
        $lokasi_magang = $bimbingan->map(function ($m) use ($hariIni) {
            $tglSelesai = \Carbon\Carbon::parse($m->tanggal_selesai)->endOfDay();

            if ($m->status_skp == 'sudah') {
                $statusText = 'Selesai (Lulus SKP)';
            } elseif ($tglSelesai->isPast()) {
                $statusText = 'Proses Seminar';
            } else {
                $statusText = 'Aktif Magang';
            }

            return [
                'id' => $m->id,
                'nama_mhs' => $m->mahasiswa->user->name,
                'nim' => $m->mahasiswa->nim,
                'perusahaan' => $m->perusahaan->nama_perusahaan,
                'lat' => $m->perusahaan->latitude,
                'lng' => $m->perusahaan->longitude,
                'tanggal_mulai' => $m->tanggal_mulai,
                'tanggal_selesai' => $m->tanggal_selesai,
                'status' => $statusText
            ];
        });

        // Data untuk peta (dikelompokkan per perusahaan)
        $marker_locations = $bimbingan->groupBy('perusahaan_id')->map(function ($magangs) use ($hariIni) {
            $first = $magangs->first();
            $perusahaan = $first->perusahaan;

            $hasActive = $magangs->contains(function ($m) use ($hariIni) {
                return \Carbon\Carbon::parse($m->tanggal_selesai)->endOfDay()->isFuture()
                    && $m->status_skp == 'belum';
            });

            return [
                'nama_mhs' => $magangs->pluck('mahasiswa.user.name')->toArray(),
                'perusahaan' => $perusahaan->nama_perusahaan,
                'lat' => $perusahaan->latitude,
                'lng' => $perusahaan->longitude,
                'status' => $hasActive ? 'Aktif Magang' : 'Proses Seminar',
            ];
        })->values();

        // 2. STATISTIK BARU (Dipecah menjadi 4 Metrik)
        $stat = [
            'total' => $bimbingan->count(),
            'aktif' => $bimbingan->filter(function ($m) use ($hariIni) {
                return $m->tanggal_selesai >= $hariIni->toDateString()
                    && $m->status_skp == 'belum';
            })->count(),
            'selesai_magang' => $bimbingan->filter(function ($m) use ($hariIni) {
                return $m->tanggal_selesai < $hariIni->toDateString()
                    && $m->status_skp == 'belum';
            })->count(),
            'sudah_skp' => $bimbingan->where('status_skp', 'sudah')->count(),
        ];

        return view('dosen.dashboard', compact('lokasi_magang', 'stat', 'marker_locations'));
    }

    // =========================================================================
    // 2. BIMBINGAN: List Daftar Mahasiswa Bimbingan Aktif
    // =========================================================================
    public function bimbingan()
    {
        $hariIni = \Carbon\Carbon::now();

        $bimbingan = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('dosen_id', Auth::id())
            ->where('status_validasi', 'diterima')
            // KONDISI BARU: Hanya yang masih aktif magang
            ->where('status_skp', 'belum')
            ->where('tanggal_selesai', '>=', $hariIni)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dosen.bimbingan.index', compact('bimbingan'));
    }

    // =========================================================================
    // 3. BIMBINGAN: Halaman Detail Mahasiswa & Logbook
    // =========================================================================
    public function detail($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('dosen_id', Auth::id())
            ->findOrFail($id);

        return view('dosen.bimbingan.detail', compact('magang'));
    }

    // =========================================================================
    // 4. LOGBOOK: Menyimpan Evaluasi & ACC Logbook Mahasiswa
    // =========================================================================
    public function logbook($id)
    {
        $magang = Magang::with([
            'mahasiswa.user',
            'logbooks' => function ($q) {
                $q->orderBy('minggu_ke', 'asc');
            }
        ])
            ->where('dosen_id', Auth::id())
            ->findOrFail($id);

        return view('dosen.bimbingan.logbook', compact('magang'));
    }

    public function reviewLogbook(Request $request, $id)
    {
        $logbook = \App\Models\Logbook::findOrFail($id);
        $logbook->komentar_dosen = $request->input('komentar_dosen');
        $logbook->status_acc = true;
        $logbook->save();

        return back()->with('success', 'Logbook berhasil di-ACC dan komentar telah disimpan.');
    }

    // =========================================================================
    // 5. SKP: Menampilkan Daftar Pengajuan Jadwal & Magang Selesai
    // =========================================================================
    public function skpIndex()
    {
        $dosenId = Auth::id();
        $hariIni = Carbon::now();

        $pengajuanSkp = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('dosen_id', $dosenId)
            ->where('status_validasi', 'diterima')
            // Syarat lama: sudah selesai magang ATAU sudah mulai proses pengajuan jadwal
            ->where(function ($query) use ($hariIni) {
                $query->where('tanggal_selesai', '<', $hariIni)
                    ->orWhere('status_jadwal_skp', '!=', 'belum');
            })
            // SYARAT BARU: Filter jadwal yang sudah disetujui tapi tanggalnya sudah lewat
            ->where(function ($query) use ($hariIni) {
                // Tampilkan semua yang statusnya BUKAN disetujui (misal: menunggu, ditolak, belum)
                $query->where('status_jadwal_skp', '!=', 'disetujui')
                    // ATAU tampilkan yang disetujui, ASALKAN jadwal_terpilih belum lewat
                    ->orWhere(function ($q) use ($hariIni) {
                    $q->where('status_jadwal_skp', 'disetujui')
                        ->where('jadwal_terpilih', '>=', $hariIni);
                });
            })
            ->orderByRaw("CASE WHEN status_jadwal_skp = 'menunggu' THEN 1 ELSE 2 END ASC")
            ->orderBy('tanggal_selesai', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('dosen.skp.index', compact('pengajuanSkp'));
    }

    // =========================================================================
    // 6. SKP: Halaman Form Respon Opsi Jadwal
    // =========================================================================
    public function skpRespon($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->where('id', $id)
            ->where('dosen_id', Auth::id())
            ->firstOrFail();

        return view('dosen.skp.respon', compact('magang'));
    }

    // =========================================================================
    // 7. SKP: Proses Dosen Menyetujui (ACC) Jadwal
    // =========================================================================
    public function approveJadwalSkp(Request $request, $id)
    {
        $request->validate([
            'pilihan_opsi' => 'required|in:1,2,3'
        ]);

        $magang = Magang::where('id', $id)->where('dosen_id', Auth::id())->firstOrFail();

        $jadwalTerpilih = null;
        if ($request->pilihan_opsi == '1') {
            $jadwalTerpilih = $magang->jadwal_opsi_1;
        } elseif ($request->pilihan_opsi == '2') {
            $jadwalTerpilih = $magang->jadwal_opsi_2;
        } elseif ($request->pilihan_opsi == '3') {
            $jadwalTerpilih = $magang->jadwal_opsi_3;
        }

        $magang->update([
            'status_jadwal_skp' => 'disetujui',
            'jadwal_terpilih' => $jadwalTerpilih,
            'keterangan_tolak_jadwal' => null
        ]);

        return redirect()->route('dosen.skp.index')
            ->with('success', 'Jadwal SKP berhasil disetujui. Mahasiswa akan menerima notifikasi.');
    }

    // =========================================================================
    // 8. SKP: Proses Dosen Menolak Jadwal (Minta Ajukan Ulang)
    // =========================================================================
    public function rejectJadwalSkp(Request $request, $id)
    {
        $request->validate([
            'keterangan_tolak' => 'required|string|max:500'
        ]);

        $magang = Magang::where('id', $id)->where('dosen_id', Auth::id())->firstOrFail();

        $magang->update([
            'status_jadwal_skp' => 'ditolak',
            'jadwal_terpilih' => null,
            'keterangan_tolak_jadwal' => $request->keterangan_tolak
        ]);

        return redirect()->route('dosen.skp.index')
            ->with('success', 'Jadwal ditolak. Mahasiswa diminta untuk mengajukan opsi jadwal baru.');
    }

    // =========================================================================
    // 9. RIWAYAT MAGANG (Hanya Menampilkan yang Selesai SKP)
    // =========================================================================
    public function riwayatMagang(Request $request)
    {
        $dosenId = Auth::id();
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $search = $request->input('search');
        // (Status input dihapus karena sekarang hanya 1 status statis)

        // Tambahkan relasi 'dosen'
        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->where('dosen_id', $dosenId)
            ->where('status_validasi', 'diterima')
            // KONDISI BARU: HANYA TAMPILKAN YANG SUDAH SKP
            ->where('status_skp', 'sudah');

        // Filter Pencarian (Search)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa.user', function ($qMhs) use ($search) {
                    $qMhs->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('perusahaan', function ($qPT) use ($search) {
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

        $riwayat = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('riwayat_magang.index', compact('riwayat'));
    }

    // =========================================================================
    // 10. DETAIL RIWAYAT MAGANG (Khusus Dosen)
    // =========================================================================
    public function showRiwayat($id)
    {
        $magang = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->where('dosen_id', Auth::id())
            ->findOrFail($id);

        return view('riwayat_magang.show', compact('magang'));
    }
}