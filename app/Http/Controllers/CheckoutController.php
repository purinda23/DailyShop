<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $keranjang = session()->get('keranjang', []);

        if (empty($keranjang)) {
            return redirect('/')->with('error', 'Keranjang belanja kosong!');
        }

        $total_produk = collect($keranjang)->sum(function ($item) {
            return $item['harga'] * $item['jumlah'];
        });

        $ekspedisi = DB::table('ekspedisis')
            ->select('id', 'nama_ekspedisi', 'biaya')
            ->get();

        $user = Auth::user();

        $pilihan_alamat = [];

        if ($user && !empty($user->alamat)) {

            $kumpulan = preg_split(
                '/[\n|]+|,\s*(?=[\[A-Za-z0-9])/',
                $user->alamat
            );

            foreach (array_filter(array_map('trim', $kumpulan)) as $index => $alamat) {
                if (strlen($alamat) > 5) {
                    $pilihan_alamat[] = [
                        'label' => 'Alamat ' . ($index + 1),
                        'value' => trim($alamat, ",-| ")
                    ];
                }
            }
        }

        return view(
            'layouts.pembeli.checkout',
            compact(
                'keranjang',
                'ekspedisi',
                'user',
                'pilihan_alamat',
                'total_produk'
            )
        );
    }
}