<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login, dan pastikan kolom role bernilai 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // Lolos, silakan masuk ke halaman admin
        }

        // 2. Jika bukan admin, tendang ke halaman beranda biasa dengan pesan error
        return redirect('/')->with('error', 'Anda tidak memiliki hak akses ke halaman ini!');
    }
}