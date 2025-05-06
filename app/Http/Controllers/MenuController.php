<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Bahan;
use App\Models\BahanProcess;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $menu = Menu::with('bahans', 'bahanProcesses')->get();
        $kategoris = Kategori::where('kode_kategori', 'LIKE', 'BB%')->get();
        $bahans = Bahan::all();
        $bahanProcesses = BahanProcess::all();

        return view('menu.index', compact('menu', 'kategoris', 'bahans', 'bahanProcesses'));
    }

    public function store(Request $request)
    { 
        // Validasi input
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_menu' => 'required|string|max:255',
            'bahan_biasa' => 'nullable|array',
            'bahan_biasa.*' => 'exists:bahans,id',
            'gramasi_biasa' => 'nullable|array',
            'gramasi_biasa.*' => 'numeric|min:1',
            'bahan_process' => 'nullable|array',
            'bahan_process.*' => 'exists:bahan_processes,id',
            'gramasi_process' => 'nullable|array',
            'gramasi_process.*' => 'numeric|min:1',
        ]);

        // Mendapatkan data kategori dan membuat kode_menu
        $kategori = Kategori::findOrFail($request->kategori_id);
        $kode_menu = $kategori->kode_kategori . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        // Membuat menu baru
        $menu = Menu::create([
            'kode_menu' => $kode_menu,
            'nama_menu' => $request->nama_menu,
            'kategori_id' => $request->kategori_id,
            'status_menu' => $this->cekStatusMenu(array_merge($request->bahan_biasa ?? [], $request->bahan_process ?? [])),
        ]);

        // Menyimpan bahan biasa jika ada
        if ($request->bahan_biasa) {
            $bahanBiasaData = [];
            foreach ($request->bahan_biasa as $bahanId) {
                if (isset($request->gramasi_biasa[$bahanId])) {
                    $bahanBiasaData[$bahanId] = ['gramasi' => $request->gramasi_biasa[$bahanId]];
                }
            }            
            if (!empty($bahanBiasaData)) {
                $menu->bahans()->sync($bahanBiasaData);
            }
        }

        // Menyimpan bahan process jika ada
        if ($request->bahan_process) {
            $bahanProcessData = [];
            foreach ($request->bahan_process as $bahanId) {
                if (isset($request->gramasi_process[$bahanId])) {
                    $bahanProcessData[$bahanId] = ['gramasi' => $request->gramasi_process[$bahanId]];
                }
            }            
            if (!empty($bahanProcessData)) {
                $menu->bahanProcesses()->sync($bahanProcessData);
            }
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('menu.index')->with('success');
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_menu' => 'required|string|max:255',
        'bahan_biasa' => 'nullable|array',
        'bahan_biasa.*' => 'exists:bahans,id',
        'gramasi_biasa' => 'nullable|array',
        'gramasi_biasa.*' => 'nullable|numeric',
        'bahan_process' => 'nullable|array',
        'bahan_process.*' => 'exists:bahan_processes,id',
        'gramasi_process' => 'nullable|array',
        'gramasi_process.*' => 'nullable|numeric',
    ]);

    $menu = Menu::findOrFail($id);
    $menu->update([
        'nama_menu' => $request->nama_menu,
    ]);

    // Update bahan biasa
    $bahanBiasaData = [];
    if ($request->filled('bahan_biasa')) {
        foreach ($request->bahan_biasa as $bahanId) {
            $gramasi = $request->gramasi_biasa[$bahanId] ?? null;
            $bahanBiasaData[$bahanId] = ['gramasi' => $gramasi ?? 0];
        }
    }
    $menu->bahans()->sync($bahanBiasaData);

    // Update bahan process
    $bahanProcessData = [];
    if ($request->filled('bahan_process')) {
        foreach ($request->bahan_process as $bahanId) {
            $gramasi = $request->gramasi_process[$bahanId] ?? null;
            $bahanProcessData[$bahanId] = ['gramasi' => $gramasi ?? 0];
        }
    }
    $menu->bahanProcesses()->sync($bahanProcessData);

    return redirect()->route('menu.index')->with('success');
}




    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->bahans()->detach();
        $menu->bahanProcesses()->detach(); // Pastikan bahan proses juga dihapus
        $menu->delete();

        return redirect()->route('menu.index')->with('success');
    }

    private function cekStatusMenu($bahanIds)
    {
        $bahanTersedia = Bahan::whereIn('id', $bahanIds)->where('sisa_stok', '>', 0)->count();
        return $bahanTersedia == count($bahanIds) ? 'Tersedia' : 'Tidak Tersedia';
    }
}
