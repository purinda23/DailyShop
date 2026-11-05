<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\AktivitasPengguna;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // 1. Mengolah Pendapatan Bulanan (Untuk Tabel/Grafik)
        $pendapatanBulanan = Transaksi::select(
            DB::raw('SUM(total_harga) as total'),
            DB::raw("DATE_FORMAT(tanggal, '%M') as bulan")
        )
        ->groupBy('bulan')
        ->get();

        // 2. Mengolah Tren Produk Populer (Berdasarkan Aktivitas 'beli')
        // Sesuai poin proposal: produk yang banyak diminati [cite: 70, 82]
        $produkPopuler = DB::table('aktivitas_pengguna')
            ->join('produk', 'aktivitas_pengguna.kode_produk', '=', 'produk.kode_produk')
            ->select('produk.nama_produk', DB::raw('count(*) as total_minat'))
            ->where('aktivitas_pengguna.jenis_aktivitas', 'beli')
            ->groupBy('produk.nama_produk')
            ->orderBy('total_minat', 'desc')
            ->take(5)
            ->get();

        return view('admin.laporan.index', compact('pendapatanBulanan', 'produkPopuler'));
    }
}