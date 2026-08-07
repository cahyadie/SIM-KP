<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        if (! $mahasiswa) {
            return redirect()->route('profile.edit')->with('warning', 'Silakan lengkapi profil terlebih dahulu.');
        }

        $riwayat_magang = $this->riwayatQuery($mahasiswa->id)->get();

        return view('mahasiswa.dashboard', [
            'mahasiswa' => $mahasiswa,
            'riwayat_magang' => $riwayat_magang,
            'magang' => $riwayat_magang->first(),
        ]);
    }

    public function seminar()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $magang = Magang::where('mahasiswa_id', $mahasiswa->id)
            ->diterima()
            ->latest()
            ->first();

        if (! $magang) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum terdaftar dalam program magang.');
        }

        return view('mahasiswa.seminar', compact('magang'));
    }

    public function seminarStore(Request $request)
    {
        $magang = Magang::where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->diterima()
            ->latest()
            ->firstOrFail();

        $request->validate([
            'nilai_seminar' => 'required|in:A,B,C,D,E',
            'file_seminar' => $magang->file_seminar ? 'nullable|mimes:pdf|max:5120' : 'required|mimes:pdf|max:5120',
        ]);

        $dataUpdate = [
            'nilai_seminar' => $request->nilai_seminar,
            'status_skp' => 'sudah',
            'keterangan_revisi' => null,
        ];

        if ($request->hasFile('file_seminar')) {
            $this->deleteStoredFile($magang->file_seminar);
            $dataUpdate['file_seminar'] = $request->file('file_seminar')->store('laporan_seminar', 'public');
        }

        $magang->update($dataUpdate);

        return redirect()->back()->with('success', 'Data seminar berhasil disimpan dan SKP telah diterbitkan!');
    }

    public function ajukanJadwal(Request $request)
    {
        $magang = Magang::where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->diterima()
            ->latest()
            ->firstOrFail();

        $this->validateJadwalRequest($request, $magang);

        if ($request->hasFile('surat_selesai_magang')) {
            $this->deleteStoredFile($magang->surat_selesai_magang);
            $magang->surat_selesai_magang = $request->file('surat_selesai_magang')->store('surat_selesai', 'public');
        }

        $magang->update([
            'jadwal_opsi_1' => $request->jadwal_opsi_1,
            'jadwal_opsi_2' => $request->jadwal_opsi_2,
            'jadwal_opsi_3' => $request->jadwal_opsi_3,
            'jadwal_opsi_4' => $request->jadwal_opsi_4,
            'jadwal_opsi_5' => $request->jadwal_opsi_5,
            'jadwal_opsi_6' => $request->jadwal_opsi_6,
            'jadwal_opsi_7' => $request->jadwal_opsi_7,
            'status_jadwal_skp' => 'menunggu',
            'keterangan_tolak_jadwal' => null,
            'surat_selesai_magang' => $magang->surat_selesai_magang,
        ]);

        return redirect()->back()->with('success', '7 Opsi jadwal (1 minggu) dan Surat Selesai Magang berhasil diajukan. Silakan tunggu persetujuan dari Dosen Pembimbing.');
    }

    public function riwayatMagang()
    {
        $riwayat_magang = $this->riwayatQuery(Auth::user()->mahasiswa->id)->get();

        return view('mahasiswa.riwayat_magang.index', compact('riwayat_magang'));
    }

    public function editMagang($id)
    {
        $magang = Magang::with(['perusahaan', 'dosen'])
            ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
            ->findOrFail($id);

        $daftar_dosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('mahasiswa.riwayat_magang.edit', compact('magang', 'daftar_dosen'));
    }

    public function updateMagang(Request $request, $id)
    {
        $magang = Magang::where('mahasiswa_id', Auth::user()->mahasiswa->id)->findOrFail($id);

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tema_magang' => 'required|string|max:255',
            'status_gaji' => 'required|in:paid,unpaid',
            'dosen_id' => 'required|exists:users,id',
        ]);

        $magang->update($request->only('tanggal_mulai', 'tanggal_selesai', 'tema_magang', 'status_gaji', 'dosen_id'));

        return redirect()->route('mahasiswa.riwayat-magang.index')
            ->with('success', 'Data magang berhasil diperbarui.');
    }

    private function riwayatQuery(int $mahasiswaId)
    {
        return Magang::with(['perusahaan', 'dosen'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->orderBy('created_at', 'desc');
    }

    private function validateJadwalRequest(Request $request, Magang $magang): void
    {
        $rules = [];
        $messages = [
            'after' => 'Jadwal tidak boleh di masa lalu atau hari ini (harus minimal besok).',
        ];

        for ($i = 1; $i <= 7; $i++) {
            $different = collect(range(1, $i - 1))
                ->map(fn ($j) => "different:jadwal_opsi_{$j}")
                ->implode('|');

            $rules["jadwal_opsi_{$i}"] = trim("required|date|after:today|{$different}", '|');

            if ($i > 1) {
                $messages["jadwal_opsi_{$i}.different"] = "Opsi {$i} harus berbeda dengan opsi sebelumnya.";
            }
        }

        $rules['surat_selesai_magang'] = $magang->surat_selesai_magang
            ? 'nullable|mimes:pdf|max:2048'
            : 'required|mimes:pdf|max:2048';

        $request->validate($rules, $messages);
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
