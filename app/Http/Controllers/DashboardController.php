<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;

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
        $menu = Menu::with('bahans')->get();
        return view('dashboard', compact('menu'));
    }
    public function indexlaporan()
    {
        return view('laporan.index');
    }

    
}
