<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanAkhir;
use App\Models\Bahan;
use App\Models\BahanKeluar;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
class BahanAkhirController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showTanggalInput()
    {
        return view('bahan.bahanakhir');
    }

    public function prosesTanggal(Request $request)
    {
        $tanggalHariIni = now()->format('Y-m-d');
        session(['tanggal_input' => $tanggalHariIni]);
        return $this->index();
    }

    public function index()
    {
        $tanggal = session('tanggal_input');
        if (!$tanggal) {
            return redirect()->route('bahan.akhir.tanggal')->with('error', 'Silakan pilih tanggal terlebih dahulu.');
        }

        $user = Auth::user();
        $query = Bahan::select('id', 'kode_bahan', 'kategori_bahan', 'nama_bahan', 'sisa_stok');

        // Cek role menggunakan Spatie
        if ($user->hasRole(['Admin', 'Bar'])) {
            $query->orWhere('kode_bahan', 'LIKE', 'BBAR%');
        }

        if ($user->hasRole(['Admin', 'Kitchen'])) {
            $query->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
        }

        $data = $query->get();

        return view('bahan.bahanakhir', compact('data', 'tanggal'));
    }
    public function tampilkan(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        // Ambil data bahan_akhir berdasarkan tanggal input
        $data = BahanAkhir::whereDate('tanggal_input', $tanggal)->get();

        return view('bahan.databahanakhir', compact('data', 'tanggal'));
    }

    public function laporan(Request $request)
    {
        $data = collect();
        $message = null;

        if ($request->has(['dari_tanggal', 'sampai_tanggal'])) {
            $dari_tanggal = $request->dari_tanggal . ' 00:00:00';
            $sampai_tanggal = $request->sampai_tanggal . ' 23:59:59';

            $data = BahanAkhir::whereBetween('created_at', [$dari_tanggal, $sampai_tanggal])->get();

            if ($data->isEmpty()) {
                $message = "Maaf, tidak ada data di tanggal ini";
            }
        }

        return view('laporan.bahanakhir', compact('data', 'message'));
    }

    public function downloadPdf()
    {
        $data = BahanAkhir::where('kode_bahan', 'LIKE', 'BB%')->get();
        $pdf = Pdf::loadView('laporan.bahanakhir_pdf', compact('data'));
        return $pdf->download('laporan_daftar_bahan_akhir.pdf');
    }
    public function update(Request $request)
    {
        $request->validate([
            'sisa_stok' => 'required|array',
            'sisa_stok.*' => 'required|integer|min:0',
        ]);

        $tanggal = session('tanggal_input');
        if (!$tanggal) {
            return redirect()->route('bahan.akhir.tanggal')->with('error', 'Silakan pilih tanggal terlebih dahulu.');
        }

        foreach ($request->sisa_stok as $id => $stok_akhir) {
            $bahan = Bahan::find($id);
            if (!$bahan) {
                return redirect()->route('bahan.akhir.index')->with('error', "Bahan dengan ID $id tidak ditemukan.");
            }

            $jumlah_keluar = $bahan->sisa_stok - $stok_akhir;
            $bahan->update(['sisa_stok' => $stok_akhir]);

            BahanAkhir::updateOrCreate(
                [
                    'tanggal_input' => $tanggal,
                    'kode_bahan' => $bahan->kode_bahan,
                ],
                [
                    'kategori_bahan' => $bahan->kategori_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'stok_terakhir' => $stok_akhir,
                ]
            );

            if ($jumlah_keluar > 0) {
                BahanKeluar::create([
                    'bahan_id' => $bahan->id,
                    'kode_bahan' => $bahan->kode_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'jumlah_keluar' => $jumlah_keluar,
                    'tanggal_keluar' => $tanggal,
                    'satuan' => $bahan->satuan,
                ]);
            }
        }

        return redirect()->route('bahan.akhir.index')->with('success');
    }
}
