<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuTerjual;
use App\Models\Menu;

class MenuTerjualController extends Controller
{
    public function index()
    {
        // Mengambil data MenuTerjual
        $menuTerjual = MenuTerjual::with(['menu.bahans.bahanKeluar'])->get();
        $menus = Menu::all();

    
        return view('menu_terjual.index', compact('menuTerjual', 'menus'));
    }


    public function store(Request $request)
{
    $request->validate([
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
            ]);
        }
    }

    return redirect()->route('menu.terjual.index')->with('success');
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