<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\XenditController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EkspedisiController;

// =========================================================================
// 1. PUBLIC ROUTES (Bisa diakses tanpa login sama sekali)
// =========================================================================
Route::get('/', [ProdukController::class, 'katalog']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);
Route::post('/keranjang/tambah', [KeranjangController::class, 'tambah']);

// =========================================================================
// 2. GUEST ROUTES (Hanya bisa diakses jika BELUM login)
// =========================================================================
Route::middleware(['guest'])->group(function () {
    // Auth (Login & Register)
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', function () { return view('layouts.auth.register'); });
    Route::post('/register', [AuthController::class, 'register']);
    
    // Alur Lupa Password lewat Email
    Route::post('/lupa_password', [AuthController::class, 'kirimLinkReset']); // Menerima email & kirim token
    Route::get('/password/reset/{token}', [AuthController::class, 'tampilkanHalamanReset'])->name('password.reset'); // Halaman ketik pass baru
    Route::post('/password/reset', [AuthController::class, 'simpanPasswordBaru']); // Menyimpan ke database
});


// =========================================================================
// 3. AUTHENTICATED ROUTES (Wajib login & Terkunci oleh Prevent Back History)
// =========================================================================
Route::middleware(['auth', 'prevent-back'])->group(function () {
    Route::get('/profil', [ProfilController::class, 'index']);
    Route::put('/profil/update', [ProfilController::class, 'update']);
    Route::post('/lupa-password', [AuthController::class, 'resetPassword']);
    Route::post('/profil/alamat/store', [ProfilController::class, 'storeAlamat']);
    Route::post('/profil/alamat/update', [ProfilController::class, 'updateAlamat']);

    // Keranjang harus login jika ingin buka halaman keranjang
    Route::get('/keranjang', [KeranjangController::class, 'index']);
    Route::post('/keranjang/update', [KeranjangController::class, 'update']);

    Route::get('/checkout', [CheckoutController::class, 'index']);

    Route::post('/checkout/submit', [TransaksiController::class, 'checkout'])->name('checkout.submit');
    Route::get('/xendit/bayar', [XenditController::class, 'prosesPembayaran'])->name('xendit.bayar');
    Route::post('/xendit/callback', [XenditController::class, 'handleCallback']);
    Route::get('/pesanan', [TransaksiController::class, 'index'])->name('pesanan.index');
    Route::post('/transaksi/{id}/batal', [TransaksiController::class, 'batal'])->name('transaksi.batal');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [PesananController::class, 'detail'])->name('pesanan.detail');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


// =========================================================================
// 4. ADMIN ROUTES (Wajib Login Admin, Terkunci dari Tombol Back Browser)
// =========================================================================
Route::middleware(['auth', 'admin', 'prevent-back'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/tambah_produk', [ProdukController::class, 'create']);
    Route::post('/produk', [ProdukController::class, 'store']);
    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit']); 
    Route::put('/produk/{id}', [ProdukController::class, 'update']); 
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']); 

    Route::get('/pesanan', [PesananController::class, 'indexAdmin'])->name('admin.pesanan.index');
    Route::get('/pesanan/{id}', [PesananController::class, 'detailAdmin']);
    Route::post('/pesanan/update/{id}', [PesananController::class, 'updateStatus']);

    Route::get('/laporan', [PesananController::class, 'laporanAdmin']);

    Route::get('/ekspedisi', [EkspedisiController::class, 'index']);
    Route::post('/ekspedisi', [EkspedisiController::class, 'store']);
    Route::put('/ekspedisi/{id}', [EkspedisiController::class, 'update']);
    Route::delete('/ekspedisi/{id}', [EkspedisiController::class, 'destroy']);
});

Route::get('/doku-cek', function() {
    dd([
        'CLIENT_ID' => env('DOKU_CLIENT_ID'),
        'SECRET_KEY' => env('DOKU_SECRET_KEY'),
        'API_KEY' => env('DOKU_API_KEY')
    ]);
});