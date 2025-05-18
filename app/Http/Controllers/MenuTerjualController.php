<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuTerjual;
use App\Models\Menu;

class MenuTerjualController extends Controller
{
    public function index(Request $request)
{
    // Buat query builder terlebih dahulu
    $query = MenuTerjual::with(['menu.bahans.bahanKeluar']);

    // Filter berdasarkan tanggal jika ada
    if ($request->filled('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    }

    // Urutkan dan ambil datanya
    $menuTerjual = $query->orderBy('tanggal', 'desc')->get();

    // Ambil semua menu untuk ditampilkan di modal tambah
    $menus = Menu::all();

    return view('menu_terjual.index', compact('menuTerjual', 'menus'));
}



    public function store(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'menu_id' => 'required|array',
        'menu_id.*' => 'exists:menu,id',
        'jumlah_terjual' => 'required|array',
        'jumlah_terjual.*' => 'integer',
    ]);

    foreach ($request->menu_id as $index => $menu_id) {
        if ($request->jumlah_terjual[$index] > 0) {
            MenuTerjual::create([
                'menu_id' => $menu_id,
                'jumlah_terjual' => $request->jumlah_terjual[$index],
                'tanggal' => $request->tanggal,
            ]);
        }
    }

    return redirect()->route('menu.terjual.index')->with('success','data berhasil disimpan');
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_terjual' => 'required|integer',
        ]);

        $menuTerjual = MenuTerjual::findOrFail($id);
        $menuTerjual->update(['jumlah_terjual' => $request->jumlah_terjual]);

        return redirect()->route('menu.terjual.index')->with('success');
    }

    public function destroy($id)
    {
        $menuTerjual = MenuTerjual::findOrFail($id);
        $menuTerjual->delete();

        return redirect()->route('menu.terjual.index')->with('success');
    }
}