<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DailyShop - Kebutuhan Sehari-hari Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f7ff; }
        
        /* Warna Biru Muda Kustom */
        .bg-light-blue { background-color: #00a8e8 !important; }
        .text-light-blue { color: #00a8e8; }
        
        .navbar { background-color: white !important; border-bottom: 2px solid #00a8e8; }
        
        /* Search Bar Tengah */
        .search-bar { 
            border-radius: 12px; 
            border: 1.5px solid #e0e0e0; 
            background-color: #f8f9fa;
            transition: 0.3s;
        }
        .search-bar:focus-within {
            border-color: #00a8e8;
            background-color: white;
            box-shadow: 0 0 0 0.25rem rgba(0, 168, 232, 0.1);
        }
        
        /* Tombol Keranjang & Login */
        .cart-btn { 
            background: linear-gradient(90deg, #00a8e8, #4fc3f7); 
            color: white; 
            border-radius: 10px; 
            border: none; 
            padding: 10px 20px; 
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
        }
        .cart-btn:hover { color: white; opacity: 0.9; transform: translateY(-2px); }

        .btn-login {
            color: #00a8e8;
            font-weight: 600;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-login:hover { background-color: #f0f7ff; }

        /* Badge Angka Keranjang */
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: #ffca28;
            color: #333;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            min-width: 20px;
            text-align: center;
        }

        /* Style bagian navbar profil */
        .custom-tabs .nav-link { border: none; padding: 10px 20px; transition: 0.3s; }
        .custom-tabs .nav-link.active { border-bottom: 3px solid #0d6efd !important; color: #0d6efd !important; background: transparent; }
        .text-pink { color: #ff477e; }
        .form-control:focus { background-color: #fff !important; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); }

        .main-container {
            min-height: calc(100vh - 83px);
            background-color: #f8fafc;
            width: 100%;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top py-3">
        <div class="container">
            <div class="row w-100 align-items-center g-0">
                
                <div class="col-lg-3 col-6 order-1">
                    <a class="navbar-brand d-flex align-items-center me-0" href="/">
                        <div class="bg-light-blue p-2 rounded-3 me-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <img src="https://img.icons8.com/material-outlined/20/ffffff/shopping-cart.png"/>
                        </div>
                        <span class="fw-bold fs-4 text-light-blue mb-0" style="letter-spacing: -0.5px;">DailyShop</span>
                    </a>
                </div>

                <div class="col-lg-5 col-12 order-3 order-lg-2 mt-3 mt-lg-0">
                    @if(!Auth::check() || Auth::user()->role !== 'admin')
                        <form action="{{ url('/') }}" method="GET" class="d-flex flex-grow-1 mx-4">
                            {{-- Mempertahankan filter kategori yang sedang aktif saat melakukan pencarian --}}
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            <div class="input-group">
                                {{-- Sisi Kiri: Ikon pencarian dengan border-end-0 agar menyatu dengan input --}}
                                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                {{-- Sisi Kanan: Input teks dengan border-start-0 dan rounded-end-pill sempurna --}}
                                <input type="text" 
                                    name="search" 
                                    class="form-control border-start-0 rounded-end-pill pe-3" 
                                    placeholder="Cari kebutuhan harianmu di sini..." 
                                    value="{{ request('search') }}">
                            </div>
                        </form>
                    @endif
                </div>

                <div class="col-lg-4 col-6 order-2 order-lg-3 text-end d-flex justify-content-end align-items-center">
                    
                    @auth
                        <div class="dropdown {{ (!Auth::check() || Auth::user()->role !== 'admin') ? 'me-3' : '' }} d-none d-lg-block">
                            <a class="nav-link dropdown-toggle fw-bold text-muted d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-4 me-2 text-light-blue"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item" href="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</a></li>
                                @else
                                    <li><a class="dropdown-item" href="/profil"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                                    <li><a class="dropdown-item" href="/pesanan"><i class="bi bi-bag-check me-2"></i> Pesanan Saya</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-login me-2">Login</a>
                        <a href="/register" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-3 d-none d-lg-inline-block">Daftar</a>
                    @endauth

                    @if(!Auth::check() || Auth::user()->role !== 'admin')
                        <a href="/keranjang" class="cart-btn fw-bold position-relative shadow-sm">
                            <i class="bi bi-cart3 me-2"></i> 
                            <span>Keranjang</span>

                            @if(session('keranjang') && count(session('keranjang')) > 0)
                                <span class="cart-badge">
                                    {{ array_sum(array_column(session('keranjang'), 'jumlah')) }}
                                </span>
                            @endif
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </nav>

    <main class="main-container">

        {{-- Alert Messages --}}
        @if(session('success') || session('error'))
        <div class="container-fluid pt-4 px-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        @endif

        {{-- Tempat konten halaman dirender --}}
        @yield('content')
    </main>
    
    <footer class="py-5 text-center text-muted mt-5">
        <hr class="container mb-4">
        <small>&copy; 2026 <strong>DailyShop</strong></small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>