@extends('layouts.app')

@section('content')
<div class="d-flex">

    @include('layouts.admin.sidebar')

    <div class="flex-grow-1" style="margin-left:280px; min-height:100vh; background:#f8fafc;">
        <div class="container-fluid p-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="bi bi-cart-check text-primary me-2"></i>
                            Daftar Pesanan Masuk
                        </h4>
                        <p class="text-muted mb-0">
                            Daftar transaksi pesanan kebutuhan harian dari seluruh pelanggan DailyShop.
                        </p>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Tanggal Masuk</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanan_masuk as $pesanan)
                            <tr>
                                <td class="fw-bold">
                                    #{{ $pesanan->id }}
                                </td>
                                <td>
                                    User ID: {{ $pesanan->user_id }}
                                </td>
                                <td class="fw-bold text-primary">
                                    Rp {{ number_format($pesanan->total_harga ?? $pesanan->total, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 bg-warning-subtle text-warning">
                                        {{ $pesanan->status }}
                                    </span>
                                </td>
                                <td>
                                    {{ date('d M Y, H:i', strtotime($pesanan->created_at)) }}
                                </td>
                                <td class="text-center">
                                    <a href="/admin/pesanan/{{ $pesanan->id }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                        Detail / Proses
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Belum ada pesanan masuk dari pelanggan saat ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection