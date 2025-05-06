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
    public function index(Request $request)
    {
        $user = Auth::user();
        $filterKategori = $request->input('kategori_bahan'); // Ambil filter dari form GET

        $bahanMasukQuery = BahanMasuk::with('bahan', 'bahanProcess');
        $bahanQuery = Bahan::query();
        $prosesQuery = BahanProcess::query();

        // Role-based filter
        if ($user->hasRole('Admin')) {
            // Admin bisa melihat semua, tapi tetap cek filter kategori jika ada
            if ($filterKategori) {
                $bahanMasukQuery->where('kode_bahan', 'LIKE', $filterKategori . '%');
                $bahanQuery->where('kode_bahan', 'LIKE', $filterKategori . '%');
                $prosesQuery->where('kode_bahan', 'LIKE', $filterKategori . '%');
            }
        } elseif ($user->hasAnyRole(['Headbar', 'Bar'])) {
            $bahanMasukQuery->where('kode_bahan', 'LIKE', 'BBAR%');
            $bahanQuery->where('kode_bahan', 'LIKE', 'BBAR%');
            $prosesQuery->where('kode_bahan', 'LIKE', 'BBAR%');
        } elseif ($user->hasAnyRole(['Headkitchen', 'Kitchen'])) {
            $bahanMasukQuery->where('kode_bahan', 'LIKE', 'BBKTC%');
            $bahanQuery->where('kode_bahan', 'LIKE', 'BBKTC%');
            $prosesQuery->where('kode_bahan', 'LIKE', 'BBKTC%');
        } else {
            $bahanMasuk = collect();
            $bahans = collect();
            $bahan_processes = collect();
            return view('bahan.bahanmasuk', compact('bahanMasuk', 'bahans', 'bahan_processes'));
        }

        $bahanMasuk = $bahanMasukQuery->get();
        $bahans = $bahanQuery->get();
        $bahan_processes = $prosesQuery->get();

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

                if ($tipe === 'proses') {
                    $bahanProses = BahanProcess::with(['komposisis.bahan', 'komposisiBahanProses.bahan'])->find($bahan_id);
                    if (!$bahanProses)
                        continue;

                    // Simpan bahan masuk
                    $bahanMasuk = BahanMasuk::create([
                        'tanggal_masuk' => $request->tanggal_masuk,
                        'kode_bahan' => $bahanProses->kode_bahan,
                        'nama_bahan' => $bahanProses->nama_bahan,
                        'jumlah_masuk' => $jumlahMasuk,
                        'satuan' => $bahanProses->satuan,
                        'tipe' => $tipe
                    ]);

                    // Hitung total gramasi bahan non-proses dari relasi 'komposisis'
                    $totalGramasi = $bahanProses->komposisis->sum('gramasi');
                    $sisaStokBaru = $jumlahMasuk * $totalGramasi;

                    $bahanProses->update([
                        'jumlah_batch' => $jumlahMasuk,
                        'sisa_stok' => $sisaStokBaru
                    ]);

                    // Kurangi stok bahan non-proses dari relasi 'komposisis'
                    foreach ($bahanProses->komposisis as $komposisi) {
                        $bahanNonProses = $komposisi->bahan;
                        $jumlahKeluar = $jumlahMasuk * $komposisi->gramasi;

                        if ($bahanNonProses && $jumlahKeluar > 0) {
                            $bahanNonProses->decrement('sisa_stok', $jumlahKeluar);
                            // Catat ke tabel bahan_keluar
                            BahanKeluar::create([
                                'tanggal_keluar' => now(),
                                'kode_bahan' => $bahanNonProses->kode_bahan,
                                'nama_bahan' => $bahanNonProses->nama_bahan,
                                'jumlah_keluar' => $jumlahKeluar,
                                'satuan' => $bahanNonProses->satuan,
                                'bahan_masuk_id' => $bahanMasuk->id,
                                // 'bahan_id' => $bahanNonProses->id,
                            ]);
                        }
                    }

                    // Kurangi stok bahan proses lain dari relasi 'komposisiBahanProses'
                    foreach ($bahanProses->komposisiBahanProses as $komposisiProses) {
                        $bahanProsesLain = $komposisiProses->bahan;
                        $jumlahKeluar = $jumlahMasuk * $komposisiProses->gramasi;

                        if ($bahanProsesLain && $jumlahKeluar > 0) {
                            $bahanProsesLain->decrement('sisa_stok', $jumlahKeluar);
                            // Catat ke tabel bahan_keluar
                            BahanKeluar::create([
                                'tanggal_keluar' => now(),
                                'kode_bahan' => $bahanProsesLain->kode_bahan,
                                'nama_bahan' => $bahanProsesLain->nama_bahan,
                                'jumlah_keluar' => $jumlahKeluar,
                                'satuan' => $bahanProsesLain->satuan,
                                'bahan_masuk_id' => $bahanMasuk->id,
                                // 'bahan_id' => $bahanProsesLain->id,
                            ]);
                        }
                    }

                } else {
                    $bahan = Bahan::find($bahan_id);
                    if (!$bahan)
                        continue;

                    BahanMasuk::create([
                        'tanggal_masuk' => $request->tanggal_masuk,
                        'kode_bahan' => $bahan->kode_bahan,
                        'nama_bahan' => $bahan->nama_bahan,
                        'jumlah_masuk' => $jumlahMasuk,
                        'satuan' => $bahan->satuan,
                        'tipe' => $tipe
                    ]);

                    $bahan->increment('sisa_stok', $jumlahMasuk);
                }
            }
        });

        return redirect()->route('bahan.bahanmasuk')->with('success', 'Data bahan masuk berhasil disimpan.');
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

        // Cek apakah bahan adalah bahan biasa
        $bahan = Bahan::where('kode_bahan', $kodeBahan)->first();
        if ($bahan) {
            $bahan->sisa_stok += $selisih;
            $bahan->save();
        } else {
            // Jika bukan bahan biasa, cari di bahan proses
            $bahanProses = BahanProcess::where('kode_bahan', $kodeBahan)->first();

            if ($bahanProses) {
                $bahanProses->sisa_stok += $selisih;
                $bahanProses->save();

                $totalGramasi = $bahanProses->komposisis->sum('gramasi');

                if ($totalGramasi > 0) {
                    $oldRasio = $oldJumlah / $totalGramasi;
                    $newRasio = $newJumlah / $totalGramasi;

                    foreach ($bahanProses->bahans as $bahanBaku) {
                        $gramasi = $bahanBaku->pivot->gramasi;

                        // Rollback stok lama
                        $rollback = $gramasi * $oldRasio;
                        $bahanBaku->sisa_stok += $rollback;

                        // Kurangi stok sesuai input baru
                        $pengurangan = $gramasi * $newRasio;
                        $bahanBaku->sisa_stok -= $pengurangan;

                        $bahanBaku->save();

                        // Hitung jumlah_keluar baru
                        $jumlahKeluarBaru = $gramasi * $newRasio;

                        // Update atau buat record BahanKeluar
                        $keluar = BahanKeluar::where('bahan_masuk_id', $bahanMasuk->id)
                            ->where('bahan_id', $bahanBaku->id)
                            ->first();

                        if ($keluar) {
                            $keluar->jumlah_keluar = $jumlahKeluarBaru;
                            $keluar->save();
                        } else {
                            BahanKeluar::create([
                                'tanggal_keluar' => now(),
                                'kode_bahan' => $bahanBaku->kode_bahan,
                                'nama_bahan' => $bahanBaku->nama_bahan,
                                'jumlah_keluar' => $jumlahKeluarBaru,
                                'satuan' => $bahanBaku->satuan,
                                'bahan_masuk_id' => $bahanMasuk->id,
                                // 'bahan_id' => $bahanBaku->id,
                            ]);
                        }
                    }
                }
            }
        }

        // Update jumlah masuk
        $bahanMasuk->jumlah_masuk = $newJumlah;
        $bahanMasuk->save();

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }





    public function destroy($id)
    {
        $bahanMasuk = BahanMasuk::findOrFail($id);
        $kodeBahan = $bahanMasuk->kode_bahan;
        $jumlahMasuk = $bahanMasuk->jumlah_masuk;

        DB::transaction(function () use ($bahanMasuk, $kodeBahan, $jumlahMasuk) {
            $bahan = Bahan::where('kode_bahan', $kodeBahan)->first();

            if ($bahan) {
                // Bahan Non-Proses
                $bahan->sisa_stok = max(0, $bahan->sisa_stok - $jumlahMasuk);
                $bahan->save();
            } else {
                // Bahan Proses
                $bahanProcess = BahanProcess::with('komposisis.bahan')->where('kode_bahan', $kodeBahan)->first();

                if ($bahanProcess) {
                    // Kurangi jumlah_batch
                    $bahanProcess->jumlah_batch = max(0, $bahanProcess->jumlah_batch - $jumlahMasuk);

                    // Hitung ulang sisa_stok
                    $totalGramasi = $bahanProcess->komposisis->sum(function ($komposisi) {
                        return $komposisi->gramasi;
                    });
                    $bahanProcess->sisa_stok = $bahanProcess->jumlah_batch * $totalGramasi;
                    $bahanProcess->save();

                    // Kembalikan stok bahan non-proses (komposisi)
                    foreach ($bahanProcess->komposisis as $komposisi) {
                        $bahanKomposisi = $komposisi->bahan;
                        $jumlahKembali = $jumlahMasuk * $komposisi->gramasi;

                        if ($bahanKomposisi) {
                            $bahanKomposisi->increment('sisa_stok', $jumlahKembali);
                        }
                    }

                    // Kembalikan stok bahan proses lain (komposisi dalam proses)
                    foreach ($bahanProcess->komposisiBahanProses as $komposisi) {
                        $bahanProsesLain = $komposisi->bahan;

                        // Pastikan ini bahan proses (bukan bahan biasa)
                        if ($bahanProsesLain instanceof \App\Models\BahanProcess) {
                            $jumlahKembaliProses = $jumlahMasuk * $komposisi->gramasi;

                            if ($jumlahKembaliProses > 0) {
                                $bahanProsesLain->increment('sisa_stok', $jumlahKembaliProses);
                            }
                        }
                    }

                    // Hapus data bahan keluar yang terkait
                    BahanKeluar::where('bahan_masuk_id', $bahanMasuk->id)->delete();
                }
            }

            // Hapus bahan masuk
            $bahanMasuk->delete();
        });

        return redirect()->route('bahan.bahanmasuk')->with('success', 'Data bahan masuk berhasil dihapus dan stok dikembalikan.');
    }

}
