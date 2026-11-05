<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('layouts.pembeli.transaksi', compact('transaksi'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'alamat' => 'required',
            'id_ekspedisi' => 'required'
        ]);

        $keranjang = session()->get('keranjang', []);

        if (empty($keranjang)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        try {

            DB::beginTransaction();

            // Cek stok
            foreach ($keranjang as $id => $item) {

                $produk = Produk::find($id);

                if (!$produk) {
                    throw new \Exception('Produk tidak ditemukan.');
                }

                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception(
                        "Stok {$produk->nama_produk} tidak mencukupi."
                    );
                }
            }

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'kode_transaksi' => 'DS-' . strtoupper(uniqid()),
                'total_harga' => $request->total,
                'alamat_pengiriman' => $request->alamat,
                'metode_pembayaran' => 'Xendit',
                'ekspedisi_id' => $request->id_ekspedisi,
                'status' => 'proses'
            ]);
            // Simpan detail transaksi
            foreach ($keranjang as $id => $item) {

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['jumlah'] * $item['harga']
                ]);

                // Kurangi stok
                Produk::where('id', $id)
                    ->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            // Hapus keranjang
            session()->forget('keranjang');

            return redirect()->route(
                'xendit.bayar',
                ['transaksi_id' => $transaksi->id]
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Gagal membuat transaksi: ' . $e->getMessage()
            );
        }
    }

    public function batal(int $id)
    {
        try {

            DB::beginTransaction();

            $transaksi = Transaksi::findOrFail($id);

            if ($transaksi->status === 'batal') {
                return back()->with(
                    'error',
                    'Transaksi sudah dibatalkan.'
                );
            }

            foreach ($transaksi->detail as $detail) {

                $detail->produk->increment(
                    'stok',
                    $detail->jumlah
                );
            }

            $transaksi->update([
                'status' => 'batal'
            ]);

            DB::commit();

            return back()->with(
                'success',
                'Transaksi berhasil dibatalkan.'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Gagal membatalkan: ' . $e->getMessage()
            );
        }
    }
}