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
        margin-left: 280px; 
        width: calc(100% - 280px); 
        padding-top: 0px; /* Di-nolkan agar tidak ada bantalan dalam */
        margin-top: -45px; /* TRIK UTAMA: Nilai minus untuk menarik card ke atas mendekati alert */
        min-height: calc(100vh - 80px);
    }

    /* MENYETEL ULANG SPASI ALERT INDUK */
    .alert {
        margin-left: 310px; /* Jeda manis dari batas sidebar */
        margin-right: 30px;
        margin-top: 15px;   /* Pas di bawah batas aman navbar biru */
        margin-bottom: 0px; /* Dipaksa 0 agar tidak mendorong konten di bawahnya */
        position: relative;
        z-index: 1000;
    }

    /* Menghilangkan margin bawaan card agar tidak menambah scrollbar */
    .custom-admin-card {
        margin-bottom: 50px;
    }

    /* Responsif: Jika dibuka di HP/Tablet */
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
            
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 custom-admin-card">
                
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            <i class="bi bi-truck text-primary me-2"></i>Data Manajemen Ekspedisi
                        </h4>
                        <p class="text-muted mb-0 small">Kelola opsi mitra pengiriman kurir beserta tarif biaya harian toko DailyShop.</p>
                    </div>
                    <button class="btn btn-primary rounded-3 fw-semibold px-3 py-2 d-flex align-items-center mb-auto" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="bi bi-plus-circle me-2 fs-5"></i> Tambah Ekspedisi
                    </button>
                </div>

                <hr class="text-muted my-4">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th width="6%" class="text-center">No</th>
                                <th width="15%">Kode Kurir</th>
                                <th>Nama Ekspedisi</th>
                                <th>Biaya Pengiriman</th>
                                <th>Tanggal Dibuat</th>
                                <th width="18%" class="text-center">Aksi (Edit / Hapus)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($ekspedisis as $item)
                            <tr>
                                <td class="text-center fw-semibold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-2 fw-mono text-uppercase">
                                        {{ $item->kode_ekspedisi }}
                                    </span>
                                </td>
                                <td class="fw-bold text-dark">{{ $item->nama_ekspedisi }}</td>
                                <td class="fw-bold text-primary">
                                    Rp {{ number_format((float)($item->biaya ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i> {{ optional($item->created_at)->format('d M Y') ?? date('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning rounded-2 fw-semibold px-3 me-1 text-white"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit{{ $item->id }}">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </button>

                                    <form action="/admin/ekspedisi/{{ $item->id }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-2 fw-semibold px-3"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus mitra ekspedisi ini?')">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit{{ $item->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form action="/admin/ekspedisi/{{ $item->id }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-header border-0 pt-4 px-4 pb-0">
                                                <h5 class="fw-bold text-dark"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Ekspedisi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-secondary small">Kode Ekspedisi</label>
                                                    <input type="text" name="kode_ekspedisi" class="form-control rounded-3" value="{{ $item->kode_ekspedisi }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-secondary small">Nama Ekspedisi</label>
                                                    <input type="text" name="nama_ekspedisi" class="form-control rounded-3" value="{{ $item->nama_ekspedisi }}" required>
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label fw-semibold text-secondary small">Biaya Pengiriman (Rp)</label>
                                                    <input type="number" name="biaya" class="form-control rounded-3" value="{{ $item->biaya }}" required>
                                                </div>
                                            </div>

                                            <div class="modal-footer border-0 pb-4 px-4 pt-0">
                                                <button type="button" class="btn btn-light border rounded-3 fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-3 fw-semibold px-4">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @empty
                            <tr>
                                <td colspan="6" class="text-center p-5 text-muted small">
                                    <i class="bi bi-truck-flatbed d-block mb-2 fs-2 text-secondary"></i>
                                    Belum ada data armada mitra ekspedisi yang terdaftar.
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

<div class="modal fade" id="tambahModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="/admin/ekspedisi" method="POST">
                @csrf

                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark"><i class="bi bi-plus-circle text-primary me-2"></i>Registrasi Ekspedisi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Kode Ekspedisi</label>
                        <input type="text" name="kode_ekspedisi" class="form-control rounded-3" placeholder="Contoh: JNE, JNT, GOSEND" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Nama Ekspedisi</label>
                        <input type="text" name="nama_ekspedisi" class="form-control rounded-3" placeholder="Contoh: J&T Express Regular" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold text-secondary small">Biaya Pengiriman (Rp)</label>
                        <input type="number" name="biaya" class="form-control rounded-3" placeholder="Masukkan tarif nominal angka saja" required>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4 pt-0">
                    <button type="button" class="btn btn-light border rounded-3 fw-semibold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection