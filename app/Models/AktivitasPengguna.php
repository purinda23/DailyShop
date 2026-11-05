<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AktivitasPengguna extends Model
{
    protected $table = 'aktivitas_penggunas';

    protected $fillable = [
        'user_id',
        'aktivitas',
        'produk_id'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke produk (opsional)
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
