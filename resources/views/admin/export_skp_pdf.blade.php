<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dokumen SKP Mahasiswa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; line-height: 1.2; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        .filter { margin-bottom: 15px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #aaa; padding: 8px 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; color: white; font-weight: bold; }
        .bg-success { background-color: #198754; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-info { background-color: #0dcaf0; color: #000; }
        .bg-secondary { background-color: #6c757d; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Status Dokumen SKP Mahasiswa</h2>
        <p>Program Studi Information Technology - Universitas Muhammadiyah Yogyakarta</p>
    </div>

    @if(!empty($bulan) || !empty($tahun))
    <div class="filter">
        Periode Data: {{ !empty($bulan) ? date('F', mktime(0,0,0,$bulan,10)) : 'Semua Bulan' }} {{ !empty($tahun) ? $tahun : date('Y') }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 15%;">NIM</th>
                <th style="width: 25%;">Nama Mahasiswa</th>
                <th style="width: 26%;">Perusahaan</th>
                <th style="width: 15%;">Berkas Seminar</th>
                <th style="width: 15%;">Status SKP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($skpData as $index => $m)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $m->mahasiswa->nim ?? '-' }}</td>
                    <td>{{ $m->mahasiswa->user->name ?? '-' }}</td>
                    <td>{{ $m->perusahaan->nama_perusahaan ?? '-' }}</td>
                    <td class="text-center">
                        @if($m->file_seminar)
                            <span class="badge bg-info">Ada Berkas</span>
                        @else
                            <span class="badge bg-secondary">Belum Upload</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($m->status_skp == 'sudah')
                            <span class="badge bg-success">Selesai ({{ $m->nilai_seminar }})</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center" style="padding: 20px;">Tidak ada data dokumen SKP pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>