<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: bold; font-size: 14px;">
                LAPORAN RIWAYAT MAGANG MAHASISWA
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">
                Bulan: {{ request('bulan') ?: 'Semua' }} | Tahun: {{ request('tahun') ?: 'Semua' }} | Status: {{ request('status') ? ucfirst(request('status')) : 'Semua' }}
            </th>
        </tr>
        <tr>
            <th colspan="7"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">No</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">Nama Mahasiswa</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">NIM</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">Dosen Pembimbing</th>
            {{-- PERBAIKAN: Mengganti "&" menjadi "dan" agar tidak error DOM Document --}}
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">Perusahaan dan Tema</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">Periode Magang</th>
            <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #f2f2f2;">Status Saat Ini</th>
        </tr>
    </thead>
    <tbody>
        @foreach($riwayat as $index => $p)
            @php
                $tglSelesai = \Carbon\Carbon::parse($p->tanggal_selesai)->endOfDay();
                if ($p->status_skp == 'sudah') {
                    $statusText = 'Selesai (Lulus SKP)';
                } elseif ($tglSelesai->isPast()) {
                    $statusText = 'Belum SKP';
                } else {
                    $statusText = 'Aktif Magang';
                }
            @endphp
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #000000;">{{ $p->mahasiswa->user->name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $p->mahasiswa->nim }}</td>
                <td style="border: 1px solid #000000;">{{ $p->dosen->name ?? 'Belum Ditentukan' }}</td>
                {{-- PERBAIKAN: Mengubah <br> menjadi <br/> --}}
                <td style="border: 1px solid #000000;">{{ $p->perusahaan->nama_perusahaan }} <br/> ({{ $p->tema_magang ?? '-' }})</td>
                <td style="border: 1px solid #000000; text-align: center;">
                    {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d/m/Y') }}
                </td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $statusText }}</td>
            </tr>
        @endforeach
    </tbody>
</table>