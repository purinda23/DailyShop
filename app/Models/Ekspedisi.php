<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekspedisi extends Model
{
    protected $table = 'ekspedisis';

    protected $fillable = [
        'kode_ekspedisi',
        'nama_ekspedisi',
        'biaya'
    ];

    // relasi ke transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
