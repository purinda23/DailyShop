<div class="bg-white border-end pt-3 px-3" style="width: 280px; height: calc(100vh - 83px); position: fixed; top: 83px; left: 0; z-index: 1020; overflow-y: auto; box-sizing: border-box;">
    <div class="mb-4 px-2">
        <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">Menu Utama</small>
    </div>
    
    <div class="nav flex-column nav-pills gap-2">
        <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active bg-light text-dark' : 'text-secondary' }} border-0 py-2.5 px-3 rounded-3 fw-semibold d-flex align-items-center">
            <i class="bi bi-speedometer2 me-3 fs-5"></i> Dashboard
        </a>
        
        <div class="text-secondary small py-2 px-3 d-flex align-items-center fw-medium text-uppercase" style="letter-spacing: 0.5px;">
            <i class="bi bi-box-seam me-2 text-primary fs-6"></i> Produk Sistem
        </div>

        <a href="/admin/produk/tambah_produk" class="nav-link {{ request()->is('admin/produk/tambah_produk') ? 'active bg-light text-dark' : 'text-secondary' }} small py-2 px-3 ms-3 rounded-2 d-flex align-items-center fw-medium">
            <i class="bi bi-plus-circle me-2 text-success"></i> Tambah Produk Baru
        </a>

        <a href="/admin/produk" class="nav-link {{ request()->is('admin/produk') ? 'active bg-light text-dark' : 'text-secondary' }} small py-2 px-3 ms-3 rounded-2 d-flex align-items-center fw-medium">
            <i class="bi bi-pencil-square me-2 text-warning"></i> Kelola Produk
        </a>

        <hr class="my-2 text-muted">

        <a href="{{ route('admin.pesanan.index') }}" class="nav-link {{ request()->is('admin/pesanan*') ? 'active bg-light text-dark' : 'text-secondary' }} py-2.5 px-3 rounded-3 fw-semibold d-flex align-items-center">
            <i class="bi bi-cart-check me-3 fs-5 text-primary"></i> Pesanan Masuk
        </a>
        
        <a href="/admin/laporan" class="nav-link {{ request()->is('admin/laporan*') ? 'active bg-light text-dark' : 'text-secondary' }} py-2.5 px-3 rounded-3 fw-semibold d-flex align-items-center">
            <i class="bi bi-graph-up-arrow me-3 fs-5 text-primary"></i> Laporan Penjualan
        </a>
        <a href="/admin/ekspedisi" class="nav-link {{ request()->is('admin/ekspedisi*') ? 'active bg-light text-dark' : 'text-secondary' }} py-2.5 px-3 rounded-3 fw-semibold d-flex align-items-center">
            <i class="bi bi-truck me-3 fs-5 text-primary"></i> Ekspedisi
        </a>
    </div>

    <div style="position: absolute; bottom: 10px; left: 0; width: 100%; padding: 0 15px; box-sizing: border-box;">
        <hr class="text-muted">
        <small class="text-muted d-block text-center">DailyShop Admin</small>
    </div>
</div>