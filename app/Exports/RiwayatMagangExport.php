<?php

namespace App\Exports;

use App\Models\Magang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RiwayatMagangExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected $bulan = null,
        protected $tahun = null,
        protected $status = null,
    ) {}

    public function view(): View
    {
        $riwayat = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->diterima()
            ->when($this->bulan, fn ($query) => $query->whereMonth('tanggal_mulai', $this->bulan))
            ->when($this->tahun, fn ($query) => $query->whereYear('tanggal_mulai', $this->tahun))
            ->when($this->status, function ($query) {
                match ($this->status) {
                    'selesai' => $query->skpSudah(),
                    'seminar' => $query->skpBelum()->where('tanggal_selesai', '<', now()),
                    'aktif' => $query->skpBelum()->where('tanggal_selesai', '>=', now()),
                    default => null,
                };
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('riwayat_magang.export_table', ['riwayat' => $riwayat]);
    }
}
