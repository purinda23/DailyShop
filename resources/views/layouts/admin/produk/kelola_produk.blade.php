@extends('layouts.app')

@section('content')
<style>
    /* Mengunci layar agar tidak ada double scrollbar horizontal */
    body {
        overflow-x: hidden;
        background-color: #f8fafc;
    }
    
    /* PENGUNCI LAYOUT KONTEN: Menggeser ke kanan sidebar (280px) */
    .main-admin-content {
        margin-left: 280px; /* Sesuai dengan lebar sidebar DailyShop */
        width: calc(100% - 280px); /* Mengambil sisa lebar layar */
        padding-top: 0px; /* Di-nolkan agar tidak ada celah kosong internal */
        margin-top: -45px; /* TRIK UTAMA: Menarik konten ke atas agar mepet pas dengan area alert */
        min-height: calc(100vh - 83px);
    }

    /* FIX TAMILAN ALERT INDUK: Menggeser alert agar tidak tertutup sidebar */
    .alert {
        margin-left: 310px; /* Jarak aman di sebelah kanan posisi fixed sidebar */
        margin-right: 30px;
        margin-top: 15px;   /* Letak presisi tepat di bawah batas garis biru navbar atas */
        margin-bottom: 0px; /* Dikunci 0px agar space ke card tabel tidak kejauhan */
        position: relative;
        z-index: 1000;      /* Memastikan mengambang dengan aman di atas element background */
    }

    /* Ukuran thumbnail gambar produk di dalam tabel */
    .img-table-thumb {
        object-fit: cover; 
        height: 50px; 
        width: 50px;
    }

    /* Responsif: Jika dibuka di HP atau Tablet */
    @media (max-width: 768px) {
        .main-admin-content {
            margin-left: 0;
            width: 100%;
            padding-top: 10px;
            margin-top: 0px;
        }
        .alert {
            margin-left: 15px;
            margin-right: 15px;
            margin-top: 80px;
            margin-bottom: 10px;
        }
    }
</style>

<div class="container-fluid px-0">
    <div class="position-relative">
        
        @include('layouts.admin.sidebar')

        <div class="main-admin-content p-4 p-md-5">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Daftar Produk DailyShop</h3>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="p-3" style="width: 80px;">Foto</th>
                                    <th class="p-3">Nama Produk</th>
                                    <th class="p-3">Kategori</th>
                                    <th class="p-3">Harga</th>
                                    <th class="p-3">Stok</th>
                                    <th class="p-3 text-center" style="width: 200px;">Aksi (Edit / Hapus)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produk as $item)
                                <tr>
                                    <td class="p-3">
                                        <img src="{{ $item->gambar ? asset('images/' . $item->gambar) : 'https://placehold.co/150' }}" 
                                             class="img-table-thumb rounded-3 border" 
                                             alt="{{ $item->nama_produk }}">
                                    </td>
                                    <td class="p-3 fw-bold text-dark">
                                        {{ $item->nama_produk }}
                                        <small class="text-muted d-block fw-normal" style="font-size: 0.75rem;">{{ $item->kode_produk }}</small>
                                    </td>
                                    <td class="p-3 text-secondary">{{ $item->kategori ?? 'Kebutuhan Harian' }}</td>
                                    <td class="p-3 fw-bold text-primary">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="p-3">
                                        <span class="badge {{ $item->stok > 5 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-1.5">
                                            {{ $item->stok }} Pcs
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="d-inline-flex gap-2">
                                            <a href="/admin/produk/{{ $item->id }}/edit" class="btn btn-sm btn-warning text-white rounded-2 px-3 fw-semibold">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                            
                                            <form action="/admin/produk/{{ $item->id }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus produk {{ $item->nama_produk }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-2 px-3 fw-semibold">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center p-5 text-muted small">
                                        <i class="bi bi-box-seam d-block mb-2 fs-3 text-secondary"></i>
                                        Belum ada produk yang dimasukkan ke sistem.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection