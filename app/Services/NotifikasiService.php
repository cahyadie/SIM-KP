<?php

namespace App\Services;

use App\Models\Magang;
use App\Models\User;
use App\Notifications\MagangNotification;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke dosen, opsional dengan proteksi anti-duplikat.
     */
    public static function kirim(
        User $dosen,
        string $jenis,
        string $pesan,
        string $url,
        string $icon = 'bi-bell',
        ?int $magangId = null,
        bool $dedup = false,
    ): void {
        if (! $dosen || $dosen->role !== 'dosen') {
            return;
        }

        if ($dedup && self::sudahAda($dosen, $jenis, $magangId)) {
            return;
        }

        $dosen->notify(new MagangNotification($jenis, $pesan, $url, $icon, $magangId));
    }

    /**
     * Kirim notifikasi "selesai magang" untuk semua dosen pembimbing yang
     * masa magangnya sudah lewat. Dipakai oleh scheduler dan fallback
     * dashboard dosen. Mengembalikan jumlah notifikasi yang dikirim.
     */
    public static function kirimSelesaiMagang(): int
    {
        $magangs = Magang::with(['dosen', 'mahasiswa.user'])
            ->diterima()
            ->whereDate('tanggal_selesai', '<', now()->toDateString())
            ->get();

        $jumlah = 0;

        foreach ($magangs as $magang) {
            $dosen = $magang->dosen;

            if (! $dosen) {
                continue;
            }

            $pesan = 'Mahasiswa '.$magang->mahasiswa->user->name.' telah menyelesaikan masa magang';

            $sebelumnya = $dosen->notifications()
                ->where('type', MagangNotification::class)
                ->whereJsonContains('data->magang_id', $magang->id)
                ->whereJsonContains('data->jenis', 'selesai_magang')
                ->exists();

            if ($sebelumnya) {
                continue;
            }

            $dosen->notify(new MagangNotification(
                'selesai_magang',
                $pesan,
                route('dosen.bimbingan.detail', $magang->id),
                'bi-flag-fill',
                $magang->id,
            ));

            $jumlah++;
        }

        return $jumlah;
    }

    private static function sudahAda(User $dosen, string $jenis, ?int $magangId): bool
    {
        if ($magangId === null) {
            return false;
        }

        return $dosen->notifications()
            ->where('type', MagangNotification::class)
            ->whereJsonContains('data->magang_id', $magangId)
            ->whereJsonContains('data->jenis', $jenis)
            ->exists();
    }
}
