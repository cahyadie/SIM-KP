<?php

namespace App\Exports;

use App\Models\Magang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RiwayatMagangExport implements FromView, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;
    protected $status;

    public function __construct($bulan, $tahun, $status = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->status = $status;
    }

    public function view(): View
    {
        $hariIni = \Carbon\Carbon::now();
        $query = Magang::with(['mahasiswa.user', 'perusahaan', 'dosen'])
            ->where('status_validasi', 'diterima');

        if (!empty($this->bulan)) { $query->whereMonth('tanggal_mulai', $this->bulan); }
        if (!empty($this->tahun)) { $query->whereYear('tanggal_mulai', $this->tahun); }
        
        if (!empty($this->status)) {
            if ($this->status == 'selesai') {
                $query->where('status_skp', 'sudah');
            } elseif ($this->status == 'seminar') {
                $query->where('status_skp', 'belum')->where('tanggal_selesai', '<', $hariIni);
            } elseif ($this->status == 'aktif') {
                $query->where('status_skp', 'belum')->where('tanggal_selesai', '>=', $hariIni);
            }
        }

        $riwayat = $query->orderBy('created_at', 'desc')->get();

        // Kita gunakan view khusus untuk Excel dan PDF secara seragam
        return view('riwayat_magang.export_table', [
            'riwayat' => $riwayat
        ]);
    }
}