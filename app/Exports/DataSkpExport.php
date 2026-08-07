<?php

namespace App\Exports;

use App\Models\Magang;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DataSkpExport implements FromView, ShouldAutoSize
{
    public function __construct(
        protected $bulan = null,
        protected $tahun = null,
    ) {}

    public function view(): View
    {
        $skpData = Magang::with(['mahasiswa.user', 'perusahaan'])
            ->diterima()
            ->when($this->bulan, fn ($query) => $query->whereMonth('updated_at', $this->bulan))
            ->when($this->tahun, fn ($query) => $query->whereYear('updated_at', $this->tahun))
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.export_skp_pdf', [
            'skpData' => $skpData,
            'bulan' => $this->bulan,
            'tahun' => $this->tahun,
        ]);
    }
}
