<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Services\RekomendasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProdukController extends Controller
{
    protected RekomendasiService $recommendationService;

    // Tambahkan constructor untuk menyuntikkan fungsi RecommendationService
    public function __construct(RekomendasiService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * MENU ADMIN: Menampilkan daftar produk untuk halaman Kelola/Edit Produk
     */
    public function index()
    {
        // Cukup ambil semua produk untuk ditampilkan di tabel admin
        $produk = Produk::all();
        
        // Kirim data ke view admin
        return view('layouts.admin.produk.kelola_produk', compact('produk'));
    }

    public function katalog(Request $request)
    {
        // 1. Ambil kategori unik dari produk
        $kategori = Produk::select('kategori as nama_kategori')
                        ->distinct()
                        ->whereNotNull('kategori')
                        ->get();

        // 2. Query Produk (Filter & Search)
        $query_produk = Produk::query();
        
        $search = $request->query('search');
        $kategori_nama = $request->query('kategori');
        $kategori_dipilih = null;

        if ($search) {
            $query_produk->where('nama_produk', 'LIKE', '%' . $search . '%');
        }

        if ($kategori_nama) {
            $kategori_dipilih = (object) ['nama_kategori' => $kategori_nama];
            $query_produk->where('kategori', $kategori_nama);
        }

        $produk = $query_produk->get();

        // 3. Query Top 1 Penjualan
        $topProdukData = DB::table('detail_transaksis')
            ->select('produk_id', DB::raw('SUM(jumlah) as total_terjual'))
            ->groupBy('produk_id')
            ->orderBy('total_terjual', 'desc')
            ->first();

        $topProduk = null;
        if ($topProdukData) {
            $topProduk = Produk::find($topProdukData->produk_id);
            if ($topProduk) {
                $topProduk->total_terjual = $topProdukData->total_terjual;
            }
        }

        // 4. Logika Rekomendasi (Content-Based)
        $rekomendasi = collect();
        $userId = Auth::id(); 
        
        if ($userId && Schema::hasTable('aktivitas_pengguna')) {
            $userInteractions = DB::table('aktivitas_pengguna')
                ->join('produk', 'aktivitas_pengguna.produk_id', '=', 'produk.id')
                ->where('aktivitas_pengguna.user_id', $userId)
                ->select('produk.nama_produk', 'produk.kategori', 'produk.deskripsi')
                ->get();

            if ($userInteractions->isNotEmpty()) {
                $rekomendasi = $this->recommendationService->calculateRecommendations($userInteractions, 4);
            }
        }

        if ($rekomendasi->isEmpty()) {
            $randomProducts = Produk::inRandomOrder()->limit(4)->get();
            $rekomendasi = $randomProducts->map(function($prod) {
                return ['produk' => $prod, 'score' => 0];
            });
        }

        // 5. Oper semua ke satu view
        return view('layouts.pembeli.beranda', compact(
            'produk', 'kategori', 'topProduk', 'kategori_dipilih', 'rekomendasi', 'search'
        ));
    }

    /**
     * Halaman form tambah produk
     */
    public function create() {
        return view('layouts.admin.produk.tambah_produk');
    }

    /**
     * Fungsi untuk menampilkan halaman form edit
     */
    public function edit(int $id)
    {
        $produk_terpilih = Produk::findOrFail($id); 
        return view('layouts.admin.produk.edit', compact('produk_terpilih'));
    }

    /**
     * Fungsi untuk mengeksekusi perubahan data (Update)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'kode_produk' => 'required|string|unique:produk,kode_produk,'.$id,
            'nama_produk' => 'required|string',
            'kategori'    => 'required',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $produk = Produk::findOrFail($id);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar && file_exists(public_path('images/' . $produk->gambar))) {
                @unlink(public_path('images/' . $produk->gambar));
            }

            $namaGambar = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images'), $namaGambar);
            $produk->gambar = $namaGambar; 
        }

        $produk->kode_produk = $request->kode_produk;
        $produk->nama_produk = $request->nama_produk;
        $produk->kategori = $request->kategori;
        $produk->deskripsi = $request->deskripsi;
        $produk->harga = $request->harga;
        $produk->stok = $request->stok;
        $produk->save();

        return redirect('/admin/produk')->with('success', 'Produk "' . $produk->nama_produk . '" berhasil diperbarui!');
    }

    /**
     * Fungsi untuk mengeksekusi penghapusan data
     */
    public function destroy(int $id)
    {
        $produk = Produk::findOrFail($id);
        
        if ($produk->foto && file_exists(public_path('images/' . $produk->foto))) {
            @unlink(public_path('images/' . $produk->foto));
        }

        $produk->delete();
        return redirect('/admin/produk')->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Fungsi menyimpan produk baru (Tambah)
     */
    public function store(Request $request) {
        $request->validate([
            'kode_produk' => 'required|unique:produk',
            'nama_produk' => 'required',
            'kategori'    => 'required',
            'harga'       => 'required|numeric',
            'stok'        => 'required|integer',
            'gambar'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $namaGambar = null;
        if ($request->hasFile('gambar')) {
            $namaGambar = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images'), $namaGambar);
        }

        // SINKRONISASI: Diubah menjadi 'foto' agar konsisten dengan method update() kamu
        Produk::create([
            'kode_produk' => $request->kode_produk,
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'gambar'        => $namaGambar 
        ]);

        return redirect('/admin/produk')->with('success', 'Produk berhasil disimpan!');
    }

    /**
     * Menampilkan detail produk pembeli
     */
    public function show(int $id) {
        $produk = Produk::find($id);

        if (!$produk) {
            abort(404);
        }

        if (Auth::check() && Schema::hasTable('aktivitas_pengguna')) {
            DB::table('aktivitas_pengguna')->insert([
                'user_id'    => Auth::id(),
                'produk_id'  => $id,
                'aktivitas'  => 'lihat',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return view('layouts.pembeli.detail', compact('produk'));
    }
}