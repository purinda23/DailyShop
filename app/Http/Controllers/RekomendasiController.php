<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Services\RekomendasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RekomendasiController extends Controller
{
    // Tambahkan type-hinting agar VS Code Intelephense mengenali metodenya
    protected RekomendasiService $recommendationService;

    /**
     * Constructor untuk Dependency Injection ke Service Penghitung Rekomendasi
     */
    public function __construct(RekomendasiService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Menampilkan halaman khusus rekomendasi produk untuk pembeli
     */
    public function index()
    {
        $rekomendasi = collect();
        $userId = Auth::id();

        // 1. Cek apakah user sudah masuk sistem dan tabel riwayatnya ada
        if ($userId && Schema::hasTable('aktivitas_pengguna')) {
            
            // Ambil semua riwayat produk yang pernah dibuka oleh user tersebut
            $userInteractions = DB::table('aktivitas_pengguna')
                ->join('produk', 'aktivitas_pengguna.produk_id', '=', 'produk.id')
                ->where('aktivitas_pengguna.user_id', $userId)
                ->select('produk.nama_produk', 'produk.kategori', 'produk.deskripsi')
                ->get();

            // 2. Jika ada riwayat, panggil fungsi perhitungan di dalam Service
            if ($userInteractions->isNotEmpty()) {
                $rekomendasi = $this->recommendationService->calculateRecommendations($userInteractions, 8); // Ambil top 8 produk
            }
        }

        // 3. FALLBACK: Jika belum ada riwayat (user baru), tampilkan produk acak sebagai default
        if ($rekomendasi->isEmpty()) {
            $randomProducts = Produk::inRandomOrder()->limit(8)->get();
            $rekomendasi = $randomProducts->map(function ($prod) {
                return [
                    'produk' => $prod,
                    'score' => 0 // Set skor ke 0 karena ini produk acak bukan hasil hitung konten
                ];
            });
        }

        // 4. Kirim hasil array rekomendasi ke halaman view blade kamu
        return view('layouts.pembeli.rekomendasi', compact('rekomendasi'));
    }
}