<?php

namespace App\Http\Controllers;

use App\Models\BahanAkhir;
use App\Models\Bahan;
use App\Models\BahanKeluar;
use Illuminate\Http\Request;

class DatabahanAkhirController extends Controller
{
    public function index()
    {
        // Mengambil data bahan akhir yang diperlukan
        $data = BahanAkhir::all();
        return view('bahan.databahanakhir', compact('data'));
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
        if (!$bahan) continue;

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
        if ($stok_akhir > 0){
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
public function updateSingle(Request $request, $id)
{
    $request->validate([
        'tanggal_input' => 'required|date',
        'kode_bahan' => 'required|string|max:50',
        'nama_bahan' => 'required|string|max:100',
        'stok_terakhir' => 'required|numeric|min:0',
    ]);

    $bahanAkhir = BahanAkhir::findOrFail($id);

    $tanggal = $request->tanggal_input;
    $kodeBahan = $request->kode_bahan;
    $stokBaru = $request->stok_terakhir;

    // Ambil stok sebelumnya (sebelum diubah)
    $stokLama = $bahanAkhir->stok_terakhir;

    // Hitung selisih untuk jumlah_keluar
    $jumlahKeluar = $stokLama - $stokBaru;

    // Update data bahan akhir
    $bahanAkhir->update([
        'tanggal_input' => $tanggal,
        'kode_bahan' => $kodeBahan,
        'nama_bahan' => $request->nama_bahan,
        'stok_terakhir' => $stokBaru,
    ]);

    // Update stok di tabel Bahan
    $bahan = \App\Models\Bahan::where('kode_bahan', $kodeBahan)->first();
    if ($bahan) {
        $bahan->update([
            'sisa_stok' => $stokBaru,
        ]);
    }

    // Catat jumlah keluar jika ada pengurangan
    if ($jumlahKeluar > 0) {
        $bahanKeluar = \App\Models\BahanKeluar::where('kode_bahan', $kodeBahan)
            ->whereDate('tanggal_keluar', $tanggal)
            ->first();

        if ($bahanKeluar) {
            // Update jumlah_keluar jika data sebelumnya sudah ada
            $bahanKeluar->update([
                'jumlah_keluar' => $bahanKeluar->jumlah_keluar + $jumlahKeluar,
            ]);
        } else {
            // Atau buat data baru jika belum ada
            \App\Models\BahanKeluar::create([
                'kode_bahan' => $kodeBahan,
                'nama_bahan' => $request->nama_bahan,
                'jumlah_keluar' => $jumlahKeluar,
                'tanggal_keluar' => $tanggal,
                'satuan' => $bahan?->satuan,
                'bahan_masuk_id' => null,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Data bahan akhir berhasil diperbarui.');
}


}
