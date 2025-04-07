<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanMasuk;
use App\Models\Bahan;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;



class BahanMasukController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bahanMasuk = BahanMasuk::with('bahan')->get();

        // Filter bahan berdasarkan peran pengguna
        $query = Bahan::query();

        if ($user->hasRole([ 'Admin','Headbar', 'Bar'])) {
            $query->where('kode_bahan', 'LIKE', 'BBAR%');
        }

        if ($user->hasRole([ 'Admin', 'Headkitchen', 'Kitchen'])) {
            $query->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
        }

        $bahans = $query->get();

        return view('bahan.bahanmasuk', compact('bahanMasuk', 'bahans'));
    }
    public function laporanmasuk(Request $request)
    {
    $bahanMasuk = collect(); // Default kosong jika belum ada input tanggal
    $message = null;
    
    if ($request->has(['dari_tanggal', 'sampai_tanggal'])) {
        $dari_tanggal = $request->dari_tanggal . ' 00:00:00';
        $sampai_tanggal = $request->sampai_tanggal . ' 23:59:59';

        $bahanMasuk = BahanMasuk::whereBetween('created_at', [$dari_tanggal, $sampai_tanggal])->get();
        
        if ($bahanMasuk->isEmpty()) {
            $message = "Maaf, tidak ada data di tanggal ini";
        }
    }
    
    return view('laporan.bahanmasuk', compact('bahanMasuk', 'message'));
    }

    public function downloadPdfmasuk()
    {
        $bahanMasuk = BahanMasuk::where('kode_bahan', 'LIKE', 'BB%')->get();
        $pdf = Pdf::loadView('laporan.bahanmasuk_pdf', compact('bahanMasuk'));
        return $pdf->download('laporan_bahan_masuk.pdf');
    }

public function store(Request $request)
{
    $request->validate([
        'tanggal_masuk' => 'required|date',
        'bahan_id' => 'required|array',
        'jumlah_masuk' => 'required|array',
        'jumlah_masuk.*' => 'integer',
    ]);


    DB::transaction(function () use ($request) {
        foreach ($request->bahan_id as $index => $bahan_id) {
            $jumlahMasuk = $request->jumlah_masuk[$index];

            // Skip jika jumlah masuk 0
            if ($jumlahMasuk <= 0) {
                continue;
            }

            $bahan = Bahan::find($bahan_id);

            if (!$bahan) {
                continue; // Lewati bahan yang tidak ditemukan
            }

            // Simpan data bahan masuk
            BahanMasuk::create([
                'tanggal_masuk' => $request->tanggal_masuk,
                'kode_bahan' => $bahan->kode_bahan,
                'nama_bahan' => $bahan->nama_bahan,
                'jumlah_masuk' => $jumlahMasuk,
            ]);


            // Update stok bahan
            $bahan->increment('sisa_stok', $request->jumlah_masuk[$index]);
        }
    });
        return redirect()->route('bahan.bahanmasuk')->with('success');
    }

    public function destroy($id)
    {
        $bahanMasuk = BahanMasuk::findOrFail($id);

        // Ambil bahan terkait
        $bahan = Bahan::where('kode_bahan', $bahanMasuk->kode_bahan)->first();

        // Kurangi stok jika bahan ditemukan
        if ($bahan) {
            $bahan->sisa_stok = max(0, $bahan->sisa_stok - $bahanMasuk->jumlah_masuk);
            $bahan->save();
        }

        // Hapus data bahan masuk
        $bahanMasuk->delete();

        return redirect()->route('bahan.bahanmasuk')->with('success');
    }
}
