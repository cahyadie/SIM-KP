<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pantauan Lama SKP</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .header h3 { margin: 0 0 5px 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 0; font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #bdc3c7; padding: 8px 10px; text-align: left; }
        th { background-color: #ecf0f1; font-weight: bold; color: #2c3e50; text-align: center; }
        .text-center { text-align: center; }
        .text-danger { color: #e74c3c; font-weight: bold; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <div class="header">
        <h3>Laporan Pantauan Seminar Praktek Magang (SKP)</h3>
        <p>Daftar Mahasiswa dengan Keterlambatan Mendaftar SKP > 30 Hari</p>
        <p style="font-size: 10px; margin-top: 5px;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">NIM</th>
                <th style="width: 20%;">Nama Mahasiswa</th>
                <th style="width: 20%;">Dosen Pembimbing</th>
                <th style="width: 18%;">Tempat Magang</th>
                <th style="width: 13%;">Tgl Selesai</th>
                <th style="width: 12%;">Keterlambatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswaOverdue as $index => $mhs)
                @php
                    $magang = $mhs->magangs->where('status_validasi', 'diterima')->sortByDesc('tanggal_selesai')->first();
                    $hariTerlewat = floor(\Carbon\Carbon::parse($magang->tanggal_selesai)->diffInDays(\Carbon\Carbon::now()));
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $mhs->nim }}</td>
                    <td>{{ $mhs->user->name ?? 'Tidak diketahui' }}</td>
                    <td>{{ $magang->dosen->name ?? 'Belum Ditentukan' }}</td>
                    <td>{{ $magang->perusahaan->nama_perusahaan ?? 'Tidak diketahui' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($magang->tanggal_selesai)->format('d/m/Y') }}</td>
                    <td class="text-center text-danger">{{ $hariTerlewat }} Hari</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">
                        Saat ini tidak ada mahasiswa yang terlambat mendaftar SKP lebih dari 30 hari.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>