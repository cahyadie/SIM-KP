<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use App\Models\Perusahaan;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListPerusahaanController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role ?? 'mahasiswa';

        $query = Perusahaan::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount(['magangs as total_alumni_count' => fn ($q) => $q->diterima()])
            ->withExists(['magangs as has_paid' => fn ($q) => $q->diterima()->where('status_gaji', 'paid')])
            ->withExists(['magangs as has_unpaid' => fn ($q) => $q->diterima()->where('status_gaji', 'unpaid')]);

        if ($keyword = $request->input('cari')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_perusahaan', 'like', "%{$keyword}%")
                    ->orWhere('kategori_industri', 'like', "%{$keyword}%")
                    ->orWhere('alamat', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_industri', $request->kategori);
        }

        if ($request->filled('tipe')) {
            $query->when($request->tipe === 'paid', fn ($q) => $q->whereHas('magangs', fn ($m) => $m->diterima()->where('status_gaji', 'paid')))
                ->when($request->tipe === 'unpaid', fn ($q) => $q
                    ->whereHas('magangs', fn ($m) => $m->diterima()->where('status_gaji', 'unpaid'))
                    ->whereDoesntHave('magangs', fn ($m) => $m->diterima()->where('status_gaji', 'paid')));
        }

        $query->when($request->query('sort') === 'rating_tinggi', fn ($q) => $q->orderBy('reviews_avg_rating', 'desc'))
            ->when($request->query('sort') === 'rating_terendah', fn ($q) => $q->orderBy('reviews_avg_rating', 'asc'))
            ->when($request->query('sort') === 'mhs_terbanyak', fn ($q) => $q->orderBy('total_alumni_count', 'desc'))
            ->when($request->query('sort') === 'mhs_tersedikit', fn ($q) => $q->orderBy('total_alumni_count', 'asc'))
            ->when(! in_array($request->query('sort'), ['rating_tinggi', 'rating_terendah', 'mhs_terbanyak', 'mhs_tersedikit']), fn ($q) => $q->latest());

        $perusahaans = $query->paginate(10);

        return view('shared.perusahaan.index', compact('perusahaans', 'role'));
    }

    public function show(Request $request, $id)
    {
        $sort = $request->query('sort', 'terbaru');

        $perusahaan = Perusahaan::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withExists(['magangs as has_paid' => fn ($q) => $q->diterima()->where('status_gaji', 'paid')])
            ->withExists(['magangs as has_unpaid' => fn ($q) => $q->diterima()->where('status_gaji', 'unpaid')])
            ->with([
                'magangs',
                'reviews' => function ($query) use ($sort) {
                    match ($sort) {
                        'tertinggi' => $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc'),
                        'terendah' => $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc'),
                        default => $query->latest(),
                    };
                },
                'reviews.mahasiswa.user',
            ])->findOrFail($id);

        $bisa_review = false;
        if (Auth::user()->role === 'mahasiswa') {
            $bisa_review = Magang::where('mahasiswa_id', Auth::user()->mahasiswa?->id ?? 0)
                ->where('perusahaan_id', $id)
                ->diterima()
                ->exists();
        }

        return view('shared.perusahaan.show', compact('perusahaan', 'bisa_review', 'sort'));
    }

    public function storeReview(Request $request, $id)
    {
        abort_unless($mahasiswaId = Auth::user()->mahasiswa?->id, 403);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:500',
        ]);

        Review::create([
            'perusahaan_id' => $id,
            'mahasiswa_id' => $mahasiswaId,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function destroyReview($id)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        Review::findOrFail($id)->delete();

        return back()->with('success', 'Review berhasil dihapus.');
    }

    public function destroyMagang($id)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        Magang::findOrFail($id)->delete();

        return back()->with('success', 'Data riwayat magang berhasil dihapus.');
    }
}
