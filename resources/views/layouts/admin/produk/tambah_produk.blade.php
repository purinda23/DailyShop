@extends('layouts.app')

@section('content')
<style>
    body{
        background:#f8fafc;
        overflow-x:hidden;
    }

    .content-wrapper{
        margin-left:280px;
        padding:2rem;
        min-height:100vh;
    }

    .card-product{
        border:none;
        border-radius:20px;
        box-shadow:0 .125rem .25rem rgba(0,0,0,.075);
    }

    .form-control,
    .form-select{
        border-radius:12px;
    }

    .form-control:focus,
    .form-select:focus{
        border-color:#0ea5e9;
        box-shadow:0 0 0 .25rem rgba(14,165,233,.15);
    }

    .btn-save{
        background:linear-gradient(90deg,#0ea5e9,#38bdf8);
        border:none;
        color:white;
        font-weight:600;
    }

    .btn-save:hover{
        color:white;
        opacity:.95;
    }

    .upload-box{
        background:#f8fafc;
        border:2px dashed #dbeafe;
        border-radius:16px;
        padding:20px;
    }
</style>

@include('layouts.admin.sidebar')

<div class="content-wrapper">

<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="/admin/produk"class="btn btn-light rounded-circle shadow-sm me-3">
            <i class="bi bi-arrow-left"></i></a>
        <div>
            <h2 class="fw-bold mb-1">Tambah Produk Baru</h2>
            <p class="text-muted mb-0">Tambahkan produk baru ke katalog DailyShop.</p>
        </div>
    </div>
    <div class="card card-product">
        <div class="card-body p-4 p-lg-5">
            <form action="/admin/produk" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Kode Produk</label>
                        <input type="text" name="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" value="{{ old('kode_produk') }}" placeholder="Contoh: MRN6100001" required>
                        @error('kode_produk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" placeholder="Masukkan nama produk" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option disabled selected>Pilih kategori</option>
                            <option value="Sembako">Sembako</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Bumbu">Bumbu</option>
                            <option value="Kebutuhan Rumah Tangga">Kebutuhan Rumah Tangga</option>
                            <option value="Perawatan Diri">Perawatan Diri</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Upload Gambar Produk</label>
                        <div class="upload-box">
                            <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">
                            <small class="text-muted">Format JPG, JPEG, PNG (maks. 2MB)</small>
                        </div>
                        @error('gambar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Harga Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Stok Awal</label>
                        <input 
                            type="number" 
                            name="stok" 
                            class="form-control" 
                            value="{{ old('stok', 0) }}" 
                            min="0" 
                            step="1" 
                            required>
                        <div class="form-text">Masukkan jumlah stok (minimal 0).</div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Deskripsi Produk</label>
                    <textarea name="deskripsi" rows="4" class="form-control" placeholder="Jelaskan detail produk...">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-save py-3">
                        <i class="bi bi-plus-circle me-2"></i> Simpan ke Katalog</button>
                    <a href="/admin/produk" class="btn btn-light border">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection