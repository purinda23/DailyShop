@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/pesanan" class="btn btn-outline-secondary rounded-circle me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="fw-bold mb-0">Detail Transaksi Pelanggan</h3>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Barang yang Harus Dikirim</h5>
                </div>
                <div class="card-body">
                    @foreach($items as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="ms-1 flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ $item->nama_produk }}</h6>
                            <small class="text-muted">{{ $item->jumlah }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-between mt-3">
                        <h5 class="fw-bold">Total Pendapatan</h5>
                        <h5 class="fw-bold text-success">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Update Status Pesanan</h5>
                    <form action="/admin/pesanan/update/{{ $transaksi->id }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <select name="status" class="form-select border-0">
                                <option value="proses" {{ $transaksi->status == 'proses' || $transaksi->status == 'Pembayaran Berhasil' ? 'selected' : '' }}>Pembayaran Berhasil (Proses)</option>
                                <option value="dikirim" {{ $transaksi->status == 'dikirim' || $transaksi->status == 'Pesanan Dikirim' ? 'selected' : '' }}>Pesanan Dikirim</option>
                                <option value="selesai" {{ $transaksi->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="batal" {{ $transaksi->status == 'batal' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-light w-100 fw-bold rounded-pill">Update Status</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold text-muted small mb-3 text-uppercase">Informasi Pengiriman</h6>
                    <p class="mb-1 fw-bold">{{ $transaksi->nama_pembeli }}</p>
                    <p class="text-muted small mb-3">{{ $transaksi->email }}</p>
                    <hr>
                    <p class="text-dark small"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> {{ $transaksi->alamat_pengiriman }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection