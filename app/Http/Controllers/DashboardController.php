<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\Bahan;

class DashboardController extends Controller
{
    public function index()
    {
        
        $user = Auth::user();

        // Peran yang diizinkan untuk mengakses dashboard
        $roles = [ 'Admin', 'Bar', 'Kitchen'];

        if ($user->hasAnyRole($roles)) {
            return redirect()->route('dashboard');
        }

        return abort(403, 'Unauthorized access');
    }
    public function dashboard()
{
    $user = Auth::user();

    // Ambil semua bahan low stock terlebih dahulu
    $query = Bahan::whereColumn('sisa_stok', '<=', 'batas_minimum');

    // Filter berdasarkan role
    if ($user->hasRole(['Admin', 'Headbar', 'Bar'])) {
        $query->where('kode_bahan', 'LIKE', 'BBAR%');
    } elseif ($user->hasRole(['Admin', 'Headkitchen', 'Kitchen'])) {
        $query->where('kode_bahan', 'LIKE', 'BBKTC%');
    }

    $bahan_low_stock = $query->get();
    $menu = Menu::with('bahans')->get();

    return view('dashboard', compact('menu', 'bahan_low_stock'));
}
    public function indexlaporan()
    {
        return view('laporan.index');
    }

    
}
