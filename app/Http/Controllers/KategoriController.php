<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'kode_kategori' => 'required|unique:kategoris',
        'keterangan' => 'nullable',
    ]);

    // Mapping kode kategori ke nama kategori
    $namaKategoriMap = [
        'BBAR' => 'Bahan Baku Bar',
        'BBKTC' => 'Bahan Baku Kitchen',
        'INVB' => 'Inventaris Bar',
        'INVK' => 'Inventaris Kitchen',
        'INVO' => 'Inventaris Operasional',
    ];

    // Ambil nama kategori berdasarkan kode yang dipilih
    $nama_kategori = $namaKategoriMap[$request->kode_kategori] ?? 'Kategori Tidak Diketahui';

    // Simpan kategori
    Kategori::create([
        'kode_kategori' => $request->kode_kategori,
        'nama_kategori' => $nama_kategori,
        'keterangan' => $request->keterangan,
    ]);

    return redirect()->route('kategori.index')->with('success');
}


    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'kode_kategori' => 'required|unique:kategoris,kode_kategori,' . $kategori->id,
            'nama_kategori' => 'required',
            'keterangan' => 'nullable',
        ]);

        $kategori->update($request->all());
        return redirect()->route('kategori.index')->with('success');
    }

    public function destroy(Kategori $kategori)
    {
        $kategori->delete();
        return redirect()->route('kategori.index')->with('success');
    }
}