<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bahan;
use App\Models\Kategori;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\StokMenipisEvent;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::all();
        $user = Auth::user();
        $kategoriBahan = $request->query('kategori_bahan', 'all');

        $query = Bahan::query();

        // Filter berdasarkan kategori bahan (BBAR / BBKTC)
        if ($kategoriBahan === 'BBAR') {
            $query->where('kode_bahan', 'like', 'BBAR%');
        } elseif ($kategoriBahan === 'BBKTC') {
            $query->where('kode_bahan', 'like', 'BBKTC%');
        } else {
            // Filter berdasarkan role user
            $query->where(function ($q) use ($user) {
                if ($user->hasRole(['Admin', 'Headbar', 'Bar'])) {
                    $q->where('kode_bahan', 'LIKE', 'BBAR%');
                }

                if ($user->hasRole(['Admin', 'Headkitchen', 'Kitchen'])) {
                    $q->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
                }
            });
        }

        $bahan = $query->orderBy('status', 'desc')->get();

        // Proses status stok
        foreach ($bahan as $item) {
            if ($item->sisa_stok > $item->batas_minimum) {
                $item->status = '<span class="badge bg-success">AMAN</span>';
            } elseif ($item->sisa_stok > 0) {
                $item->status = '<span class="badge bg-warning text-dark">MENIPIS</span>';
            } else {
                $item->status = '<span class="badge bg-danger">HABIS</span>';
            }
        }

        return view('bahan.daftarbahan', compact('bahan', 'kategoris'));
    }



    public function laporan(Request $request)
    {
        $bahans = collect();
        $message = null;

        if ($request->has(['dari_tanggal', 'sampai_tanggal'])) {
            $dari_tanggal = $request->dari_tanggal . ' 00:00:00';
            $sampai_tanggal = $request->sampai_tanggal . ' 23:59:59';

            $bahans = Bahan::whereBetween('created_at', [$dari_tanggal, $sampai_tanggal])->get();

            if ($bahans->isEmpty()) {
                $message = "Maaf, tidak ada data di tanggal ini";
            }
        }

        return view('laporan.bahan', compact('bahans', 'message'));
    }

    public function downloadPdf()
    {
        $bahans = Bahan::where('kode_bahan', 'LIKE', 'BB%')->get();
        $pdf = Pdf::loadView('laporan.bahan_pdf', compact('bahans'));
        return $pdf->download('laporan_daftar_bahan.pdf');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bahan' => 'required',
            'nama_bahan' => 'required',
            'tipe' => 'required',
            'jenis_bahan' => 'required',
            'kategori_bahan' => 'required',
            'sisa_stok' => 'nullable|integer',
            'satuan' => 'required'
        ]);

        $status = $request->sisa_stok > 2 ? 'AMAN' : ($request->sisa_stok > 0 ? 'MENIPIS' : 'HABIS');

        Bahan::create([
            'kode_bahan' => $request->kode_bahan,
            'nama_bahan' => $request->nama_bahan,
            'tipe' => $request->tipe,
            'jenis_bahan' => $request->jenis_bahan,
            'kategori_bahan' => $request->kategori_bahan,
            'sisa_stok' => $request->sisa_stok ?? 0,
            'satuan' => $request->satuan,
            'status' => $status
        ]);

        return redirect()->back()->with('success');
    }

    public function cekStokMenipis()
    {
        $batasMenipis = 5;
        $bahanMenipis = Bahan::where('sisa_stok', '<=', $batasMenipis)->get();

        foreach ($bahanMenipis as $bahan) {
            event(new StokMenipisEvent($bahan));
        }

        return response()->json(['message' => 'Notifikasi stok menipis dikirim!']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'satuan' => 'required',
            'batas_minimum' => 'required|integer|min:0'
        ]);

        $bahan = Bahan::findOrFail($id);
        $bahan->update($request->only(['nama_bahan', 'satuan', 'batas_minimum']));

        return redirect()->back()->with('success');
    }

    public function destroy($id)
    {
        $bahan = Bahan::findOrFail($id);
        // Hapus semua data bahan_keluar yang terkait
        $bahan->bahanKeluar()->delete();
        $bahan->bahanMasuk()->delete();
        // Hapus data bahan
        $bahan->delete();
        return redirect()->back()->with('success');
    }
}