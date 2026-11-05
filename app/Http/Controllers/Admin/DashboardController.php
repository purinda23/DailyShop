<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Inisialisasi awal variabel dengan nilai default agar view aman dari error
        $total_pendapatan = 0;
        $pesanan_masuk = 0;
        $total_produk = 0;
        $total_pelanggan = 0;
        $transaksi_terbaru = collect();

        // Siapkan struktur array untuk 30 hari terakhir (Key: Y-m-d, Value: 0)
        $data_harian = [];
        for ($i = 29; $i >= 0; $i--) {
            $tanggal_key = Carbon::now()->subDays($i)->format('Y-m-d');
            $data_harian[$tanggal_key] = 0;
        }

        // 2. Tentukan nama tabel transaksi yang aktif di database
        $nama_tabel_transaksi = null;
        if (Schema::hasTable('transaksi')) {
            $nama_tabel_transaksi = 'transaksi';
        } elseif (Schema::hasTable('transaksis')) {
            $nama_tabel_transaksi = 'transaksis';
        }

        // Jika tabel transaksi ditemukan
        if ($nama_tabel_transaksi) {
            
            // Hitung total pendapatan (keseluruhan waktu)
            $total_pendapatan = DB::table($nama_tabel_transaksi)
                ->whereIn('status', ['Pembayaran Berhasil', 'dikirim', 'diproses', 'selesai', 'pending', 'proses'])
                ->where('status', '!=', 'batal')
                ->sum('total_harga') ?? 0;

            // Hitung jumlah pesanan masuk
            $pesanan_masuk = DB::table($nama_tabel_transaksi)
                ->whereIn('status', ['Pembayaran Berhasil', 'diproses', 'pending', 'proses'])
                ->count();
                
            // Ambil 5 transaksi terbaru
            if (Schema::hasTable('users')) {
                $foreignKey = Schema::hasColumn($nama_tabel_transaksi, 'id_pengguna') ? 'id_pengguna' : 'user_id';

                $transaksi_terbaru = DB::table($nama_tabel_transaksi)
                    ->join('users', "{$nama_tabel_transaksi}.{$foreignKey}", '=', 'users.id')
                    ->select("{$nama_tabel_transaksi}.*", 'users.name as nama_pembeli')
                    ->orderBy("{$nama_tabel_transaksi}.created_at", 'desc')
                    ->take(5)
                    ->get();
            }

            // KODE PERUBAHAN: Tarik data pendapatan harian selama 30 hari terakhir
            $pendapatan_harian = DB::table($nama_tabel_transaksi)
                ->select(
                    DB::raw('DATE(created_at) as tgl'),
                    DB::raw('SUM(total_harga) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(29)->startOfDay())
                ->where('status', '!=', 'batal')
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            // Masukkan hasil query ke array data harian yang sudah di-generate awal
            foreach ($pendapatan_harian as $ph) {
                if (array_key_exists($ph->tgl, $data_harian)) {
                    $data_harian[$ph->tgl] = (int) $ph->total;
                }
            }
        }

        // 3. Hitung total produk
        if (Schema::hasTable('produk')) {
            $total_produk = DB::table('produk')->count();
        }

        // 4. Hitung total pelanggan 
        if (Schema::hasTable('users')) {
            $total_pelanggan = DB::table('users')
                ->whereIn('role', ['pembeli', 'user'])
                ->count();
        }

        // Memisahkan tanggal (untuk Label grafik) dan total (untuk Data grafik)
        $grafik_tanggal = [];
        $grafik_pendapatan = [];

        foreach ($data_harian as $tgl => $total) {
            // Mengubah format tanggal '2026-06-19' menjadi format lebih singkat '19 Jun' untuk tampilan grafik
            $grafik_tanggal[] = Carbon::parse($tgl)->translatedFormat('d M');
            $grafik_pendapatan[] = $total;
        }

        // 5. Kirim semua data ke halaman view admin
        return view('layouts.admin.dashboard', compact(
            'total_pendapatan', 
            'pesanan_masuk', 
            'total_produk', 
            'total_pelanggan', 
            'transaksi_terbaru',
            'grafik_tanggal',   // <-- Label Tanggal Baru (Sumbu X)
            'grafik_pendapatan' // <-- Data Pendapatan Baru (Sumbu Y)
        ));
    }
}