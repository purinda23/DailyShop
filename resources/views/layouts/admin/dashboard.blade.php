@extends('layouts.app')

@section('content')
<style>
    /* Mengunci halaman utama agar scrollbar tidak berlipat ganda */
    body {
        overflow-x: hidden;
        background-color: #f8f9fa; /* Latar belakang abu-abu terang yang halus */
    }
    .main-content-wrapper {
        margin-left: 280px; /* Ruang pas untuk lebar fixed sidebar */
        width: calc(100% - 280px);
        min-height: 100vh;
        transition: all 0.3s ease;
    }
    @media (max-width: 768px) {
        .main-content-wrapper {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<div class="container-fluid px-0">
    <div class="position-relative min-vh-100">
        
        @include('layouts.admin.sidebar')

        <div class="p-4 p-md-5 main-content-wrapper">
            
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Selamat Datang, Admin DailyShop! 👋</h3>
                    <p class="text-muted mb-0">Berikut adalah performa dan statistik tokomu hari ini.</p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 border-start border-primary border-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Pendapatan</h6>
                                <h4 class="fw-bold mb-0 text-dark">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h4>
                            </div>
                            <div class="fs-2 text-primary opacity-50"><i class="bi bi-wallet2"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 border-start border-warning border-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Pesanan Baru</h6>
                                <h4 class="fw-bold mb-0 text-dark">{{ $pesanan_masuk }} Pesanan</h4>
                            </div>
                            <div class="fs-2 text-warning opacity-50"><i class="bi bi-cart-fill"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 border-start border-success border-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Produk</h6>
                                <h4 class="fw-bold mb-0 text-dark">{{ $total_produk }} Item</h4>
                            </div>
                            <div class="fs-2 text-success opacity-50"><i class="bi bi-box-seam-fill"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 border-start border-info border-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-muted small text-uppercase fw-bold mb-1">Pelanggan Aktif</h6>
                                <h4 class="fw-bold mb-0 text-dark">{{ $total_pelanggan }} User</h4>
                            </div>
                            <div class="fs-2 text-info opacity-50"><i class="bi bi-people-fill"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up text-primary me-2"></i>Grafik Tren Pendapatan Penjualan (30 Hari Terakhir)</h5>
                        <div style="position: relative; height:280px; width:100%">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-secondary me-2"></i>Aktivitas Transaksi Terbaru</h5>
                    <a href="/admin/pesanan" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="p-3">Kode Transaksi</th>
                                    <th class="p-3">Nama Pembeli</th>
                                    <th class="p-3">Total Harga</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksi_terbaru as $trx)
                                <tr>
                                    <td class="p-3 fw-bold text-secondary">{{ $trx->kode_transaksi ?? '-' }}</td>
                                    <td class="p-3">{{ $trx->nama_pembeli ?? 'Pembeli' }}</td>
                                    <td class="p-3 fw-bold text-primary">Rp {{ number_format($trx->total_harga ?? 0, 0, ',', '.') }}</td>
                                    <td class="p-3">
                                        <span class="badge rounded-pill px-3 py-2
                                            {{ ($trx->status ?? '') == 'proses' || ($trx->status ?? '') == 'selesai' ? 'bg-success-subtle text-success' : '' }}
                                            {{ ($trx->status ?? '') == 'dikirim' ? 'bg-info-subtle text-info' : '' }}
                                            {{ ($trx->status ?? '') == 'pending' ? 'bg-warning-subtle text-warning' : '' }}
                                            {{ ($trx->status ?? '') == 'batal' ? 'bg-danger-subtle text-danger' : '' }}">
                                    
                                            {{ ucfirst($trx->status ?? 'Pending') }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-muted small">
                                        {{ isset($trx->created_at) && $trx->created_at ? date('d M Y, H:i', strtotime($trx->created_at)) : '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4 text-muted">Belum ada aktivitas transaksi masuk saat ini.</td>
                                </tr>
                                @endempty
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        const labelHari = JSON.parse('{!! json_encode($grafik_tanggal ?? []) !!}');
        const dataPendapatan = JSON.parse('{!! json_encode($grafik_pendapatan ?? []) !!}');

        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelHari,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: dataPendapatan,
                    borderColor: '#00a8e8',
                    backgroundColor: 'rgba(0, 168, 232, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#00a8e8',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false } 
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: { 
                        grid: { display: false } 
                    }
                }
            }
        });
    });
</script>
@endsection