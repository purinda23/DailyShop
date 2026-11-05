@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    {{-- Header Banner / Judul Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if(request('search'))
                <h4 class="fw-bold mb-1">Hasil Pencarian untuk: <span class="text-primary">"{{ request('search') }}"</span></h4>
                <small class="text-muted">Menampilkan produk yang cocok dengan kata kunci Anda</small>
            @else
                <h3 class="fw-bold mb-1">Katalog Produk</h3>
                <small class="text-muted">Temukan semua kebutuhan harianmu dengan harga terbaik</small>
            @endif
        </div>
        
        {{-- Jika sedang mencari sesuatu, munculkan tombol kembali ke katalog awal --}}
        @if(request('search'))
            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-x-circle me-1"></i> Bersihkan Pencarian
            </a>
        @endif
    </div>

    {{-- Kondisi 1: Jika Produk Tidak Ditemukan --}}
    @if($produk->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-5">
            <div class="py-4">
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                <h5 class="fw-bold mt-4 mb-2">Produk Tidak Ditemukan</h5>
                <p class="text-muted mb-4">Maaf, produk dengan kata kunci "<strong>{{ request('search') }}</strong>" tidak tersedia di toko kami.</p>
                <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 py-2">Lihat Semua Produk</a>
            </div>
        </div>
    
    {{-- Kondisi 2: Jika Produk Berhasil Ditemukan / Tampil Normal --}}
    @else
        <div class="row g-4">
            @foreach($produk as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card" style="transition: transform 0.2s;">
                        
                        {{-- Bagian Gambar Produk --}}
                        <div class="position-relative bg-light text-center" style="height: 200px;">
                            @php
                                $pathGambar = $item->gambar;
                                // Logika penyesuaian public/images seperti sebelumnya
                                if (\Illuminate\Support\Str::contains($pathGambar, 'images/')) {
                                    $srcGambar = asset($pathGambar);
                                } else {
                                    $srcGambar = asset('images/' . $pathGambar);
                                }
                            @endphp
                            <img src="{{ $srcGambar }}" 
                                class="w-100 h-100" 
                                style="object-fit: cover;" 
                                alt="{{ $item->nama_produk }}">
                        </div>

                        {{-- Bagian Informasi Produk --}}
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark text-truncate mb-1" title="{{ $item->nama_produk }}">
                                    {{ $item->nama_produk }}
                                </h6>
                                <p class="text-muted small text-truncate mb-2">{{ $item->deskripsi ?? 'Tidak ada deskripsi produk.' }}</p>
                            </div>
                            
                            <div class="mt-2">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-primary fs-5">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                    <small class="text-muted">Stok: {{ $item->stok }}</small>
                                </div>

                                {{-- Form untuk Masuk ke Keranjang Belanja --}}
                                <form action="{{ url('/keranjang/tambah') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <input type="hidden" name="qty" value="1">
                                    
                                    @if($item->stok > 0)
                                        <button type="submit" class="btn btn-primary w-100 rounded-pill btn-sm py-2">
                                            <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary w-100 rounded-pill btn-sm py-2" disabled>
                                            Habis
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination (Halaman) bawaan Laravel agar link search tidak hilang saat klik halaman berikutnya --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $produk->appends(['search' => request('search')])->links() }}
        </div>
    @endif

</div>

{{-- Efek Hover Sederhana untuk Card Produk --}}
<style>
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection