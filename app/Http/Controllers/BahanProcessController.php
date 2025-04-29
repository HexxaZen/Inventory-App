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
        $bahanProcesses = BahanProcess::with(['bahans'])->get();
        $bahans = Bahan::where('tipe', 'non-process')->get();
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
        $request->validate([
            'nama_bahan' => 'required|string',
            'satuan' => 'required|string',
            'batas_minimum' => 'required|integer|min:0',
            'bahan_process' => 'required|array',
            'gramasi' => 'required|array'
        ]);

        $bahanProses = BahanProcess::findOrFail($id);

        // Hitung ulang total gramasi
        $totalGramasi = 0;
        foreach ($request->bahan_process as $bahanId) {
            if (isset($request->gramasi[$bahanId])) {
                $totalGramasi += (int) $request->gramasi[$bahanId];
            }
        }

        // Hitung sisa stok baru
        $sisaStok = $request->jumlah_batch * $totalGramasi;

        $bahanProses->update([
            'nama_bahan' => $request->nama_bahan,
            'satuan' => $request->satuan,
            'batas_minimum' => $request->batas_minimum,
            'sisa_stok' => $sisaStok
        ]);

        // Update pivot bahan dan gramasi
        $dataPivot = [];
        foreach ($request->bahan_process as $bahanId) {
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
