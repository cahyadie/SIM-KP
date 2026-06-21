@extends('layouts.app')

@section('title', $title)

@section('styles')
<style>
    /* View-Specific Styles */
    .text-super-muted { 
        color: var(--text-muted); 
        font-size: 0.8rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 0.5px; 
    }
    .chart-container {
        position: relative;
        height: 50vh; 
        width: 100%;
    }
    .bg-danger-soft { background-color: #FEE2E2; color: #991B1B; }
</style>
@endsection

@section('content')
<div class="container-fluid content-wrapper">
    
    {{-- Header dengan Tombol Back --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('kaprodi.dashboard') }}" class="btn btn-outline-secondary rounded-circle me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px; padding: 0;">
            <i class="bi bi-arrow-left fs-5" style="margin-left: 0 !important; margin-right: 0 !important;"></i>
        </a>
        {{-- Detail Total Angka --}}
        <div class="text-end ms-auto">
            <span class="text-super-muted d-block">Total Keseluruhan</span>
            <h4 class="fw-bold text-primary mb-0">{{ number_format($total_data) }} <span class="fs-6 text-muted fw-normal">Data</span></h4>
        </div>
    </div>

    {{-- Bagian Atas: Tombol Aksi & Indikator Filter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            @if(request('tahun'))
                <span class="badge bg-info text-dark shadow-sm">
                    Menampilkan Tahun: {{ request('tahun') }}
                </span>
            @endif
        </div>

        <div class="d-flex gap-2">
            {{-- Tombol Filter --}}
            <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel me-1"></i> Filter Data
            </button>
        </div>
    </div>

    {{-- Chart Card --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

    {{-- TABEL LIST MAHASISWA --}}
    @if($list_mahasiswa->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0 text-dark">Rincian Data - {{ $title }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive border-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;" class="ps-4">#</th>
                            <th>NAMA MAHASISWA</th>
                            <th>PERUSAHAAN</th>
                            <th>TGL MULAI</th>
                            <th>TGL SELESAI</th>
                            <th class="text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list_mahasiswa as $index => $magang)
                            <tr>
                                <td class="text-muted ps-4 align-middle">{{ $list_mahasiswa->firstItem() + $loop->index }}</td>
                                <td class="fw-bold align-middle">{{ $magang->mahasiswa->user->name ?? 'N/A' }}</td>
                                <td class="align-middle">
                                    <span class="text-secondary"><i class="bi bi-building me-1"></i> {{ $magang->perusahaan->nama_perusahaan ?? 'N/A' }}</span>
                                </td>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($magang->tanggal_mulai)->translatedFormat('d M Y') }}</td>
                                <td class="fw-bold text-dark align-middle">{{ \Carbon\Carbon::parse($magang->tanggal_selesai)->translatedFormat('d M Y') }}</td>
                                <td class="text-center align-middle">
                                    
                                    {{-- LOGIKA LABEL BERDASARKAN KATEGORI --}}
                                    @if($kategori === 'aktif')
                                        @php 
                                            $sisaHari = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($magang->tanggal_selesai)->startOfDay(), false); 
                                        @endphp
                                        @if($sisaHari <= 7 && $sisaHari >= 0)
                                            <span class="badge bg-warning-soft"><i class="bi bi-exclamation-circle me-1"></i> Sisa {{ $sisaHari }} Hari</span>
                                        @else
                                            <span class="badge bg-success-soft"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                                        @endif
                                        
                                    @elseif($kategori === 'lulus')
                                        <span class="badge bg-success-soft"><i class="bi bi-mortarboard-fill me-1"></i> Lulus SKP</span>
                                        
                                    @elseif($kategori === 'belum-skp')
                                        <span class="badge bg-danger-soft"><i class="bi bi-clock-history me-1"></i> Belum SKP</span>
                                        
                                    @elseif($kategori === 'gaji')
                                        @if($magang->status_gaji == 'paid')
                                            <span class="badge bg-success-soft"><i class="bi bi-cash-stack me-1"></i> Paid</span>
                                        @else
                                            <span class="badge bg-secondary text-white"><i class="bi bi-dash-circle me-1"></i> Unpaid</span>
                                        @endif
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($list_mahasiswa->hasPages())
                <div class="card-footer bg-white border-0 pt-3 pb-3 px-4 d-flex justify-content-end">
                    {{ $list_mahasiswa->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <i class="bi bi-folder-x display-4 text-muted opacity-25"></i>
        <p class="text-muted mt-2">Tidak ada data yang sesuai dengan filter ini.</p>
    </div>
    @endif

    {{-- Modal Filter --}}
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="filterModalLabel">Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ url()->current() }}" method="GET">
                    <div class="modal-body py-4">
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
                            <select name="tahun" class="form-select border-0 shadow-sm" style="background-color: var(--bg-body);">
                                @php $currentYear = date('Y'); @endphp
                                <option value="">Semua Tahun</option>
                                @for($y = $currentYear + 1; $y >= $currentYear - 3; $y--)
                                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 d-flex flex-column gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan Filter</button>
                        @if(request('tahun'))
                            <a href="{{ url()->current() }}" class="btn btn-light btn-sm w-100 text-danger">Reset Filter</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('myChart');
        const chartType = '{{ $chartType }}';
        
        const labels = @json($chartLabels);
        const dataValues = @json($chartValues);

        let bgColors = 'rgba(0, 75, 35, 0.2)'; 
        let borderColors = 'rgba(0, 75, 35, 1)'; 

        if (chartType === 'doughnut') {
            bgColors = ['rgba(0, 75, 35, 0.8)', 'rgba(245, 158, 11, 0.8)']; 
            borderColors = ['#fff', '#fff'];
        }

        new Chart(ctx, {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Data',
                    data: dataValues,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: chartType === 'doughnut' ? 3 : 2,
                    fill: chartType === 'line', 
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: chartType === 'doughnut', 
                        position: 'bottom',
                        labels: { font: { family: "'Inter', sans-serif" } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)', 
                        titleFont: { family: "'Inter', sans-serif", size: 13 },
                        bodyFont: { family: "'Inter', sans-serif", size: 14 },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: chartType !== 'doughnut' ? {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" } },
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        grid: { display: false }, 
                        ticks: { font: { family: "'Inter', sans-serif" } }
                    }
                } : {}
            }
        });
    });
</script>
@endsection