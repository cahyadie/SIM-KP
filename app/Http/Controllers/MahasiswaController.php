<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Magang;
use App\Models\Mahasiswa;
use App\Models\User;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        // 1. Ambil data mahasiswa yang sedang login
        $mahasiswa = Auth::user()->mahasiswa;

        // 2. Cek apakah mahasiswa ada (safety check jika data belum lengkap)
        if (!$mahasiswa) {
            return redirect()->route('profile.edit')->with('warning', 'Silakan lengkapi profil terlebih dahulu.');
        }

        // 3. Ambil RIWAYAT magang
        // Mengambil semua magang milik mahasiswa ini, urutkan dari yang terbaru
        // PERUBAHAN: Tambahkan relasi 'dosen' (sesuaikan dengan nama fungsi relasi di model Magang Anda)
        $riwayat_magang = Magang::with(['perusahaan', 'dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Ambil data magang AKTIF (paling baru) untuk keperluan widget di dashboard
        $magang = $riwayat_magang->first();

        // 5. Kirim ke View
        return view('mahasiswa.dashboard', compact('mahasiswa', 'riwayat_magang', 'magang'));
    }

    /**
     * Menampilkan Halaman Seminar (Form Input Nilai & Upload)
     */
    public function seminar()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        // Cari data magang mahasiswa ini (karena pendaftaran otomatis 'diterima', data pasti langsung terbaca)
        $magang = Magang::where('mahasiswa_id', $mahasiswa->id)
            ->where('status_validasi', 'diterima')
            ->latest()
            ->first();

        if (!$magang) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum terdaftar dalam program magang.');
        }

        return view('mahasiswa.seminar', compact('magang'));
    }

    /**
     * Memproses Simpan Nilai & Upload Berkas Seminar
     */
    /**
     * Memproses Simpan Nilai & Upload Berkas Seminar (Langsung Terbit SKP)
     */
    public function seminarStore(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        // 1. Ambil data magang yang aktif
        $magang = Magang::where('mahasiswa_id', $mahasiswa->id)
            ->where('status_validasi', 'diterima')
            ->latest()
            ->firstOrFail();

        // 2. VALIDASI INPUT
        $request->validate([
            'nilai_seminar' => 'required|in:A,B,C,D,E',
            // Gunakan logika validasi: Wajib jika file belum ada di DB
            'file_seminar' => $magang->file_seminar ? 'nullable|mimes:pdf|max:5120' : 'required|mimes:pdf|max:5120',
        ]);

        // 3. SIAPKAN DATA
        $dataUpdate = [
            'nilai_seminar' => $request->nilai_seminar,
            'status_skp' => 'sudah', // <-- UBAH DI SINI: Langsung jadi 'sudah' agar SKP otomatis terbit
            'keterangan_revisi' => null,    // Reset field revisi
        ];

        // 4. UPLOAD FILE
        if ($request->hasFile('file_seminar')) {
            // Hapus file lama jika ada
            if ($magang->file_seminar && Storage::disk('public')->exists($magang->file_seminar)) {
                Storage::disk('public')->delete($magang->file_seminar);
            }
            // Simpan file baru
            $path = $request->file('file_seminar')->store('laporan_seminar', 'public');
            $dataUpdate['file_seminar'] = $path;
        }

        // 5. EKSEKUSI UPDATE
        $magang->update($dataUpdate);

        // Sesuaikan pesan balikan agar relevan dengan alur baru
        return redirect()->back()->with('success', 'Data seminar berhasil disimpan dan SKP telah diterbitkan!');
    }

    public function ajukanJadwal(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $magang = Magang::where('mahasiswa_id', $mahasiswa->id)
            ->where('status_validasi', 'diterima')
            ->latest()
            ->firstOrFail();

        // 1. Tambahkan validasi untuk file surat selesai magang
        $request->validate([
            'jadwal_opsi_1' => 'required|date|after:today',
            'jadwal_opsi_2' => 'required|date|after:today|different:jadwal_opsi_1',
            'jadwal_opsi_3' => 'required|date|after:today|different:jadwal_opsi_1|different:jadwal_opsi_2',
            // 'ruangan_skp' => 'required|string|max:255',
            // Jika file sudah pernah diupload (misal jadwal ditolak dan ajukan ulang), file tidak wajib. Jika belum, wajib.
            'surat_selesai_magang' => $magang->surat_selesai_magang ? 'nullable|mimes:pdf|max:2048' : 'required|mimes:pdf|max:2048',
        ], [
            'jadwal_opsi_2.different' => 'Opsi 2 harus berbeda dengan Opsi 1.',
            'jadwal_opsi_3.different' => 'Opsi 3 harus berbeda dengan opsi lainnya.',
            'after' => 'Jadwal tidak boleh di masa lalu atau hari ini (harus minimal besok).'
        ]);

        // 2. Proses upload file jika ada
        if ($request->hasFile('surat_selesai_magang')) {
            // Hapus file lama jika ada (opsional)
            if ($magang->surat_selesai_magang && Storage::disk('public')->exists($magang->surat_selesai_magang)) {
                Storage::disk('public')->delete($magang->surat_selesai_magang);
            }
            // Simpan file baru
            $pathSurat = $request->file('surat_selesai_magang')->store('surat_selesai', 'public');
            $magang->surat_selesai_magang = $pathSurat;
        }

        // 3. Update data jadwal
        $magang->update([
            'jadwal_opsi_1' => $request->jadwal_opsi_1,
            'jadwal_opsi_2' => $request->jadwal_opsi_2,
            'jadwal_opsi_3' => $request->jadwal_opsi_3,
            // 'ruangan_skp' => $request->ruangan_skp,
            'status_jadwal_skp' => 'menunggu',
            'keterangan_tolak_jadwal' => null,
            'surat_selesai_magang' => $magang->surat_selesai_magang // Simpan path surat
        ]);

        return redirect()->back()->with('success', '3 Opsi jadwal dan Surat Selesai Magang berhasil diajukan. Silakan tunggu persetujuan dari Dosen Pembimbing.');
    }

    public function riwayatMagang()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $riwayat_magang = Magang::with(['perusahaan', 'dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.riwayat_magang.index', compact('riwayat_magang'));
    }

    public function editMagang($id)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $magang = Magang::with(['perusahaan', 'dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->findOrFail($id);

        $daftar_dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('mahasiswa.riwayat_magang.edit', compact('magang', 'daftar_dosen'));
    }

    public function updateMagang(Request $request, $id)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $magang = Magang::where('mahasiswa_id', $mahasiswa->id)->findOrFail($id);

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tema_magang' => 'required|string|max:255',
            'status_gaji' => 'required|in:paid,unpaid',
            'dosen_id' => 'required|exists:users,id',
        ]);

        $magang->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tema_magang' => $request->tema_magang,
            'status_gaji' => $request->status_gaji,
            'dosen_id' => $request->dosen_id,
        ]);

        return redirect()->route('mahasiswa.riwayat-magang.index')
            ->with('success', 'Data magang berhasil diperbarui.');
    }
}