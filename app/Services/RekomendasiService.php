<?php

namespace App\Services;

use App\Models\Produk;

class RekomendasiService
{
    /**
     * Menghitung kemiripan konten produk dengan profil interaksi pengguna
     *
     * @param \Illuminate\Support\Collection $userInteractions
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function calculateRecommendations($userInteractions, $limit = 4)
    {
        // 1. Ambil semua produk yang tersedia di toko
        $allProducts = Produk::all();
        
        // 2. Bangun Vektor Profil Pengguna dari gabungan teks produk yang pernah dilihat
        $userProfileText = "";
        foreach ($userInteractions as $interaction) {
            $userProfileText .= " " . $interaction->kategori . " " . $interaction->nama_produk . " " . $interaction->deskripsi;
        }
        
        $userVector = $this->tokenize($userProfileText);
        $recommendations = [];

        // 3. Hitung Cosine Similarity untuk setiap produk di database
        foreach ($allProducts as $produk) {
            // Gabungkan teks fitur dari produk yang sedang diuji
            $productText = $produk->kategori . " " . $produk->nama_produk . " " . $produk->deskripsi;
            $productVector = $this->tokenize($productText);

            // Hitung skor kesamaan antara preferensi user dengan produk saat ini
            $score = $this->calculateCosine($userVector, $productVector);

            // Hanya masukkan produk yang memiliki kemiripan (skor > 0)
            if ($score > 0) {
                $recommendations[] = [
                    'produk' => $produk,
                    'score' => $score
                ];
            }
        }

        // 4. Urutkan hasil rekomendasi dari skor tertinggi ke terendah
        usort($recommendations, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // 5. Kembalikan data sesuai limit dalam bentuk Collection
        return collect(array_slice($recommendations, 0, $limit));
    }

    /**
     * Memecah teks menjadi array kata baku (Tokenisasi & Pembersihan Teks)
     */
    private function tokenize(string $text)
    {
        // Mengubah teks ke huruf kecil dan menghapus simbol/karakter aneh
        $cleanText = strtolower(preg_replace('/[^a-z0-9 ]/i', '', $text));
        
        // Menghitung frekuensi kemunculan kata (Term Frequency)
        return array_count_values(array_filter(explode(' ', $cleanText)));
    }

    /**
     * Rumus Matematika Cosine Similarity
     */
    private function calculateCosine(array $vec1, array $vec2)
    {
        $dotProduct = 0;
        $vector1Magnitude = 0;
        $vector2Magnitude = 0;

        // Hitung perkalian titik (Dot Product) dan panjang vektor 1
        foreach ($vec1 as $word => $count) {
            if (isset($vec2[$word])) {
                $dotProduct += $count * $vec2[$word];
            }
            $vector1Magnitude += pow($count, 2);
        }

        // Hitung panjang vektor 2
        foreach ($vec2 as $count) {
            $vector2Magnitude += pow($count, 2);
        }

        // Cek cegah pembagian dengan angka nol (zero division)
        if ($vector1Magnitude == 0 || $vector2Magnitude == 0) {
            return 0;
        }

        // Rumus: dotProduct / (sqrt(mag1) * sqrt(mag2))
        return $dotProduct / (sqrt($vector1Magnitude) * sqrt($vector2Magnitude));
    }
}