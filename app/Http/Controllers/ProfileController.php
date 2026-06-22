<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa ?? new Mahasiswa();

        return view('mahasiswa.profile', compact('user', 'mahasiswa'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi Input
        $request->validate([
            'nim' => 'required|numeric|unique:mahasiswas,nim,' . ($user->mahasiswa->id ?? 'null'),
            'angkatan' => 'required|numeric|digits:4',
            'no_hp' => 'required|numeric',
        ]);

        // Simpan atau Update Data Mahasiswa
        Mahasiswa::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nim' => $request->nim,
                'angkatan' => $request->angkatan,
                'prodi' => 'Teknologi Informasi', // Auto set
                'no_hp' => $request->no_hp 
            ]
        );
        
        // Opsional: Update kolom bantu di tabel users jika ada
        // $user->update(['nomor_induk' => $request->nim]);

        // Redirect ke dashboard dengan pesan instruksi selanjutnya
        return redirect()->route('mahasiswa.dashboard')->with('success', 'Data profil berhasil dilengkapi! Silakan lanjutkan ke pendaftaran magang.');
    }
}