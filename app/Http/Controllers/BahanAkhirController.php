<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanAkhir;
use App\Models\Bahan;
use App\Models\BahanProcess;
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

    public function index(Request $request)
    {
        $tanggal = session('tanggal_input');
        if (!$tanggal) {
            return redirect()->route('bahan.akhir.tanggal')->with('error', 'Silakan pilih tanggal terlebih dahulu.');
        }

        $user = Auth::user();
        
        // Query bahan non-proses
        $queryNonProses = Bahan::select('id', 'kode_bahan', 'nama_bahan', 'kategori_bahan', 'sisa_stok');
        if ($user->hasRole(['Admin', 'Headbar', 'Bar'])) {
            $queryNonProses->orWhere('kode_bahan', 'LIKE', 'BBAR%');
        }
        if ($user->hasRole(['Admin', 'Headkitchen', 'Kitchen'])) {
            $queryNonProses->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
        }
        $dataNonProses = $queryNonProses->get();

        // Query bahan proses
        $queryProses = BahanProcess::select('id', 'kode_bahan', 'nama_bahan', 'kategori_bahan', 'sisa_stok');
        if ($user->hasRole(['Admin', 'Headbar', 'Bar'])) {
            $queryProses->orWhere('kode_bahan', 'LIKE', 'BBAR%');
        }
        if ($user->hasRole(['Admin', 'Headkitchen', 'Kitchen'])) {
            $queryProses->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
        }
        $dataProses = $queryProses->get();

        return view('bahan.bahanakhir', compact('dataNonProses', 'dataProses', 'tanggal'));
    }

    public function tampilkan(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
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
            $isProses = str_starts_with($id, 'p_');
            $modelId = $isProses ? str_replace('p_', '', $id) : $id;

            $bahan = $isProses ? BahanProcess::find($modelId) : Bahan::find($modelId);
            if (!$bahan)
                continue;

            // Ambil stok hari ini jika sebelumnya sudah pernah disimpan
            $stok_hari_ini = BahanAkhir::where('kode_bahan', $bahan->kode_bahan)
                ->where('tanggal_input', $tanggal)
                ->value('stok_terakhir');

            // Jika belum ada, ambil stok terakhir sebelum hari ini
            if ($stok_hari_ini !== null) {
                $stok_sebelumnya = $stok_hari_ini;
            } else {
                $stok_sebelumnya = BahanAkhir::where('kode_bahan', $bahan->kode_bahan)
                    ->where('tanggal_input', '<', $tanggal)
                    ->orderByDesc('tanggal_input')
                    ->value('stok_terakhir') ?? $bahan->sisa_stok;
            }

            $jumlah_keluar = $stok_sebelumnya - $stok_akhir;

            // Update sisa stok di tabel bahan
            $bahan->update(['sisa_stok' => $stok_akhir]);

            // Update/simpan data bahan akhir
            if ($stok_akhir > 0) {
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
            }


            // Catat atau update bahan keluar hanya jika ada pengurangan stok
            if ($jumlah_keluar > 0) {
                $bahanKeluar = BahanKeluar::where('kode_bahan', $bahan->kode_bahan)
                    ->whereDate('tanggal_keluar', $tanggal)
                    ->first();

                if ($bahanKeluar) {
                    // Update jumlah_keluar jika data sebelumnya sudah ada
                    $bahanKeluar->update([
                        'jumlah_keluar' => $bahanKeluar->jumlah_keluar + $jumlah_keluar,
                    ]);
                } else {
                    // Atau buat data baru jika belum ada
                    BahanKeluar::create([
                        'kode_bahan' => $bahan->kode_bahan,
                        'nama_bahan' => $bahan->nama_bahan,
                        'jumlah_keluar' => $jumlah_keluar,
                        'tanggal_keluar' => $tanggal,
                        'satuan' => $bahan->satuan,
                        'bahan_masuk_id' => null,
                    ]);
                }
            }
        }

        return redirect()->route('bahan.akhir.index')->with('success', 'Data bahan akhir berhasil disimpan.');
    }


}
