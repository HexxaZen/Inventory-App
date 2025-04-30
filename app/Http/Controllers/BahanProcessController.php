<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BahanProcess;
use App\Models\Kategori;
use App\Models\Komposisis;
use Illuminate\Http\Request;
use App\Events\StokMenipisEvent;
use App\Models\BahanProcessKomposisi;

class BahanProcessController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // Ambil semua data untuk Admin
    if ($user->hasRole('Admin')) {
        $bahanProcesses = BahanProcess::with(['bahans'])->get();
        $bahans = Bahan::where('tipe', 'non-process')->get();
    }

    // Khusus Headbar dan Bar: kode_bahan berawalan BBAR
    elseif ($user->hasAnyRole(['Headbar', 'Bar'])) {
        $bahanProcesses = BahanProcess::with(['bahans'])
            ->whereHas('bahans', function ($query) {
                $query->where('kode_bahan', 'LIKE', 'BBAR%');
            })
            ->get();

        $bahans = Bahan::where('tipe', 'non-process')
            ->where('kode_bahan', 'LIKE', 'BBAR%')
            ->get();
    }

    // Khusus Headkitchen dan Kitchen: kode_bahan berawalan BBKTC
    elseif ($user->hasAnyRole(['Headkitchen', 'Kitchen'])) {
        $bahanProcesses = BahanProcess::with(['bahans'])
            ->whereHas('bahans', function ($query) {
                $query->where('kode_bahan', 'LIKE', 'BBKTC%');
            })
            ->get();

        $bahans = Bahan::where('tipe', 'non-process')
            ->where('kode_bahan', 'LIKE', 'BBKTC%')
            ->get();
    }

    // Default jika role tidak dikenal
    else {
        $bahanProcesses = collect();
        $bahans = collect();
    }

    $kategoris = Kategori::all();
    return view('bahan.process', compact('bahanProcesses', 'bahans', 'kategoris'));
}


    public function cekStokMenipis()
    {
        $bahanMenipis = BahanProcess::whereColumn('sisa_stok', '<=', 'batas_minimum')->get();

        foreach ($bahanMenipis as $bahan) {
            event(new StokMenipisEvent($bahan));
        }

        return response()->json(['message' => 'Notifikasi stok menipis dikirim!']);
    }
    public function store(Request $request)
    {
        $request->validate([
            'kode_bahan' => 'required|unique:bahan_processes,kode_bahan',
            'kategori_bahan' => 'required',
            'nama_bahan' => 'required|string',
            'bahan_process' => 'nullable|array',
            'bahan_process.*' => 'exists:bahan_processes,id',
            'gramasi_process' => 'nullable|array',
            'gramasi_process.*' => 'numeric|min:1',
            'satuan' => 'required',
            'bahan_biasa' => 'nullable|array',
            'bahan_biasa.*' => 'exists:bahans,id',
            'gramasi_biasa' => 'nullable|array',
            'gramasi_biasa.*' => 'numeric|min:1',
            'batas_minimum' => 'required|integer|min:0',
        ]);

        // Buat record bahan proses utama
        $bahanProses = BahanProcess::create([
            'kode_bahan' => $request->kode_bahan,
            'kategori_bahan' => $request->kategori_bahan,
            'nama_bahan' => $request->nama_bahan,
            'satuan' => $request->satuan,
            'batas_minimum' => $request->batas_minimum,
        ]);

        // Simpan bahan biasa ke tabel pivot
        if ($request->bahan_biasa && $request->gramasi_biasa) {
            $bahanBiasaData = [];
            foreach ($request->bahan_biasa as $bahanId) {
                if (isset($request->gramasi_biasa[$bahanId])) {
                    $bahanBiasaData[$bahanId] = ['gramasi' => $request->gramasi_biasa[$bahanId]];
                }
            }
            // Menggunakan sync untuk bahan biasa
            $bahanProses->bahans()->sync($bahanBiasaData);
        }

        // Simpan bahan proses ke tabel pivot `bahan_process_komposisi`
        if ($request->bahan_process && $request->gramasi_process) {
            foreach ($request->bahan_process as $bahanProcessId) {
                if (isset($request->gramasi_process[$bahanProcessId])) {
                    Komposisis::create([
                        'bahan_process_id' => $bahanProses->id,
                        'bahan_id' => $bahanProcessId,
                        'gramasi' => $request->gramasi_process[$bahanProcessId],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success');
    }


    public function update(Request $request, $id)
    {
        // Pastikan validasi sesuai dengan input pada form (termasuk 'jumlah_batch' jika memang diperlukan)
        $request->validate([
            'nama_bahan' => 'required|string',
            'satuan' => 'required|string',
            'batas_minimum' => 'required|integer|min:0',
            'bahan_proses' => 'required|array',
            'gramasi' => 'required|array',
            'jumlah_batch' => 'required|numeric' // Tambahkan validasi ini jika field jumlah_batch di form
        ]);

        $bahanProses = BahanProcess::findOrFail($id);

        // Hitung ulang total gramasi
        $totalGramasi = 0;
        foreach ($request->bahan_proses as $bahanId) {
            if (isset($request->gramasi[$bahanId])) {
                $totalGramasi += (int) $request->gramasi[$bahanId];
            }
        }

        // Hitung sisa stok baru
        $sisaStok = $request->jumlah_batch * $totalGramasi;

        // Perbarui data utama bahan proses
        $bahanProses->update([
            'nama_bahan' => $request->nama_bahan,
            'satuan' => $request->satuan,
            'batas_minimum' => $request->batas_minimum,
            'sisa_stok' => $sisaStok
        ]);

        // Persiapkan data pivot: key-nya adalah id bahan, value-nya adalah array attribute pivot (gramasi)
        $dataPivot = [];
        foreach ($request->bahan_proses as $bahanId) {
            if (isset($request->gramasi[$bahanId])) {
                $dataPivot[$bahanId] = ['gramasi' => $request->gramasi[$bahanId]];
            }
        }
        $bahanProses->bahans()->sync($dataPivot);

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $bahanProses = BahanProcess::findOrFail($id);
        $bahanProses->bahans()->detach();
        $bahanProses->delete();

        return redirect()->route('bahan.process')->with('success', 'Data berhasil dihapus.');
    }
}
