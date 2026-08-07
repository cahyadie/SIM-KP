<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    public function index($magang_id)
    {
        $user = Auth::user();

        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.profile.edit')
                ->with('error', 'Silakan lengkapi data profil mahasiswa terlebih dahulu.');
        }

        $magang = $this->ownedMagang($magang_id);

        if (! $magang) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Data magang tidak ditemukan.');
        }

        $logbooks = $magang->logbooks()->orderBy('tgl_mulai', 'desc')->get();

        return view('mahasiswa.logbook.index', compact('magang', 'logbooks'));
    }

    public function create($magang_id)
    {
        $magang = $this->ownedMagang($magang_id);

        if (! $magang) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data magang tidak ditemukan.');
        }

        $minggu_ke = $magang->logbooks()->max('minggu_ke') + 1;

        $tgl_mulai_minggu = Carbon::parse($magang->tanggal_mulai)->addDays(($minggu_ke - 1) * 7);
        $tgl_selesai_magang = Carbon::parse($magang->tanggal_selesai);

        if ($tgl_mulai_minggu > $tgl_selesai_magang) {
            return redirect()->route('mahasiswa.logbook.index', $magang_id)
                ->with('error', 'Semua logbook untuk periode magang ini sudah terisi penuh.');
        }

        $tgl_selesai_minggu = $tgl_mulai_minggu->copy()->addDays(6)->min($tgl_selesai_magang);

        $hari_minggu_ini = [];
        for ($date = $tgl_mulai_minggu->copy(); $date <= $tgl_selesai_minggu; $date->addDay()) {
            $hari_minggu_ini[] = $date->copy();
        }

        return view('mahasiswa.logbook.create', compact(
            'magang',
            'minggu_ke',
            'tgl_mulai_minggu',
            'tgl_selesai_minggu',
            'hari_minggu_ini'
        ));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'minggu_ke' => 'required|integer|min:1',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'log' => 'required|array',
            'log.*.kegiatan' => 'required|string',
            'log.*.permasalahan' => 'required|string',
            'log.*.solusi' => 'required|string',
        ], [
            'log.*.kegiatan.required' => 'Kegiatan harian wajib diisi.',
            'log.*.permasalahan.required' => 'Permasalahan wajib diisi (ketik "-" jika tidak ada).',
            'log.*.solusi.required' => 'Solusi wajib diisi (ketik "-" jika tidak ada).',
        ]);

        $magang = $this->ownedMagang($id);

        if (! $magang) {
            return back()->with('error', 'Data magang tidak ditemukan.');
        }

        $magang->logbooks()->create([
            'minggu_ke' => $request->minggu_ke,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'isi_logbook' => $request->log,
        ]);

        NotifikasiService::kirim(
            $magang->dosen,
            'logbook',
            'Mahasiswa '.$magang->mahasiswa->user->name.' mengisi logbook Minggu Ke-'.$request->minggu_ke,
            route('dosen.bimbingan.logbook', $magang->id),
            'bi-journal-bookmark-fill',
            $magang->id,
        );

        return redirect()->route('mahasiswa.logbook.index', $id)
            ->with('success', 'Logbook mingguan berhasil disimpan!');
    }

    private function ownedMagang(int $magangId): ?Magang
    {
        return Magang::where('id', $magangId)
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->first();
    }
}
