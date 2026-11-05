@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Shopping Cart</h5>
                    <a href="/" class="text-dark text-decoration-none fw-bold">&times;</a>
                </div>
                <div class="card-body py-5 text-center">
                    @if(empty($keranjang))
                        {{-- Tampilan saat keranjang kosong (Seperti gambarmu) --}}
                        <div class="py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329073.png" width="100" class="mb-3 opacity-25">
                            <h6 class="fw-bold text-muted">Keranjang Kosong</h6>
                            <p class="small text-secondary">Masukkan beberapa produk</p>
                            <a href="/" class="btn btn-sm btn-primary rounded-pill px-4" style="background-color: #00a8e8; border: none;">Belanja Sekarang</a>
                        </div>
                    @else
                        {{-- Tampilan jika ada isi produk --}}
                        <ul class="list-group list-group-flush text-start">
                            @foreach($keranjang as $id => $details)
                                <li class="list-group-item d-flex align-items-center border-0 mb-3 shadow-sm rounded-3">
                                    <img src="{{ asset('images/'.$details['gambar']) }}" width="60" class="rounded-3 me-3">
        
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">{{ $details['nama'] }}</h6>
                                        <span class="fw-bold text-light-blue">Rp {{ number_format($details['harga'], 0, ',', '.') }}</span>
                                    </div>

                                    <div class="d-flex align-items-center bg-light rounded-pill px-2">
                                        <form action="/keranjang/update" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="hidden" name="aksi" value="kurang">
                                            <button type="submit" class="btn btn-sm fw-bold">-</button>
                                        </form>

                                        <span class="mx-3 fw-bold">{{ $details['jumlah'] }}</span>

                                        <form action="/keranjang/update" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <input type="hidden" name="aksi" value="tambah">
                                            <button type="submit" class="btn btn-sm fw-bold">+</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                            @if(!empty($keranjang))
                                <div class="mt-4 pt-3 border-top">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal Produk</span>
                                        <span class="fw-bold">
                                            @php
                                                $total = 0;
                                                foreach($keranjang as $item) {
                                                    $total += $item['harga'] * $item['jumlah'];
                                                }
                                            @endphp
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </div>
        
                                    <!-- <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">Biaya Layanan</span>
                                        <span class="text-success fw-bold">Gratis</span>
                                    </div> -->

                                    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3" style="background-color: #f8fdff; border: 1px dashed #00a8e8;">
                                        <h5 class="fw-bold mb-0">Total Pembayaran</h5>
                                        <h5 class="fw-bold text-light-blue mb-0">Rp {{ number_format($total, 0, ',', '.') }}</h5>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="/" class="btn btn-link text-muted text-decoration-none">
                                            Tambah Barang Lagi
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </ul>
                        <hr>
                        <div class="d-grid mt-3">
                            <a href="/checkout" class="btn btn-primary py-2 fw-bold text-white text-decoration-none text-center">
                                Checkout Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection