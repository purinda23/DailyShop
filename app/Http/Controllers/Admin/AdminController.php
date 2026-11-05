<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung total pendapatan dari transaksi yang sudah lunas/selesai
        $total_pendapatan = DB::table('transaksis')
            ->whereIn('status', ['Pembayaran Berhasil', 'dikirim', 'diproses'])
            ->sum('total_harga');

        // 2. Hitung jumlah pesanan baru yang butuh diproses
        $pesanan_masuk = DB::table('transaksis')
            ->where('status', 'Pembayaran Berhasil')
            ->count();

        // 3. Hitung total jenis produk di toko
        $total_produk = DB::table('produk')->count();

        // 4. Hitung total pelanggan/pembeli yang terdaftar
        $total_pelanggan = DB::table('users')
            ->where('role', 'pembeli')
            ->count();

        // 5. Ambil 5 transaksi terbaru untuk ditampilkan di tabel
        $transaksi_terbaru = DB::table('transaksis')
            ->join('users', 'transaksis.user_id', '=', 'users.id')
            ->select('transaksis.*', 'users.name as nama_pembeli')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('layouts.admin.dashboard', compact(
            'total_pendapatan', 
            'pesanan_masuk', 
            'total_produk', 
            'total_pelanggan', 
            'transaksi_terbaru'
        ));
    }
}