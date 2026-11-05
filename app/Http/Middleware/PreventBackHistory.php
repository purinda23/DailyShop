<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Menjalankan request ke proses berikutnya untuk mendapatkan response halaman
        $response = $next($request);

        // Menambahkan header HTTP anti-cache sebelum halaman dikirim ke browser pengguna
        // Ini memaksa browser untuk selalu meminta data segar dari server Laravel
        // daripada mengambil rekaman halaman lama dari memori lokal (cache) browser.
        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }
}