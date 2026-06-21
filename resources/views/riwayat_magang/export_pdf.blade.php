<!DOCTYPE html>
<html>
<head>
    <title>Export PDF Riwayat Magang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
    </style>
</head>
<body>
    <h3 style="text-align: center; margin-bottom: 5px;">LAPORAN RIWAYAT MAGANG MAHASISWA</h3>
    <p style="text-align: center; margin-top: 0; font-size: 12px; color: #555;">
        Filter: Bulan {{ $bulan ?: 'Semua' }} | Tahun {{ $tahun ?: 'Semua' }} | Status {{ $status ? ucfirst($status) : 'Semua' }}
    </p>

    {{-- Memanggil template tabel yang sama persis dengan Excel --}}
    @include('riwayat_magang.export_table', ['riwayat' => $riwayat])
    
</body>
</html>