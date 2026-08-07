<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagangController extends Controller
{
    public function create()
    {
        Mahasiswa::firstOrCreate(
            ['user_id' => Auth::id()],
            ['nim' => Auth::user()->nomor_induk ?? '0000', 'angkatan' => '2023', 'prodi' => 'TI']
        );

        $daftar_dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('mahasiswa.daftar_magang', compact('daftar_dosen'));
    }

    public function store(Request $request)
    {
        $filePath = $request->hasFile('file_surat')
            ? $request->file('file_surat')->store('surat_magang', 'public')
            : null;

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $magang = Magang::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $request->dosen_id,
            'perusahaan_id' => $this->findOrCreatePerusahaan($request),
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status_gaji' => $request->status_gaji,
            'tema_magang' => $request->tema_magang,
            'status_validasi' => 'diterima',
            'file_surat_kaprodi' => $filePath,
        ]);

        $dosen = User::find($request->dosen_id);
        NotifikasiService::kirim(
            $dosen,
            'mulai_magang',
            'Mahasiswa '.$mahasiswa->user->name.' mulai magang di '.$magang->perusahaan->nama_perusahaan,
            route('dosen.bimbingan.detail', $magang->id),
            'bi-rocket-takeoff-fill',
            $magang->id,
        );

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Pendaftaran berhasil! Anda sudah terdaftar dalam program magang.');
    }

    private function findOrCreatePerusahaan(Request $request): int
    {
        $perusahaan = Perusahaan::firstOrCreate(
            ['nama_perusahaan' => $request->nama_perusahaan],
            [
                'alamat' => $request->alamat,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'kategori_industri' => $request->kategori_industri,
            ]
        );

        return $perusahaan->id;
    }
}
