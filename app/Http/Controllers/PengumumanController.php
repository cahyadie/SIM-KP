<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    // -------------------------------------------------------------------------
    // KELOLA PENGAUMAN (Admin)
    // -------------------------------------------------------------------------
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
        $request->validate($this->rules());

        Pengumuman::create($request->all());

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman lowongan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules());

        Pengumuman::findOrFail($id)->update($request->all());

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pengumuman::destroy($id);

        return back()->with('success', 'Pengumuman berhasil dihapus!');
    }

    // -------------------------------------------------------------------------
    // PUBLIK: INFO LOWONGAN
    // -------------------------------------------------------------------------
    public function lowongan(Request $request)
    {
        $query = Pengumuman::query()
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('judul', 'like', "%{$request->search}%")
                    ->orWhere('deskripsi', 'like', "%{$request->search}%")
                    ->orWhere('lokasi', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('angkatan'), fn ($q) => $q->where('target_angkatan', 'like', "%{$request->angkatan}%"))
            ->when($request->filled('tipe_pendapatan'), function ($q) use ($request) {
                if ($request->tipe_pendapatan === 'paid') {
                    $q->whereNotNull('info_gaji')->where('info_gaji', '!=', '');
                } elseif ($request->tipe_pendapatan === 'unpaid') {
                    $q->whereNull('info_gaji')->orWhere('info_gaji', '');
                }
            });

        $lowongan = $request->sort === 'terlama' ? $query->oldest() : $query->latest();

        return view('admin.pengumuman.lowongan', [
            'lowongan' => $lowongan->paginate(10),
        ]);
    }

    public function showLowongan($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        return view('admin.pengumuman.show', compact('pengumuman'));
    }

    private function rules(): array
    {
        return [
            'judul' => 'required',
            'deskripsi' => 'required',
            'link_pendaftaran' => 'required|url',
            'deadline' => 'nullable|date|after:today',
        ];
    }
}
