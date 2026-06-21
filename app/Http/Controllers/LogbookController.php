<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Magang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LogbookController extends Controller
{
    public function index($magang_id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // 1. CEK: Apakah User sudah melengkapi Profil Mahasiswa?
        // Jika habis migrate:fresh, data ini pasti hilang.
        if (!$user->mahasiswa) {
            return redirect()->route('profile.edit')
                ->with('error', 'Silakan lengkapi data profil mahasiswa terlebih dahulu.');
        }

        // 2. CEK: Apakah Data Magang dengan ID tersebut ada?
        // Karena migrate:fresh, ID 4 pasti sudah hilang.
        $magang = \App\Models\Magang::where('id', $magang_id)
            ->where('mahasiswa_id', $user->mahasiswa->id)
            ->first();

        if (!$magang) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Data magang tidak ditemukan (Mungkin database baru di-reset).');
        }

        // 3. Ambil Logbook Mingguan
        $logbooks = $magang->logbooks()->orderBy('tgl_mulai', 'desc')->get();

        return view('mahasiswa.logbook.index', compact('magang', 'logbooks'));
    }

    // 2. Menampilkan Form Tambah Logbook
    // 2. Menampilkan Form Tambah Logbook
    public function create($magang_id)
    {
        $magang = Magang::findOrFail($magang_id);

        // 1. Menghitung minggu ke berapa otomatis
        $minggu_ke = $magang->logbooks()->max('minggu_ke') + 1;

        // 2. Hitung tanggal mulai untuk minggu ini
        // (Tanggal mulai magang + ((minggu_ke - 1) * 7 hari))
        $tgl_mulai_minggu = \Carbon\Carbon::parse($magang->tanggal_mulai)->addDays(($minggu_ke - 1) * 7);
        $tgl_selesai_magang = \Carbon\Carbon::parse($magang->tanggal_selesai);

        // Jika tanggal mulai minggu ini sudah melebihi tanggal selesai magang,
        // artinya mahasiswa sudah selesai mengisi seluruh logbook.
        if ($tgl_mulai_minggu > $tgl_selesai_magang) {
            return redirect()->route('logbook.index', $magang_id)
                ->with('error', 'Semua logbook untuk periode magang ini sudah terisi penuh.');
        }

        // 3. Hitung tanggal selesai untuk minggu ini (tambah 6 hari dari tgl mulai)
        $tgl_selesai_minggu = $tgl_mulai_minggu->copy()->addDays(6);

        // Jika tanggal selesai minggu ini melewati batas akhir magang, batasi di tanggal selesai magang
        if ($tgl_selesai_minggu > $tgl_selesai_magang) {
            $tgl_selesai_minggu = $tgl_selesai_magang;
        }

        // 4. Buat array daftar hari untuk di-loop langsung di Blade
        $hari_minggu_ini = [];
        $currentDate = $tgl_mulai_minggu->copy();
        while ($currentDate <= $tgl_selesai_minggu) {
            $hari_minggu_ini[] = $currentDate->copy();
            $currentDate->addDay();
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
        // Validasi input (Hapus rule 'trim')
        $request->validate([
            'minggu_ke' => 'required|integer|min:1',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'log' => 'required|array', // Data harian harus ada
            'log.*.kegiatan' => 'required|string', // Setiap hari harus ada kegiatan
            'log.*.permasalahan' => 'required|string', // Setiap hari harus ada permasalahan
            'log.*.solusi' => 'required|string', // Setiap hari harus ada solusi
        ], [
            // Custom pesan error agar lebih ramah dibaca pengguna
            'log.*.kegiatan.required' => 'Kegiatan harian wajib diisi.',
            'log.*.permasalahan.required' => 'Permasalahan wajib diisi (ketik "-" jika tidak ada).',
            'log.*.solusi.required' => 'Solusi wajib diisi (ketik "-" jika tidak ada).'
        ]);

        $magang = \App\Models\Magang::findOrFail($id);

        // Simpan Data
        $magang->logbooks()->create([
            'minggu_ke' => $request->minggu_ke,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'isi_logbook' => $request->log // Array otomatis jadi JSON
        ]);

        return redirect()->route('logbook.index', $id)
            ->with('success', 'Logbook mingguan berhasil disimpan!');
    }
}