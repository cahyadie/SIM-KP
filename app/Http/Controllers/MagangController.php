<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Magang;
use App\Models\Perusahaan;
use App\Models\Mahasiswa;
use App\Models\User; // Pastikan import model User
use Illuminate\Support\Facades\Auth;

class MagangController extends Controller
{
    // 1. Tampilkan Form Pendaftaran
    public function create()
    {
        $user = Auth::user();
        
        Mahasiswa::firstOrCreate(
            ['user_id' => Auth::id()],
            ['nim' => Auth::user()->nomor_induk ?? '0000', 'angkatan' => '2023', 'prodi' => 'TI']
        );

        // AMBIL DATA DOSEN (User dengan role 'dosen')
        $daftar_dosen = User::where('role', 'dosen')->orderBy('name')->get();

        // Pastikan 'daftar_dosen' dikirim ke view menggunakan compact
        return view('mahasiswa.daftar_magang', compact('daftar_dosen'));
    }

    public function store(Request $request)
    {
        // 1. Proses Upload File
        $filePath = null;
        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('surat_magang', 'public');
        }

        // 3. Simpan ke Database
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        Magang::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $request->dosen_id, // Simpan ID Dosen
            'perusahaan_id' => $this->findOrCreatePerusahaan($request),
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status_gaji' => $request->status_gaji,
            'tema_magang' => $request->tema_magang,
            // UBAH DISINI: Langsung set menjadi 'diterima' tanpa pending
            'status_validasi' => 'diterima', 
            'file_surat_kaprodi' => $filePath,
        ]);

        // UBAH DISINI: Pesan flash disesuaikan agar tidak ada kata "Menunggu validasi"
        return redirect()->route('mahasiswa.dashboard')->with('success', 'Pendaftaran berhasil! Anda sudah terdaftar dalam program magang.');
    }

    // Helper untuk menangani data perusahaan
    private function findOrCreatePerusahaan($request) {
        $perusahaan = Perusahaan::firstOrCreate(
            ['nama_perusahaan' => $request->nama_perusahaan],
            [
                'alamat' => $request->alamat,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'kategori_industri' => $request->kategori_industri
            ]
        );
        return $perusahaan->id;
    }

    // Tampilkan Halaman Upload Seminar
    public function seminar()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        // Ambil data magang aktif
        $magang = $mahasiswa->magangs()->where('status_validasi', 'diterima')->first();

        if(!$magang) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum memiliki magang yang aktif/diterima.');
        }

        return view('mahasiswa.seminar', compact('magang'));
    }

    // Proses Simpan File Seminar
    public function storeSeminar(Request $request)
    {
        $request->validate([
            'file_seminar' => 'required|mimes:pdf|max:5120', // Max 5MB
            'file_nilai_lapangan' => 'nullable|mimes:pdf|max:5120', // Opsional: Nilai Lapangan
        ]);

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        $magang = $mahasiswa->magangs()->where('status_validasi', 'diterima')->firstOrFail();

        $updateData = [];

        if ($request->hasFile('file_seminar')) {
            $updateData['file_seminar'] = $request->file('file_seminar')->store('seminar_files', 'public');
        }

        if ($request->hasFile('file_nilai_lapangan')) {
            $updateData['file_nilai_lapangan'] = $request->file('file_nilai_lapangan')->store('seminar_files', 'public');
        }

        if (!empty($updateData)) {
            $magang->update($updateData);
        }

        return back()->with('success', 'Berkas berhasil diupload! Menunggu penilaian.');
    }
}