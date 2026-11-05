@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Pesanan Anda</h4>
            <p class="text-muted small">Pantau terus status pembayaran dan pengiriman barang Anda di DailyShop</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill btn-sm">Lanjut Belanja</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $t)
                    <tr>
                        <td class="fw-bold text-primary">#{{ $t->id }}</td>
                        <td>{{ date('d M Y, H:i', strtotime($t->created_at)) }}</td>
                        <td class="fw-bold">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                        <td>
                            @if($t->status == 'Lunas' || $t->status == 'Berhasil')
                                <span class="badge bg-success rounded-pill px-3 py-2">Diproses 📦</span>
                            @elseif($t->status == 'Pending')
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Menunggu Pembayaran ⏳</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3 py-2">Batal ❌</span>
                            @endif
                        </td>
                        <td>
                            {{-- Tombol Batal jika statusnya masih pending --}}
                            @if($t->status == 'Pending')
                                <form action="{{ route('transaksi.batal', $t->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                        Batalkan
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-bag-x display-4 text-secondary mb-3 d-block"></i>
                            Belum ada riwayat transaksi apa pun.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection