@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #f8fafc;
        overflow-x: hidden;
    }

    .content-wrapper {
        margin-left: 280px;
        padding: 2rem;
        min-height: 100vh;
    }

    .card-edit {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
    }

    .form-control,
    .form-select {
        border-radius: 12px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 .25rem rgba(14,165,233,.15);
    }

    .btn-update {
        background: linear-gradient(90deg,#0ea5e9,#38bdf8);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-update:hover {
        color: white;
        opacity: .95;
    }

    .preview-image {
        max-height: 260px;
        object-fit: cover;
        border-radius: 16px;
    }
</style>

@include('layouts.admin.sidebar')

<div class="content-wrapper">

    <div class="container-fluid">
        <div class="d-flex align-items-center mb-4">
            <a href="/admin/produk"
               class="btn btn-light rounded-circle shadow-sm me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-1">
                    Edit Produk: {{ $produk_terpilih->nama_produk }} </h3>
                <p class="text-muted mb-0">
                    Perbarui informasi detail barang di katalog DailyShop.</p>
            </div>
        </div>

        <div class="card card-edit">
            <div class="row g-0">
                <div class="col-lg-4 bg-light border-end">
                    <div class="p-4 text-center">
                        <p class="fw-bold text-secondary text-uppercase small mb-4">
                            Produk</p>
                        <img
                            src="{{ $produk_terpilih->gambar ? asset('images/'.$produk_terpilih->gambar) : 'https://placehold.co/300x300' }}"
                            class="img-fluid preview-image shadow-sm border">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="p-4 p-lg-5">
                        <form action="/admin/produk/{{ $produk_terpilih->id }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        Kode Produk
                                    </label>
                                    <input
                                        type="text"
                                        name="kode_produk"
                                        class="form-control"
                                        value="{{ old('kode_produk', $produk_terpilih->kode_produk) }}"
                                        required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        Nama Produk
                                    </label>
                                    <input
                                        type="text"
                                        name="nama_produk"
                                        class="form-control"
                                        value="{{ old('nama_produk', $produk_terpilih->nama_produk) }}"
                                        required>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        Kategori
                                    </label>
                                    <select name="kategori" class="form-select">
                                        @foreach([
                                            'Sembako',
                                            'Makanan',
                                            'Minuman',
                                            'Bumbu',
                                            'Kebutuhan Rumah Tangga',
                                            'Perawatan Diri'
                                        ] as $cat)
                                            <option
                                                value="{{ $cat }}"
                                                {{ old('kategori', $produk_terpilih->kategori) == $cat ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        Ganti Foto Baru
                                    </label>
                                    <input
                                        type="file"
                                        name="gambar"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        Harga (Rp)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            Rp
                                        </span>
                                        <input
                                            type="number"
                                            name="harga"
                                            class="form-control"
                                            value="{{ old('harga', $produk_terpilih->harga) }}"
                                            min="0"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        Jumlah Stok
                                    </label>
                                    <input
                                        type="number"
                                        name="stok"
                                        class="form-control"
                                        value="{{ old('stok', $produk_terpilih->stok) }}"
                                        min="0"
                                        required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Deskripsi Produk
                                </label>
                                <textarea
                                    name="deskripsi"
                                    rows="4"
                                    class="form-control">{{ old('deskripsi', $produk_terpilih->deskripsi) }}</textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-update py-3">
                                    <i class="bi bi-cloud-arrow-up me-2"></i>
                                    Perbarui Data Produk
                                </button>
                                <a href="/admin/produk"
                                   class="btn btn-light border">
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection