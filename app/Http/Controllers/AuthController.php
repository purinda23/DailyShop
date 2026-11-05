<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function index() {
        return view('layouts.auth.login');
    }

    public function register(Request $request) {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Secara default pendaftar baru dari web adalah pembeli
        $user = User::create([
            'name'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
            'role'     => 'pembeli',
        ]);

        // Langsung Login
        Auth::login($user);
        
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Akun berhasil dibuat dan Anda otomatis login!');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // PERBAIKAN: Cek role user terlebih dahulu secara tegas
            if (Auth::user()->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            
            // Jika role-nya pembeli atau user biasa, arahkan ke beranda pembeli
            return redirect()->intended('/');
        }

        return back()->with('error', 'Email atau Password salah!');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // =========================================================================
    // ALUR BARU: FITUR LUPA PASSWORD VIA EMAIL (MAILTRAP SANBOX)
    // =========================================================================

    public function kirimLinkReset(Request $request){
        $request->validate([
        'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Alamat email tidak terdaftar di sistem kami.');
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        $linkReset = url('/password/reset/' . $token . '?email=' . urlencode($request->email));

        Mail::html(
            '
            <h3>Halo Pelanggan DailyShop,</h3>

            <p>Kami menerima permintaan untuk mereset password akun Anda.</p>

            <p>Silakan klik tombol berikut untuk membuat password baru:</p>

            <a href="' . $linkReset . '" 
            style="
                    background:#0d6efd;
                    color:#ffffff;
                    padding:12px 20px;
                    text-decoration:none;
                    border-radius:6px;
                    display:inline-block;
                ">
                Reset Password
            </a>

            <p style="margin-top:20px">
                Jika Anda tidak meminta reset password, abaikan email ini.
            </p>
            ',
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Reset Password Akun DailyShop');
            }
        );

        return back()->with(
            'success',
            'Link reset password berhasil dikirim. Silakan cek email Anda.'
        );

    }

    public function tampilkanHalamanReset(string $token){
        $email = request()->query('email');

        if (!$email) {
            return redirect('/login')
                ->with('error', 'Link reset password tidak valid.');
        }

        return view('layouts.auth.reset_password', [
            'token' => $token,
            'email' => $email
        ]);

    }

    public function simpanPasswordBaru(Request $request){
        $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
        ], ['password.confirmed' => 'Konfirmasi password tidak cocok.']);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return redirect('/login')
                ->with('error', 'Token reset password tidak ditemukan.');
        }

        if (!Hash::check($request->token, $record->token)) {
            return redirect('/login')
                ->with('error', 'Token reset password tidak valid.');
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect('/login')
                ->with('error', 'User tidak ditemukan.');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect('/login')
            ->with('success', 'Password berhasil diperbarui. Silakan login.');
    }
}