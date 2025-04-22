<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanMasuk;
use App\Models\Bahan;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\BahanProcess;
use App\Models\BahanKeluar;

class BahanMasukController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bahanMasuk = BahanMasuk::with('bahan')->get();

        $query = Bahan::query();

        if ($user->hasRole(['Admin', 'Headbar', 'Bar'])) {
            $query->where('kode_bahan', 'LIKE', 'BBAR%');
        }

        if ($user->hasRole(['Admin', 'Headkitchen', 'Kitchen'])) {
            $query->orWhere('kode_bahan', 'LIKE', 'BBKTC%');
        }

        $bahans = $query->get();
        $bahan_processes = BahanProcess::all();
        return view('bahan.bahanmasuk', compact('bahanMasuk', 'bahans', 'bahan_processes'));
    }

    public function laporanmasuk(Request $request)
    {
        $bahanMasuk = collect();
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
            'tipe_bahan' => 'required|array',
            'jumlah_masuk' => 'required|array',
            'jumlah_masuk.*' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->bahan_id as $index => $bahan_id) {
                $jumlahMasuk = (int) $request->jumlah_masuk[$index] ?? 0;
                $tipe = $request->tipe_bahan[$index] ?? 'non-proses';

                if ($jumlahMasuk <= 0)
                    continue;

                // Ambil bahan sesuai tipe
                $bahan = $tipe === 'proses'
                    ? BahanProcess::with('komposisis.bahan')->find($bahan_id)
                    : Bahan::find($bahan_id);

                if (!$bahan)
                    continue;

                // Simpan data bahan masuk
                BahanMasuk::create([
                    'tanggal_masuk' => $request->tanggal_masuk,
                    'kode_bahan' => $bahan->kode_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'jumlah_masuk' => $jumlahMasuk,
                    'satuan' => $bahan->satuan,
                    'tipe' => $tipe
                ]);

                // Tambah stok
                $bahan->increment('sisa_stok', $jumlahMasuk);

                // Jika bahan proses, kurangi bahan-bahan komposisi
                if ($tipe === 'proses') {
                    $totalGramasi = $bahan->komposisis->sum('gramasi');

                    if ($totalGramasi > 0) {
                        $rasio = $jumlahMasuk / $totalGramasi;

                        foreach ($bahan->komposisis as $komposisi) {
                            $bahanNonProses = $komposisi->bahan;
                            $jumlahKeluar = $komposisi->gramasi * $rasio;

                            if ($bahanNonProses && $jumlahKeluar > 0) {
                                $bahanNonProses->decrement('sisa_stok', $jumlahKeluar);

                                BahanKeluar::create([
                                    'tanggal_keluar' => $request->tanggal_masuk,
                                    'kode_bahan' => $bahanNonProses->kode_bahan,
                                    'nama_bahan' => $bahanNonProses->nama_bahan,
                                    'jumlah_keluar' => $jumlahKeluar,
                                    'satuan' => $bahanNonProses->satuan
                                ]);
                            }
                        }
                    }
                }
            }
        });

        return redirect()->route('bahan.bahanmasuk')->with('success');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_masuk' => 'required|numeric|min:0',
        ]);

        $bahanMasuk = BahanMasuk::findOrFail($id);
        $oldJumlah = $bahanMasuk->jumlah_masuk;
        $newJumlah = $request->jumlah_masuk;
        $selisih = $newJumlah - $oldJumlah;

        $kodeBahan = $bahanMasuk->kode_bahan;

        $bahan = Bahan::where('kode_bahan', $kodeBahan)->first();

        if ($bahan) {
            // Non-proses
            $bahan->sisa_stok += $selisih;
            $bahan->save();
        } else {
            // Proses
            $bahanProcess = BahanProcess::where('kode_bahan', $kodeBahan)->first();

            if ($bahanProcess) {
                $bahanProcess->sisa_stok += $selisih;
                $bahanProcess->save();

                $totalGramasi = $bahanProcess->komposisis->sum('gramasi');

                if ($totalGramasi > 0) {
                    $oldRasio = $oldJumlah / $totalGramasi;
                    $newRasio = $newJumlah / $totalGramasi;

                    foreach ($bahanProcess->bahans as $bahanBaku) {
                        $gramasi = $bahanBaku->pivot->gramasi;

                        // Rollback bahan yang sebelumnya keluar
                        $rollback = $gramasi * $oldRasio;
                        $bahanBaku->sisa_stok += $rollback;

                        // Kurangi bahan berdasarkan input baru
                        $pengurangan = $gramasi * $newRasio;
                        $bahanBaku->sisa_stok -= $pengurangan;

                        $bahanBaku->save();

                        // Update atau tambah BahanKeluar
                        $keluar = BahanKeluar::where('bahan_masuk_id', $bahanMasuk->id)
                            ->where('bahan_id', $bahanBaku->id)
                            ->first();

                        if ($keluar) {
                            // Update BahanKeluar
                            $keluar->jumlah_keluar += $selisih * $gramasi;
                            $keluar->save();
                        } else {
                            // Tambah BahanKeluar baru
                            BahanKeluar::create([
                                'tanggal_keluar' => now(),
                                'kode_bahan' => $bahanBaku->kode_bahan,
                                'nama_bahan' => $bahanBaku->nama_bahan,
                                'jumlah_keluar' => $selisih * $gramasi,
                                'satuan' => $bahanBaku->satuan,
                                'bahan_masuk_id' => $bahanMasuk->id, // Relasi ke BahanMasuk
                            ]);
                        }
                    }
                }
            }
        }

        $bahanMasuk->jumlah_masuk = $newJumlah;
        $bahanMasuk->save();

        return redirect()->back()->with('success', 'Bahan masuk berhasil diperbarui.');
    }




    public function destroy($id)
    {
        $bahanMasuk = BahanMasuk::findOrFail($id);
        $kodeBahan = $bahanMasuk->kode_bahan;
        $jumlahMasuk = $bahanMasuk->jumlah_masuk;

        $bahan = Bahan::where('kode_bahan', $kodeBahan)->first();

        if ($bahan) {
            // Tipe Non-Proses
            $bahan->sisa_stok = max(0, $bahan->sisa_stok - $jumlahMasuk);
            $bahan->save();
        } else {
            // Tipe Proses
            $bahanProcess = BahanProcess::where('kode_bahan', $kodeBahan)->first();
            if ($bahanProcess) {
                $bahanProcess->sisa_stok = max(0, $bahanProcess->sisa_stok - $jumlahMasuk);
                $bahanProcess->save();

                // Kembalikan stok bahan baku yang dikurangi sebelumnya
                foreach ($bahanProcess->bahans as $bahanBaku) {
                    $gramasi = $bahanBaku->pivot->gramasi;
                    $totalPengaruh = $jumlahMasuk * $gramasi;

                    $bahanBaku->sisa_stok += $totalPengaruh;
                    $bahanBaku->save();

                    // Hapus BahanKeluar terkait
                    BahanKeluar::where('bahan_masuk_id', $bahanMasuk->id)
                        ->where('bahan_id', $bahanBaku->id)
                        ->delete();
                }
            }
        }

        $bahanMasuk->delete();

        return redirect()->route('bahan.bahanmasuk')->with('success');
    }



}
