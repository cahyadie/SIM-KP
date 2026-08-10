<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function go(string $id)
    {
        $notif = Auth::user()->notifications()->findOrFail($id);

        $notif->markAsRead();

        $data = (array) $notif->data;

        $fallback = Auth::user()->role === 'kaprodi' ? 'kaprodi.dashboard' : 'dosen.dashboard';

        return redirect($data['url'] ?? route($fallback));
    }

    public function read(string $id)
    {
        Auth::user()->notifications()->findOrFail($id)->markAsRead();

        return back();
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
