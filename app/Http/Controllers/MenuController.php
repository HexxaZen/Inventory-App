<?php
namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Bahan;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $menu = Menu::with('bahans')->get();
        $kategoris = Kategori::where('kode_kategori', 'LIKE', 'BB%')->get();
        $bahans = Bahan::all();

        return view('menu.index', compact('menu', 'kategoris', 'bahans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama_menu' => 'required|string|max:255',
            'bahan_menu' => 'required|array',
            'bahan_menu.*' => 'required|exists:bahans,id',
            'gramasi' => 'required|array',
            'gramasi.*' => 'required|numeric|min:1'
        ]);

        $kategori = Kategori::findOrFail($request->kategori_id);
        $kode_menu = $kategori->kode_kategori . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        $menu = Menu::create([
            'kode_menu' => $kode_menu,
            'nama_menu' => $request->nama_menu,
            'kategori_id' => $request->kategori_id,
            'status_menu' => $this->cekStatusMenu($request->bahan_menu),
        ]);

        $bahanData = [];
        foreach ($request->bahan_menu as $bahanId) {
            $bahanData[$bahanId] = [
                'gramasi' => $request->gramasi[$bahanId]
            ];
        }
        $menu->bahans()->sync($bahanData);

        return redirect()->route('menu.index')->with('success');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'bahan_menu' => 'required|array',
            'bahan_menu.*' => 'required|exists:bahans,id',
            'gramasi' => 'required|array',
            'gramasi.*' => 'required|numeric|min:1'
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update(['nama_menu' => $request->nama_menu]);

        $bahanData = [];
        foreach ($request->bahan_menu as $bahanId) {
            $bahanData[$bahanId] = [
                'gramasi' => $request->gramasi[$bahanId]
            ];
        }
        $menu->bahans()->sync($bahanData);

        return redirect()->route('menu.index')->with('success');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->bahans()->detach();
        $menu->delete();

        return redirect()->route('menu.index')->with('success');
    }

    private function cekStatusMenu($bahanIds)
    {
        $bahanTersedia = Bahan::whereIn('id', $bahanIds)->where('sisa_stok', '>', 0)->count();
        return $bahanTersedia == count($bahanIds) ? 'Tersedia' : 'Tidak Tersedia';
    }
}
