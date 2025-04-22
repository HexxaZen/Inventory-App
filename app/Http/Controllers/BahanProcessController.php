<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BahanProcess;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Events\StokMenipisEvent;

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
            'kode_bahan'     => 'required',
            'kategori_bahan' => 'required',
            'nama_bahan'     => 'required|string',
            'sisa_stok'      => 'nullable|integer',
            'bahan_process'  => 'required|array',
            'satuan'         => 'required',
            'gramasi'        => 'required|array',
            'batas_minimum' => 'required|integer|min:0'
        ]);

        $sisaStok = $request->sisa_stok ?? 0;
        $status = $sisaStok > $request->batas_minimum ? 'AMAN' : ($sisaStok > 0 ? 'MENIPIS' : 'HABIS');

        $bahanProses = BahanProcess::create([
            'kode_bahan'     => $request->kode_bahan,
            'kategori_bahan' => $request->kategori_bahan,
            'nama_bahan'     => $request->nama_bahan,
            'sisa_stok'      => $sisaStok,
            'satuan'         => $request->satuan,
            'status'         => $status,
            'batas_minimum' => $request->batas_minimum
        ]);

        $dataPivot = [];
        foreach ($request->bahan_process as $bahanId) {
            if (isset($request->gramasi[$bahanId])) {
                $dataPivot[$bahanId] = ['gramasi' => $request->gramasi[$bahanId]];
            }
        }

        $bahanProses->bahans()->sync($dataPivot);

        return redirect()->back()->with('success');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bahan'     => 'required|string',
            'satuan'         => 'required|string',
            'batas_minimum'  => 'required|integer|min:0',
            'bahan_process'  => 'required|array',
            'gramasi'        => 'required|array'
        ]);

        $bahanProses = BahanProcess::findOrFail($id);

        $status = $bahanProses->sisa_stok > $request->batas_minimum ? 'AMAN' : ($bahanProses->sisa_stok > 0 ? 'MENIPIS' : 'HABIS');

        $bahanProses->update([
            'nama_bahan'     => $request->nama_bahan,
            'satuan'         => $request->satuan,
            'batas_minimum'  => $request->batas_minimum,
            'status'         => $status
        ]);

        $dataPivot = [];
        foreach ($request->bahan_process as $bahanId) {
            if (isset($request->gramasi[$bahanId])) {
                $dataPivot[$bahanId] = ['gramasi' => $request->gramasi[$bahanId]];
            }
        }

        $bahanProses->bahans()->sync($dataPivot);

        return redirect()->back()->with('success');
    }

    public function destroy($id)
    {
        $bahanProses = BahanProcess::findOrFail($id);
        $bahanProses->bahans()->detach();
        $bahanProses->delete();

        return redirect()->route('bahan.process')->with('success');
    }
}
