<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    // =========================================================================
    // PEMBELI (Untuk Riwayat Belanja Pelanggan di Halaman Depan)
    // =========================================================================
    
    public function index()
    {
        // Mengambil data transaksi milik user yang sedang login
        $pesanan = DB::table('transaksis')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('layouts.pembeli.pesanan_index', compact('pesanan'));
    }

    public function detail(int $id)
    {
        // Mengambil transaksi spesifik milik pembeli
        $transaksi = DB::table('transaksis')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$transaksi) {
            return redirect('/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Mengambil detail produk di dalam transaksi tersebut
        $detail = DB::table('detail_transaksis')
            ->join('produk', 'detail_transaksis.produk_id', '=', 'produk.id')
            ->where('detail_transaksis.transaksi_id', $id)
            ->select('detail_transaksis.*', 'produk.nama_produk', 'produk.gambar')
            ->get();

        return view('layouts.pembeli.pesanan_detail', compact('transaksi', 'detail'));
    }


    // =========================================================================
    // ADMIN (Untuk Manajemen Pesanan Masuk di Dashboard Admin)
    // =========================================================================

    // Mengambil semua transaksi pelanggan yang masuk ke toko DailyShop
    public function indexAdmin()
    {
        // Mengambil semua transaksi, dijoin dengan tabel users untuk mengambil nama pembelinya
        $pesanan_masuk = DB::table('transaksis')
            ->join('users', 'transaksis.user_id', '=', 'users.id')
            ->select('transaksis.*', 'users.name as nama_pembeli')
            ->orderBy('transaksis.created_at', 'desc')
            ->get();

        // Diarahkan ke file index.blade.php yang ada di folder views/layouts/admin/pesanan/
        return view('layouts.admin.pesanan.index', compact('pesanan_masuk'));
    }

    /**
     * Tampilan Detail Admin: Melihat rincian item kebutuhan harian yang dibeli pelanggan
     */
    public function detailAdmin(int $id)
    {
        // Mengambil transaksi spesifik beserta nama dan email pembelinya
        // Diubah menjadi 'users.email as email' agar cocok dengan properti $transaksi->email di blade
        $transaksi = DB::table('transaksis')
            ->join('users', 'transaksis.user_id', '=', 'users.id')
            ->where('transaksis.id', $id)
            ->select('transaksis.*', 'users.name as nama_pembeli', 'users.email as email')
            ->first();

        if (!$transaksi) {
            return redirect('/admin/pesanan')->with('error', 'Data pesanan tidak ditemukan.');
        }

        // PERBAIKAN: Mengubah nama variabel dari $detail menjadi $items agar singkron dengan @foreach($items as $item) di detail.blade.php
        $items = DB::table('detail_transaksis')
            ->join('produk', 'detail_transaksis.produk_id', '=', 'produk.id')
            ->where('detail_transaksis.transaksi_id', $id)
            ->select('detail_transaksis.*', 'produk.nama_produk', 'produk.gambar', 'produk.kode_produk')
            ->get();

        // Diarahkan ke file detail pesanan admin dengan melempar $transaksi dan $items yang sudah fix
        return view('layouts.admin.pesanan.detail', compact('transaksi', 'items'));
    }

    // Mengubah status pesanan (Contoh: Pembayaran Berhasil -> Pesanan Dikirim -> Selesai)
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        // Update status transaksi berdasarkan id pesanan
        DB::table('transaksis')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        // Dialihkan kembali ke halaman detail admin dengan membawa pesan sukses alert
        return redirect('/admin/pesanan/'.$id)->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function laporanAdmin()
    {
        // 1. Ambil semua transaksi yang statusnya 'selesai' atau 'proses' (berhasil bayar)
        // Kamu bisa sesuaikan status mana saja yang dianggap sebagai pendapatan masuk
        $transaksi_sukses = DB::table('transaksis')
            ->whereIn('status', ['selesai', 'proses', 'dikirim'])
            ->get();

        // 2. Hitung total pendapatan kotor dari seluruh transaksi sukses
        $total_pendapatan = $transaksi_sukses->sum('total_harga');

        // 3. Hitung total pesanan sukses
        $total_pesanan = $transaksi_sukses->count();

        // 4. Ambil riwayat untuk tabel laporan di bawah
        $laporan_penjualan = DB::table('transaksis')
            ->join('users', 'transaksis.user_id', '=', 'users.id')
            ->select('transaksis.*', 'users.name as nama_pembeli')
            ->orderBy('transaksis.updated_at', 'desc')
            ->get();

        return view('layouts.admin.pesanan.laporan', compact('total_pendapatan', 'total_pesanan', 'laporan_penjualan'));
    }
}