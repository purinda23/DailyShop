@extends('layouts.app')

@section('content')
<div class="container py-5">

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout.submit') }}" method="POST">
        @csrf

        <input type="hidden"
               name="total"
               id="total_input"
               value="{{ $total_produk }}">

        <div class="row g-4">

            {{-- KIRI --}}
            <div class="col-md-8">

                {{-- PRODUK --}}
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3">Produk yang Dibeli</h5>

                    @foreach($keranjang as $id => $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                {{ $item['nama'] }}
                                (x{{ $item['jumlah'] }})
                            </span>

                            <span>
                                Rp {{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- PENGIRIMAN --}}
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold mb-3">Alamat Pengiriman</h5>

                    <select
                        name="alamat"
                        class="form-select mb-3"
                        required>

                        <option value="">
                            Pilih Alamat
                        </option>

                        @foreach($pilihan_alamat as $alamat)
                            <option value="{{ $alamat['value'] }}">
                                {{ $alamat['label'] }} :
                                {{ $alamat['value'] }}
                            </option>
                        @endforeach
                    </select>

                    @error('alamat')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                {{-- EKSPEDISI --}}
                <div class="card border-0 shadow-sm p-4">

                    <h5 class="fw-bold mb-3">
                        Pilih Ekspedisi
                    </h5>

                    <select
                        name="id_ekspedisi"
                        id="id_ekspedisi"
                        class="form-select"
                        required>

                        <option value="" data-harga="0">
                            Pilih Ekspedisi
                        </option>

                        @foreach($ekspedisi as $eks)
                            <option
                                value="{{ $eks->id }}"
                                data-harga="{{ $eks->biaya }}">

                                {{ $eks->nama_ekspedisi }}
                                -
                                Rp {{ number_format($eks->biaya, 0, ',', '.') }}

                            </option>
                        @endforeach

                    </select>

                    @error('id_ekspedisi')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

            </div>

            {{-- KANAN --}}
            <div class="col-md-4">

                <div class="card border-0 shadow-sm rounded-3 p-4">

                    <h4 class="fw-bold mb-4">
                        Ringkasan Pesanan
                    </h4>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal Produk</span>

                        <span class="fw-bold">
                            Rp {{ number_format($total_produk, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Biaya Pengiriman</span>

                        <span
                            id="tampil_ongkir"
                            class="fw-bold text-primary">
                            Rp 0
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold">
                            Total Bayar
                        </span>

                        <h5
                            id="total_bayar"
                            class="fw-bold text-primary mb-0"
                            data-subtotal="{{ $total_produk }}">

                            Rp {{ number_format($total_produk, 0, ',', '.') }}

                        </h5>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100 py-3 fw-bold">

                        Bayar Sekarang

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const ekspedisi = document.getElementById('id_ekspedisi');
    const tampilOngkir = document.getElementById('tampil_ongkir');
    const totalBayar = document.getElementById('total_bayar');
    const totalInput = document.getElementById('total_input');

    const subtotal =
        parseInt(totalBayar.dataset.subtotal);

    ekspedisi.addEventListener('change', function () {

        const selected =
            ekspedisi.options[ekspedisi.selectedIndex];

        const ongkir =
            parseInt(selected.dataset.harga) || 0;

        const total =
            subtotal + ongkir;

        tampilOngkir.innerText =
            'Rp ' + ongkir.toLocaleString('id-ID');

        totalBayar.innerText =
            'Rp ' + total.toLocaleString('id-ID');

        totalInput.value = total;
    });

});
</script>

@endsection