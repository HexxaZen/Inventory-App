<?php

namespace App\Http\Controllers;

use App\Models\StokProses;
use Illuminate\Http\Request;

class StokProsesController extends Controller
{
    /**
     * Tampilkan daftar stok proses.
     */
    public function index()
    {
        // Ambil semua data stok proses beserta relasi bahan proses
        $stokProsesList = StokProses::with('bahanProses')->get();

        // Kirim ke view (ganti dengan path view yang sesuai)
        return view('bahan.stokprocess', compact('stokProsesList'));
    }
}
