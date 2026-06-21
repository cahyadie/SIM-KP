<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\Review;
use App\Models\Magang;
use Illuminate\Support\Facades\Auth;

class ListPerusahaanController extends Controller
{
    // 1. Halaman Daftar Semua Perusahaan
    public function index(Request $request)
    {
        $role = Auth::user()->role ?? 'mahasiswa';

        $query = Perusahaan::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount(['magangs as total_alumni_count' => function ($query) {
                $query->where('status_validasi', 'diterima');
            }])
            ->withExists(['magangs as has_paid' => function ($query) {
                $query->where('status_gaji', 'paid')->where('status_validasi', 'diterima');
            }])
            ->withExists(['magangs as has_unpaid' => function ($query) {
                $query->where('status_gaji', 'unpaid')->where('status_validasi', 'diterima');
            }]);

        // Fitur Pencarian
        if ($request->has('cari') && $request->cari != '') {
            $keyword = $request->cari;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_perusahaan', 'like', "%{$keyword}%")
                  ->orWhere('kategori_industri', 'like', "%{$keyword}%")
                  ->orWhere('alamat', 'like', "%{$keyword}%");
            });
        }

        // Fitur Filter Kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_industri', $request->kategori);
        }

        // Fitur Filter Tipe
        if ($request->has('tipe') && $request->tipe != '') {
            if ($request->tipe === 'paid') {
                $query->whereHas('magangs', function ($q) {
                    $q->where('status_gaji', 'paid')->where('status_validasi', 'diterima');
                });
            } elseif ($request->tipe === 'unpaid') {
                $query->whereHas('magangs', function ($q) {
                    $q->where('status_gaji', 'unpaid')->where('status_validasi', 'diterima');
                })->whereDoesntHave('magangs', function ($q) {
                    $q->where('status_gaji', 'paid')->where('status_validasi', 'diterima');
                });
            }
        }

        // Fitur Sortir Berdasarkan Rating
        $sort = $request->query('sort');

if ($sort === 'rating_tinggi') {
    $query->orderBy('reviews_avg_rating', 'desc');
} elseif ($sort === 'rating_terendah') {
    $query->orderBy('reviews_avg_rating', 'asc');
} elseif ($sort === 'mhs_terbanyak') {
    $query->orderBy('total_alumni_count', 'desc');
} elseif ($sort === 'mhs_tersedikit') {
    $query->orderBy('total_alumni_count', 'asc');
} else {
    $query->latest(); // Default
}

        $perusahaans = $query->paginate(10); 

        return view('shared.perusahaan.index', compact('perusahaans', 'role'));
    }

    // 2. Halaman Detail Perusahaan & Review
    public function show(Request $request, $id)
    {
        $sort = $request->query('sort', 'terbaru'); 

        $perusahaan = Perusahaan::withCount('reviews')
            ->withAvg('reviews', 'rating')
            // Cek riwayat paid/unpaid
            ->withExists(['magangs as has_paid' => function ($query) {
                $query->where('status_gaji', 'paid')->where('status_validasi', 'diterima');
            }])
            ->withExists(['magangs as has_unpaid' => function ($query) {
                $query->where('status_gaji', 'unpaid')->where('status_validasi', 'diterima');
            }])
            ->with([
                'magangs',
                'reviews' => function ($query) use ($sort) {
                    if ($sort == 'tertinggi') {
                        $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                    } elseif ($sort == 'terendah') {
                        $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                    } else {
                        $query->latest(); 
                    }
                },
                'reviews.mahasiswa.user'
            ])->findOrFail($id);
        
        $bisa_review = false;
        if (Auth::user()->role == 'mahasiswa') {
            $mhs_id = Auth::user()->mahasiswa->id ?? 0;
            $bisa_review = Magang::where('mahasiswa_id', $mhs_id)
                                 ->where('perusahaan_id', $id)
                                 ->where('status_validasi', 'diterima')
                                 ->exists();
        }

        return view('shared.perusahaan.show', compact('perusahaan', 'bisa_review', 'sort'));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:500'
        ]);

        Review::create([
            'perusahaan_id' => $id,
            'mahasiswa_id' => Auth::user()->mahasiswa->id,
            'rating' => $request->rating,
            'komentar' => $request->komentar
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function destroyReview($id)
    {
        if (Auth::user()->role == 'mahasiswa') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review berhasil dihapus.');
    }

    public function destroyMagang($id)
    {
        if (Auth::user()->role == 'mahasiswa') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $magang = Magang::findOrFail($id);
        $magang->delete();

        return back()->with('success', 'Data riwayat magang berhasil dihapus.');
    }
}