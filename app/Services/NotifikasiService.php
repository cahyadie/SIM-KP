<?php

namespace App\Services;

use App\Models\Magang;
use App\Models\User;
use App\Notifications\MagangNotification;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke dosen/kaprodi, opsional dengan proteksi anti-duplikat.
     * Mengembalikan true jika notifikasi benar-benar dikirim.
     */
    public static function kirim(
        User $user,
        string $jenis,
        string $pesan,
        string $url,
        string $icon = 'bi-bell',
        ?int $magangId = null,
        bool $dedup = false,
    ): bool {
        if (! $user || ! in_array($user->role, ['dosen', 'kaprodi'])) {
            return false;
        }

        if ($dedup && self::sudahAda($user, $jenis, $magangId)) {
            return false;
        }

        $user->notify(new MagangNotification($jenis, $pesan, $url, $icon, $magangId));

        return true;
    }

    /**
     * Kirim notifikasi ke dosen pembimbing dan semua user berperan kaprodi,
     * masing-masing dengan URL target sesuai rolenya.
     */
    public static function kirimKeSemua(
        Magang $magang,
        string $jenis,
        string $pesan,
        string $icon = 'bi-bell',
        bool $dedup = false,
    ): int {
        $jumlah = 0;

        if ($magang->dosen) {
            $jumlah += self::kirim(
                $magang->dosen,
                $jenis,
                $pesan,
                self::targetUrl($magang->dosen, $jenis, $magang),
                $icon,
                $magang->id,
                $dedup,
            ) ? 1 : 0;
        }

        foreach (User::where('role', 'kaprodi')->get() as $kaprodi) {
            $jumlah += self::kirim(
                $kaprodi,
                $jenis,
                $pesan,
                self::targetUrl($kaprodi, $jenis, $magang),
                $icon,
                $magang->id,
                $dedup,
            ) ? 1 : 0;
        }

        return $jumlah;
    }

    /**
     * Kirim notifikasi "selesai magang" untuk semua dosen pembimbing dan
     * kaprodi yang masa magangnya sudah lewat. Dipakai oleh scheduler dan
     * fallback dashboard. Mengembalikan jumlah notifikasi yang dikirim.
     */
    public static function kirimSelesaiMagang(): int
    {
        $magangs = Magang::with(['dosen', 'mahasiswa.user'])
            ->diterima()
            ->whereDate('tanggal_selesai', '<', now()->toDateString())
            ->get();

        $jumlah = 0;

        foreach ($magangs as $magang) {
            $pesan = 'Mahasiswa '.$magang->mahasiswa->user->name.' telah menyelesaikan masa magang';

            $jumlah += self::kirimKeSemua($magang, 'selesai_magang', $pesan, 'bi-flag-fill', true);
        }

        return $jumlah;
    }

    private static function sudahAda(User $user, string $jenis, ?int $magangId): bool
    {
        if ($magangId === null) {
            return false;
        }

        return $user->notifications()
            ->where('type', MagangNotification::class)
            ->whereJsonContains('data->magang_id', $magangId)
            ->whereJsonContains('data->jenis', $jenis)
            ->exists();
    }

    private static function targetUrl(User $user, string $jenis, Magang $magang): string
    {
        if ($user->role === 'kaprodi') {
            return match ($jenis) {
                'mulai_magang', 'selesai_magang' => route('kaprodi.riwayat-magang.show', $magang->id),
                'logbook' => route('kaprodi.monitoring.show', $magang->id),
                'ajukan_jadwal' => route('kaprodi.skp.show', $magang->id),
                'selesai_seminar' => route('kaprodi.skp'),
                default => route('kaprodi.dashboard'),
            };
        }

        return match ($jenis) {
            'mulai_magang', 'selesai_magang' => route('dosen.bimbingan.detail', $magang->id),
            'logbook' => route('dosen.bimbingan.logbook', $magang->id),
            'ajukan_jadwal' => route('dosen.skp.respon', $magang->id),
            'selesai_seminar' => route('dosen.riwayat-magang.index'),
            default => route('dosen.dashboard'),
        };
    }
}
