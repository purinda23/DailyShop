<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',           
        'email',      
        'password',  
        'nomor_telepon', 
        'alamat',  
        'role'  
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Karena nama kolomnya 'password' (standar Laravel), 
    // sebenarnya fungsi ini opsional, tapi bagus untuk tetap ada.
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }

    public function aktivitasPenggunas()
    {
        return $this->hasMany(AktivitasPengguna::class, 'id_user');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}