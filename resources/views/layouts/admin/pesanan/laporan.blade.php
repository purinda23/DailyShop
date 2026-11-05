@extends('layouts.app')

@section('content')
<style>
    body {
        overflow-x: hidden;
    }
    
    /* PENGUNCI LAYOUT: Memaksa halaman bergeser pas di kanan sidebar fixed */
    .main-admin-content {
        margin-left: 280px;
        width: calc(100% - 280px);
        background-color: #f8fafc;
        min-height: calc(100vh - 83px);
    }

    @media (max-width: 768px) {
        .main-admin-content {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<div class="container-fluid px-0">
    <div class="position-relative">
        
        @include('layouts.admin.sidebar')

        <div class="main-admin-content p-4 p-md-5">
            
            <div class="mb-4">
                <h3 class="fw-bold text-dark mb-1"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Laporan Penjualan</h3>
                <p class="text-muted mb-0">Pantau rangkuman omset pendapatan harian dan statistik transaksi sukses di toko DailyShop.</p>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 border-start border-success border-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Total Omset Masuk</small>
                                <h2 class="fw-bold text-success mb-0 mt-1">Rp {{ number_format((float)$total_pendapatan, 0, ',', '.') }}</h2>
                            </div>
                            <div class="bg-success-subtle p-3 rounded-3 text-success">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 border-start border-primary border-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Transaksi Berhasil</small>
                                <h2 class="fw-bold text-primary mb-0 mt-1">{{ $total_pesanan }} Pesanan</h2>
                            </div>
                            <div class="bg-primary-subtle p-3 rounded-3 text-primary">
                                <i class="bi bi-bag-check fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Log Riwayat Pembukuan</h5>
                    <p class="text-muted small mb-0">Daftar rekapan seluruh status transaksi pembeli untuk pelaporan.</p>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Update</th>
                                    <th>Nilai Kontrak</th>
                                    <th class="text-center">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan_penjualan as $log)
                                <tr>
                                    <td class="fw-bold">#{{ $log->id }}</td>
                                    <td class="fw-semibold text-dark">{{ $log->nama_pembeli }}</td>
                                    <td class="text-muted small">{{ date('d M Y, H:i', strtotime($log->updated_at ?? $log->created_at)) }} WIB</td>
                                    <td class="fw-bold text-primary">Rp {{ number_format((float)($log->total_harga ?? $log->total ?? 0), 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($log->status == 'selesai')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-bold">SELESAI</span>
                                        @elseif($log->status == 'proses' || $log->status == 'dikirim')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1.5 small fw-bold">DIPROSES</span>
                                        @elseif($log->status == 'batal')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1.5 small fw-bold">BATAL</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1.5 small fw-bold">{{ strtoupper($log->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center p-5 text-muted">Belum ada rekapan data penjualan masuk di sistem pembukuan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection