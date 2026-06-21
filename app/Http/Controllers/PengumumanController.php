<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    // =========================================================================
    // A. BAGIAN ADMIN (Kelola Pengumuman CRUD)
    // =========================================================================

    public function index()
    {
        $pengumuman = Pengumuman::latest()->paginate(10);
        return view('admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'            => 'required',
            'deskripsi'        => 'required',
            'link_pendaftaran' => 'required|url'
        ]);

        Pengumuman::create($request->all());

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman lowongan berhasil ditambahkan!');
    }

    public function showAdmin($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return view('admin.pengumuman.show', compact('pengumuman'));
    }

    public function edit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul'            => 'required',
            'deskripsi'        => 'required',
            'link_pendaftaran' => 'required|url'
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update($request->all());

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pengumuman::destroy($id);
        return back()->with('success', 'Pengumuman berhasil dihapus!');
    }


    // =========================================================================
    // B. BAGIAN PUBLIK / MAHASISWA (Output Info Lowongan)
    // =========================================================================

    public function lowongan(Request $request)
    {
        $query = Pengumuman::query();

        // Filter Pencarian Teks (Berdasarkan Judul, Deskripsi, atau Lokasi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        // Filter Angkatan
        if ($request->filled('angkatan')) {
            $query->where('target_angkatan', 'like', "%{$request->angkatan}%");
        }

        // Filter Tipe Pendapatan (Paid / Unpaid) berdasarkan kolom info_gaji
        if ($request->filled('tipe_pendapatan')) {
            if ($request->tipe_pendapatan == 'paid') {
                // Jika Paid, cari yang info_gaji-nya TIDAK null dan TIDAK kosong
                $query->whereNotNull('info_gaji')->where('info_gaji', '!=', '');
            } else if ($request->tipe_pendapatan == 'unpaid') {
                // Jika Unpaid, cari yang info_gaji-nya null ATAU kosong
                $query->where(function($q) {
                    $q->whereNull('info_gaji')->orWhere('info_gaji', '');
                });
            }
        }

        // Pengurutan (Sorting)
        if ($request->sort == 'terlama') {
            $query->oldest();
        } else {
            $query->latest(); // Default: Terbaru (teratas)
        }

        // Ubah paginate menjadi 10 agar proporsional untuk list memanjang (vertical)
        $lowongan = $query->paginate(10); 
        
        return view('admin.pengumuman.lowongan', compact('lowongan'));
    }

    public function showLowongan($id)
    {
        $lowongan = Pengumuman::findOrFail($id);
        $pengumuman = $lowongan;
        return view('admin.pengumuman.show', compact('lowongan', 'pengumuman'));
    }
}