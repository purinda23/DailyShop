<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use Illuminate\Http\Request;

class EkspedisiController extends Controller
{
    public function index()
    {
        $ekspedisis = Ekspedisi::latest()->get();
        return view('layouts.admin.ekspedisi.data', compact('ekspedisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ekspedisi' => 'required|max:20',
            'nama_ekspedisi' => 'required|max:100',
            'biaya' => 'required|numeric|min:0'
        ]);

        Ekspedisi::create([
            'kode_ekspedisi' => $request->kode_ekspedisi,
            'nama_ekspedisi' => $request->nama_ekspedisi,
            'biaya' => $request->biaya,
        ]);

        return redirect()->back()->with('success', 'Data ekspedisi berhasil ditambahkan');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'kode_ekspedisi' => 'required|max:20',
            'nama_ekspedisi' => 'required|max:100',
            'biaya' => 'required|numeric|min:0'
        ]);

        $ekspedisi = Ekspedisi::findOrFail($id);

        $ekspedisi->update([
            'kode_ekspedisi' => $request->kode_ekspedisi,
            'nama_ekspedisi' => $request->nama_ekspedisi,
            'biaya' => $request->biaya,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diubah');
    }

    public function destroy(int $id)
    {
        Ekspedisi::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}