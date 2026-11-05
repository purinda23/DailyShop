@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="/pesanan" class="btn btn-outline-secondary rounded-circle me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h3 class="fw-bold mb-0">Detail Pesanan</h3>
    </div>

    {{-- Alert Flash Message Sukses/Gagal jika ada aksi pembatalan --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Produk yang Dibeli</h5>
                </div>
                <div class="card-body">
                    @foreach($detail as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        
                        {{-- PERBAIKAN: Menyesuaikan pemanggilan gambar dari folder public/images --}}
                        @php
                            $pathGambar = $item->gambar;
                            // Jika di database sudah ada kata 'images/', langsung panggil. Jika belum, kita tambahkan 'images/' di depannya.
                            if (\Illuminate\Support\Str::contains($pathGambar, 'images/')) {
                                $srcGambar = asset($pathGambar);
                            } else {
                                $srcGambar = asset('images/' . $pathGambar);
                            }
                        @endphp

                        <img src="{{ $srcGambar }}" class="rounded-3" width="80" height="80" style="object-fit: cover;" alt="{{ $item->nama_produk }}">
                        
                        <div class="ms-3 flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ $item->nama_produk }}</h6>
                            <small class="text-muted">{{ $item->jumlah }} barang x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-light-blue">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Ringkasan Pembayaran</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span>
                        <span class="badge {{ $transaksi->status === 'batal' ? 'bg-danger' : ($transaksi->status === 'pending' || $transaksi->status === 'proses' ? 'bg-warning text-dark' : 'bg-success') }} rounded-pill">
                            {{ $transaksi->status }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Metode</span>
                        <span>{{ $transaksi->metode_pembayaran ?? 'Transfer Bank' }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-0">
                        <span class="fw-bold">Total Belanja</span>
                        <span class="fw-bold text-light-blue fs-5">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Alamat Pengiriman</h5>
                    <p class="mb-1 fw-bold">{{ Auth::user()->name }}</p>
                    <p class="text-muted small mb-0">{{ $transaksi->alamat_pengiriman }}</p>
                </div>
            </div>

            <!-- {{-- Tombol Batalkan Pesanan --}}
            @if($transaksi->status === 'pending' || $transaksi->status === 'proses')
                <form action="{{ route('transaksi.batal', $transaksi->id) }}" method="POST" 
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.');"
                      class="d-grid">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-4 py-2 fw-bold">
                        <i class="bi bi-x-circle me-1"></i> Batalkan Pesanan
                    </button>
                </form>
            @endif -->
        </div>
    </div>
</div>
@endsection