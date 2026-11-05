@extends('layouts.app')

@section('content')
<style>
    .hero-section { padding: 60px 0; text-align: center; }
    .category-btn { background: white; border: 1px solid #eee; border-radius: 12px; padding: 12px 25px; margin: 5px; transition: 0.3s; color: #666; font-weight: 500; }
    .category-btn.active { background: linear-gradient(90deg, #00a8e8, #4fc3f7); color: white; border: none; }
    .category-btn:hover:not(.active) { border-color: #00a8e8; color: #00a8e8; }
    
    .product-link { text-decoration: none; color: inherit; display: block; }
    .product-card { border: none; border-radius: 20px; overflow: hidden; background: white; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    
    .badge-unit { position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.8); padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; z-index: 10; }
    .text-light-blue { color: #00a8e8; }
    .btn-cart { background-color: #00a8e8; color: white; border: none; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; transition: 0.3s; cursor: pointer; }
    .btn-cart:hover { background-color: #0091c7; transform: scale(1.1); }
</style>

<div class="container">
    <div class="hero-section">
        <h1 class="fw-bold display-4">DailyShop</h1>
        <p class="text-muted fs-5">Kebutuhan Sehari-hari Anda</p>
    </div>

    {{-- MENU KATEGORI DINAMIS --}}
    <div class="text-center mb-5">
        <h6 class="fw-bold mb-3">Kategori</h6>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            {{-- Tombol Semua Produk --}}
            <a href="{{ url('/') }}" class="category-btn text-decoration-none {{ !request('kategori') ? 'active' : '' }}">
                Semua
            </a>
            
            {{-- Perulangan Kategori dari Database --}}
            @foreach($kategori as $kat)
                <a href="{{ url('/?kategori=' . $kat->nama_kategori) }}" 
                class="category-btn text-decoration-none {{ request('kategori') == $kat->nama_kategori ? 'active' : '' }}">
                    {{ $kat->nama_kategori }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- PRODUK PALING LARIS --}}
    @if(isset($topProduk) && $topProduk)
        <div class="row mb-5">
            <div class="col-12">
                <div class="alert alert-info shadow-sm border-0 rounded-4">
                    <h5 class="fw-bold mb-1">🔥 Produk Paling Laris Minggu Ini: {{ $topProduk->nama_produk }}</h5>
                    <p class="mb-0">Sudah terjual {{ $topProduk->total_terjual ?? 0 }} kali! Jangan sampai kehabisan.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- BAGIAN REKOMENDASI (CONTENT-BASED FILTERING) --}}
    @if(Auth::check() && count($rekomendasi) > 0)
    <div class="mb-4">
        <h4 class="fw-bold text-light-blue mb-0">Rekomendasi Untuk Anda</h4>
    </div>

    <div class="row g-4 mb-5">
        @foreach($rekomendasi as $item)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card card h-100 position-relative border-0 shadow-sm">
                
                {{-- Menampilkan Skor Akurasi Kemiripan Jika Nilainya Lebih dari 0 --}}
                @if($item['score'] > 0)
                    <span class="badge-unit text-success">Match {{ round($item['score'] * 100, 1) }}% ✨</span>
                @else
                    <span class="badge-unit text-primary">Saran ✨</span>
                @endif
                
                <a href="/produk/{{ $item['produk']->id }}" class="product-link">
                    {{-- SINKRONISASI: Menggunakan $item['produk']->foto --}}
                    <img src="{{ $item['produk']->gambar ? asset('images/'.$item['produk']->gambar) : 'https://via.placeholder.com/500x500?text=No+Image' }}" 
                         class="card-img-top p-2 rounded-4" alt="{{ $item['produk']->nama_produk }}" style="height: 180px; object-fit: cover;">
                    <div class="card-body pt-0">
                        <p class="text-muted small mb-1">{{ $item['produk']->kategori }}</p>
                        <h6 class="fw-bold mb-2 text-dark text-truncate">{{ $item['produk']->nama_produk }}</h6>
                    </div>
                </a>

                <div class="px-3 pb-3 d-flex justify-content-between align-items-center mt-auto">
                    <span class="fw-bold text-dark">Rp {{ number_format($item['produk']->harga, 0, ',', '.') }}</span>
                    @if($item['produk']->stok > 0)
                        <form action="/keranjang/tambah" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item['produk']->id }}">
                            <button type="submit" class="btn btn-cart rounded-circle shadow-sm fw-bold">+</button>
                        </form>
                    @else
                        <span class="badge bg-danger rounded-pill px-2">Habis</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <hr class="mb-5 opacity-25">
    @endif

    {{-- BAGIAN SEMUA / FILTER PRODUK / HASIL PENCARIAN --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h4 class="fw-bold">
            @if(isset($search) && $search)
                Hasil Pencarian: "<span class="text-primary">{{ $search }}</span>"
                @if(isset($kategori_dipilih))
                    di Kategori {{ $kategori_dipilih->nama_kategori }}
                @endif
            @elseif(isset($kategori_dipilih))
                Kategori: <span class="text-primary">{{ $kategori_dipilih->nama_kategori }}</span>
            @else
                Semua Produk
            @endif
        </h4>

        @if((isset($search) && $search) || isset($kategori_dipilih))
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-danger rounded-pill">Lihat Semua Produk</a>
        @endif
    </div>

    <div class="row g-4 mb-5">
        @forelse($produk as $p)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card card h-100 border-0 shadow-sm position-relative">
                
                <a href="/produk/{{ $p->id }}" class="product-link">
                    {{-- SINKRONISASI: Menggunakan $p->foto --}}
                    <img src="{{ $p->gambar ? asset('images/'.$p->gambar) : 'https://via.placeholder.com/500x500?text=No+Image' }}" 
                        class="card-img-top p-2 rounded-4" alt="{{ $p->nama_produk }}" style="height: 180px; object-fit: cover;">
                    <div class="card-body pt-0">
                        <p class="text-muted small mb-1">{{ $p->kategori }}</p>
                        <h6 class="fw-bold mb-2 text-dark text-truncate">{{ $p->nama_produk }}</h6>
                    </div>
                </a>

                <div class="px-3 pb-3 d-flex justify-content-between align-items-center mt-auto">
                    <span class="fw-bold text-dark">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                    @if($p->stok > 0)
                        <form action="/keranjang/tambah" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->id }}">
                            <button type="submit" class="btn btn-cart rounded-circle shadow-sm fw-bold">+</button>
                        </form>
                    @else
                        <span class="badge bg-danger rounded-pill px-2">Habis</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">
                {{ (isset($search) && $search) ? 'Produk yang Anda cari tidak ditemukan.' : 'Maaf, belum ada produk yang tersedia pada kategori ini.' }}
            </p>
        </div>
        @endforelse
    </div>
</div>
@endsection