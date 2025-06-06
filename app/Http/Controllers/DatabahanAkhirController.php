<?php

namespace App\Http\Controllers;

use App\Models\BahanAkhir;
use App\Models\Bahan;
use App\Models\BahanKeluar;
use App\Models\BahanProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // 1. Validasi Input: Pastikan semua data yang dibutuhkan ada dan valid.
        $request->validate([
            'tanggal_input' => 'required|date',
            'kode_bahan' => 'required|string|max:50',
            'nama_bahan' => 'required|string|max:100',
            'stok_terakhir' => 'required|numeric|min:0',
        ]);

        // 2. Menggunakan Transaksi Database: Ini sangat penting untuk menjaga integritas data.
        //    Semua operasi database di dalam blok ini akan berhasil semua atau gagal semua.
        DB::transaction(function () use ($request, $id) {
            $bahanAkhir = BahanAkhir::findOrFail($id);

            $tanggalInputBaru = $request->tanggal_input;
            $kodeBahan = $request->kode_bahan;
            $namaBahan = $request->nama_bahan;
            $stokTerakhirBaru = $request->stok_terakhir;

            // Ambil stok sebelumnya dari BahanAkhir yang sedang diupdate
            $stokTerakhirLama = $bahanAkhir->stok_terakhir;

            // 3. Update Data Bahan Akhir: Perbarui entri BahanAkhir itu sendiri.
            $bahanAkhir->update([
                'tanggal_input' => $tanggalInputBaru,
                'kode_bahan' => $kodeBahan,
                'nama_bahan' => $namaBahan,
                'stok_terakhir' => $stokTerakhirBaru,
            ]);

            // 4. Update Stok di Tabel 'Bahans':
            //    Langsung set 'sisa_stok' ke nilai 'stok_terakhir' yang baru.
            //    Ini memastikan 'sisa_stok' selalu mencerminkan data terakhir dari BahanAkhir.
            $bahan = Bahan::where('kode_bahan', $kodeBahan)->first();
            if ($bahan) {
                $bahan->update([
                    'sisa_stok' => $stokTerakhirBaru,
                ]);

                // 5. Logika Penyesuaian Bahan Keluar:
                //    Kita perlu menyesuaikan catatan BahanKeluar berdasarkan perubahan stok.
                $selisihStok = $stokTerakhirBaru - $stokTerakhirLama;

                // Jika stok berkurang (selisih negatif, berarti ada 'keluar' bahan lebih banyak dari sebelumnya)
                if ($selisihStok < 0) {
                    $jumlahKeluarDispensasi = abs($selisihStok); // Ambil nilai absolut (positif)

                    // Cari entri BahanKeluar untuk tanggal dan kode bahan ini
                    $bahanKeluar = BahanKeluar::where('tanggal_keluar', $tanggalInputBaru)
                                                ->where('kode_bahan', $kodeBahan)
                                                ->first();

                    if ($bahanKeluar) {
                        // Jika sudah ada, tambahkan selisih jumlah keluar
                        $bahanKeluar->increment('jumlah_keluar', $jumlahKeluarDispensasi);
                    } else {
                        // Jika belum ada, buat entri BahanKeluar baru
                        BahanKeluar::create([
                            'tanggal_keluar' => $tanggalInputBaru,
                            'kode_bahan' => $kodeBahan,
                            'nama_bahan' => $namaBahan,
                            'jumlah_keluar' => $jumlahKeluarDispensasi,
                            'satuan' => $bahan->satuan, // Ambil satuan dari model Bahan
                            'bahan_masuk_id' => null, // Set null jika tidak terkait dengan BahanMasuk tertentu
                        ]);
                    }
                }
                // Jika stok bertambah (selisih positif, berarti ada 'pengembalian' atau koreksi stok)
                else if ($selisihStok > 0) {
                    $jumlahKembaliDispensasi = abs($selisihStok); // Ambil nilai absolut (positif)

                    // Cari entri BahanKeluar yang sudah ada untuk tanggal dan kode bahan ini
                    $bahanKeluar = BahanKeluar::where('tanggal_keluar', $tanggalInputBaru)
                                                ->where('kode_bahan', $kodeBahan)
                                                ->first();

                    if ($bahanKeluar) {
                        // Jika ada, kurangi jumlah_keluar (pastikan tidak menjadi negatif)
                        $newJumlahKeluar = $bahanKeluar->jumlah_keluar - $jumlahKembaliDispensasi;
                        $bahanKeluar->update([
                            'jumlah_keluar' => max(0, $newJumlahKeluar) // Pastikan jumlah keluar tidak di bawah 0
                        ]);
                    }
                    // Jika tidak ada BahanKeluar untuk dikurangi, kita tidak perlu membuat entri baru
                    // karena penambahan stok biasanya dicatat di BahanMasuk, bukan sebagai pengurangan BahanKeluar.
                }
            } else {
                // Opsional: Handle kasus jika bahan tidak ditemukan (misalnya log error atau throw exception)
                // Ini bisa terjadi jika 'kode_bahan' di BahanAkhir tidak ada di tabel 'bahans'.
                // Untuk saat ini, kita biarkan saja dan hanya memproses jika bahan ditemukan.
            }
        });

        // 6. Redirect dengan Pesan Sukses:
        return redirect()->back()->with('success', 'Data bahan akhir berhasil diperbarui dan stok disesuaikan.');
    }


}
