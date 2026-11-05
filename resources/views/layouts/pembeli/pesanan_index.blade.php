@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4">Riwayat Pesanan Saya</h3>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($pesanan->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <i class="bi bi-bag-x text-muted display-1"></i>
            <p class="mt-3 text-muted">Kamu belum pernah melakukan pemesanan.</p>
            <a href="/" class="btn btn-primary rounded-pill px-4">Mulai Belanja</a>
        </div>
    @else
        @foreach($pesanan as $p)
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <span class="badge bg-light-blue text-white p-2 px-3 rounded-pill">{{ $p->status }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Kode Transaksi</small>
                            <span class="fw-bold">{{ $p->kode_transaksi }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Total Bayar</small>
                            <span class="fw-bold text-light-blue">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                        </div>
                        
                        {{-- KODE PERUBAHAN: Menampung tombol detail dan tombol batal --}}
                        <div class="col-md-3 text-end d-flex justify-content-end align-items-center gap-2">
                            
                            {{-- Jika status pending atau proses, tampilkan Tombol Batalkan Pesanan --}}
                            @if($p->status === 'pending' || $p->status === 'proses')
                                <form action="{{ route('transaksi.batal', $p->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan {{ $p->kode_transaksi }}? Stok akan dikembalikan.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger rounded-pill px-3">
                                        Batalkan
                                    </button>
                                </form>
                            @endif

                            <a href="/pesanan/{{ $p->id }}" class="btn btn-outline-primary rounded-pill px-4">Lihat Detail</a>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection