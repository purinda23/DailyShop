<?php

namespace App\Http\Controllers;

use App\Models\AktivitasPengguna;
use Illuminate\Support\Facades\Auth;

class AktivitasPenggunaController extends Controller
{
    public function catat($kode_produk, $jenis) {
        if (Auth::check()) {
            AktivitasPengguna::create([
                'id_pengguna' => Auth::id(),
                'kode_produk' => $kode_produk,
                'jenis_aktivitas' => $jenis, // 'lihat', 'klik', 'beli'
                'waktu_aktivitas' => now()
            ]);
        }
    }
}