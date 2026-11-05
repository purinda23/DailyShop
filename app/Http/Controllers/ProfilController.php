<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    // Menampilkan halaman profil pembeli
    public function index()
    {
        $user = Auth::user();
        return view('layouts.pembeli.profil', compact('user'));
    }

    // Memproses update data profil utama (Nama, Email, Telepon)
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        // Validasi data inputan dari form profil
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'telepon' => 'nullable|string|max:15',
        ]);

        // Proses update ke database
        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'telepon' => $request->telepon,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // MEMPROSES TAMBAH ALAMAT BARU (DITAMBAH MENGGUNAKAN SEPARATOR |||)
    public function storeAlamat(Request $request)
    {
        // Validasi input dari modal tambah alamat
        $request->validate([
            'label'           => 'required|string',
            'nama_penerima'   => 'required|string|max:255',
            'telepon_penerima'=> 'required|string|max:15',
            'alamat_lengkap'  => 'required|string',
        ]);

        $user = User::find(Auth::id());

        // Format teks alamat gabungan rapi untuk disimpan
        $alamatBaru = "[" . $request->label . "] " . $request->nama_penerima . " (" . $request->telepon_penerima . ") - " . $request->alamat_lengkap;

        // Cek apakah user sudah memiliki alamat sebelumnya
        if (!empty($user->alamat)) {
            // Gabungkan alamat lama dan baru dengan pembatas ||| agar tidak saling menumpuk
            $alamatFormatBaru = $user->alamat . "|||" . $alamatBaru;
        } else {
            // Jika alamat masih kosong
            $alamatFormatBaru = $alamatBaru;
        }

        // Update kolom alamat di tabel users
        $user->update([
            'alamat' => $alamatFormatBaru
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan!');
    }

    // MEMPROSES UPDATE/UBAH ALAMAT YANG SUDAH ADA
    public function updateAlamat(Request $request)
    {
        $request->validate([
            'label'           => 'required|string',
            'nama_penerima'   => 'required|string|max:255',
            'telepon_penerima'=> 'required|string|max:15',
            'alamat_lengkap'  => 'required|string',
            'alamat_index'    => 'required|integer', // Index alamat mana yang mau diubah
        ]);

        $user = User::find(Auth::id());

        // Format string alamat hasil perubahan baru
        $alamatEdit = "[" . $request->label . "] " . $request->nama_penerima . " (" . $request->telepon_penerima . ") - " . $request->alamat_lengkap;

        // Pecah semua list alamat lama menjadi array
        $listAlamat = explode('|||', $user->alamat);
        $index = $request->alamat_index;

        // Ganti baris alamat pada index yang sesuai dengan data baru
        if (isset($listAlamat[$index])) {
            $listAlamat[$index] = $alamatEdit;
        }

        // Gabungkan kembali array alamat menjadi satu string dipisahkan |||
        $alamatFormatBaru = implode('|||', $listAlamat);

        // Update ke database tabel users
        $user->update([
            'alamat' => $alamatFormatBaru
        ]);

        return back()->with('success', 'Alamat berhasil diperbarui!');
    }
}