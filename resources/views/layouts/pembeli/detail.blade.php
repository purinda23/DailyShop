@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <a href="/" class="btn btn-link text-decoration-none text-muted mb-4 p-0">
                <i class="bi bi-arrow-left"></i> Kembali ke Katalog
            </a>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-6 bg-light d-flex align-items-center justify-content-center">
                        <img src="{{ $produk->gambar ? asset('images/'.$produk->gambar) : 'https://via.placeholder.com/500x500?text=No+Image' }}" 
                             class="img-fluid p-4" 
                             alt="{{ $produk->nama_produk }}"
                             style="max-height: 450px; object-fit: contain;">
                    </div>

                    <div class="col-md-6">
                        <div class="card-body p-5">
                            <span class="badge px-3 py-2 rounded-pill mb-3" style="background-color: #e0f7fa; color: #00a8e8;">
                                {{ $produk->kategori }}
                            </span>
                            
                            <h2 class="fw-bold mb-2">{{ $produk->nama_produk }}</h2>
                            <p class="text-muted small mb-4">Kode Produk: <span class="fw-bold">{{ $produk->kode_produk }}</span></p>

                            <h3 class="fw-bold text-dark mb-4">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </h3>

                            <div class="mb-4">
                                <h6 class="fw-bold">Deskripsi Produk</h6>
                                <p class="text-muted lh-lg">
                                    {{ $produk->deskripsi ?: 'Tidak ada deskripsi untuk produk ini.' }}
                                </p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Stok Tersedia</p>
                                    <h6 class="fw-bold {{ $produk->stok > 0 ? 'text-dark' : 'text-danger' }}">
                                        {{ $produk->stok > 0 ? $produk->stok . ' Unit' : 'Stok Habis' }}
                                    </h6>
                                </div>
                            </div>

                            <form action="/keranjang/tambah" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $produk->id }}">
                                
                                <div class="row g-2">
                                    @if($produk->stok > 0)
                                        <div class="col-3">
                                            <input type="number" name="jumlah" value="1" min="1" max="{{ $produk->stok }}" class="form-control py-3 text-center rounded-3">
                                        </div>
                                        <div class="col-9">
                                            <button type="submit" class="btn btn-primary py-3 fw-bold rounded-3 w-100" style="background: linear-gradient(90deg, #00a8e8, #4fc3f7); border: none;">
                                                + Masukkan Keranjang
                                            </button>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <button type="button" class="btn btn-secondary py-3 fw-bold rounded-3 w-100" disabled>
                                                Maaf, Stok Sedang Habis
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </form>

                            <div class="mt-2 text-center">
                                <button class="btn btn-link btn-sm text-decoration-none text-muted border-0 py-2">
                                    Beli Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($rekomendasi) && count($rekomendasi) > 0)
            <div class="mt-5">
                <h4 class="fw-bold mb-4">Mungkin Anda Juga Suka</h4>
                <div class="row g-4">
                    @foreach($rekomendasi->take(4) as $item)
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 text-center p-3">
                            <a href="/produk/{{ $item->id }}" class="text-decoration-none text-dark">
                                <img src="{{ $item->gambar ? asset('images/'.$item->gambar) : 'https://via.placeholder.com/500x500?text=No+Image' }}" 
                                     class="img-fluid mb-2 rounded-3" style="height: 120px; object-fit: contain;">
                                <h6 class="fw-bold mb-1 small text-truncate">{{ $item->nama_produk }}</h6>
                                <p class="text-primary small mb-0">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection