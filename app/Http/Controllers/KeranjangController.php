<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class KeranjangController extends Controller
{
    // Menampilkan halaman keranjang
    public function index()
    {
        $keranjang = session()->get('keranjang', []);
        return view('layouts.pembeli.keranjang', compact('keranjang'));
    }

    // Menambah produk ke keranjang
   public function tambah(Request $request)
    {
        // Validasi input ID harus ada dan berupa angka
        $request->validate(['id' => 'required|integer']);

        $produk = Produk::find($request->id);

        // 1. Validasi keberadaan produk
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan!');
        }

        // 2. Validasi stok awal
        if ($produk->stok <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok produk habis!');
        }

        $keranjang = session()->get('keranjang', []);

        // 3. Cek apakah produk sudah ada di keranjang
        if (isset($keranjang[$request->id])) {
            // Cek apakah penambahan berikutnya masih memenuhi stok
            if (($keranjang[$request->id]['jumlah'] + 1) > $produk->stok) {
                return redirect()->back()->with('error', 'Jumlah di keranjang tidak boleh melebihi stok yang tersedia!');
            }
            $keranjang[$request->id]['jumlah']++;
        } else {
            // Tambah produk baru ke keranjang
            $keranjang[$request->id] = [
                "nama"   => $produk->nama_produk,
                "jumlah" => 1,
                "harga"  => $produk->harga,
                "gambar" => $produk->gambar
            ];
        }

        session()->put('keranjang', $keranjang);
        return redirect()->back()->with('success', $produk->nama_produk . ' berhasil ditambahkan!');
    }

    // Update jumlah (tambah/kurang)
    public function update(Request $request)
    {
        $keranjang = session()->get('keranjang', []);
        $id = $request->id;
        $produk = Produk::find($id);

        if (isset($keranjang[$id])) {
            if ($request->aksi == 'tambah') {
                if ($keranjang[$id]['jumlah'] + 1 > $produk->stok) {
                    return redirect()->back()->with('error', 'Stok tidak mencukupi!');
                }
                $keranjang[$id]['jumlah']++;
            } elseif ($request->aksi == 'kurang') {
                $keranjang[$id]['jumlah']--;
                if ($keranjang[$id]['jumlah'] <= 0) {
                    unset($keranjang[$id]);
                }
            }
        }

        session()->put('keranjang', $keranjang);
        return redirect()->back()->with('success', 'Keranjang diperbarui!');
    }
}