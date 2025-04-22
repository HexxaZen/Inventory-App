<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bahan;
use App\Models\BahanAkhir;
use App\Models\BahanKeluar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;



class BahanKeluarController extends Controller
{
    // Menampilkan data bahan keluar
    public function index()
    {
        $dataKeluar = BahanKeluar::all();
        $query = Bahan::query();
        $user = Auth::user();
        if ($user->hasRole([ 'Admin','Headbar','Bar'])) {
            $query->orWhere('kode_bahan', 'LIKE', 'BBAR%');
        }

        if ($user->hasRole([ 'Admin','Headkitchen', 'Kitchen'])) {
            $query->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
        }

        $bahan = $query->get();
        return view('bahan.bahankeluar', compact('dataKeluar'));
    }
    public function laporankeluar(Request $request)
    {
    $dataKeluar = collect(); // Default kosong jika belum ada input tanggal
    $message = null;
    
    if ($request->has(['dari_tanggal', 'sampai_tanggal'])) {
        $dari_tanggal = $request->dari_tanggal . ' 00:00:00';
        $sampai_tanggal = $request->sampai_tanggal . ' 23:59:59';

        $dataKeluar = BahanKeluar::whereBetween('created_at', [$dari_tanggal, $sampai_tanggal])->get();
        
        if ($dataKeluar->isEmpty()) {
            $message = "Maaf, tidak ada data di tanggal ini";
        }
    }
    
    return view('laporan.bahankeluar', compact('dataKeluar', 'message'));
    }

    public function downloadPdfkeluar()
    {
        $dataKeluar = BahanKeluar::where('kode_bahan', 'LIKE', 'BB%')->get();
        $pdf = Pdf::loadView('laporan.bahankeluar_pdf', compact('dataKeluar'));
        return $pdf->download('laporan_bahan_keluar.pdf');
    }
}