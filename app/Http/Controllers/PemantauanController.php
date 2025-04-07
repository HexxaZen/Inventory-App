<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanMasuk;
use App\Models\BahanKeluar;
use App\Models\MenuTerjual;
use Barryvdh\DomPDF\Facade\Pdf;


class PemantauanController extends Controller
{
    public function index(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        // Jika hanya satu tanggal yang diisi, set dariTanggal ke tanggal paling awal di database
        if ($sampaiTanggal && !$dariTanggal) {
            $dariTanggal = BahanMasuk::min('created_at') ?? $sampaiTanggal;
        }

        if (!$dariTanggal || !$sampaiTanggal) {
            return view('laporan.pemantauan', ['laporan' => collect([])]);
        }

        $menuTerjual = MenuTerjual::with(['menu.bahans'])->get();
        
        $bahanData = [];

        foreach ($menuTerjual as $item) {
            foreach ($item->menu->bahans as $bahan) {
                $kode_bahan = $bahan->kode_bahan;
                $gramasi = $bahan->pivot->gramasi ?? 0;
                $jumlah_terjual = $item->jumlah_terjual;
                $hasil_seharusnya = $jumlah_terjual * $gramasi;

                if (!isset($bahanData[$kode_bahan])) {
                    $bahanData[$kode_bahan] = [
                        'kode_bahan' => $kode_bahan,
                        'nama_bahan' => $bahan->nama_bahan,
                        'total_masuk' => BahanMasuk::where('kode_bahan', $kode_bahan)
                            ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                            ->sum('jumlah_masuk') ?? 0,
                        'total_keluar' => BahanKeluar::where('kode_bahan', $kode_bahan)
                            ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                            ->sum('jumlah_keluar') ?? 0,
                        'pemakaian_seharusnya' => 0,
                        'status_pemantauan' => "Balance",
                        'satuan' => $bahan->satuan ?? 'g',
                    ];
                }

                $bahanData[$kode_bahan]['pemakaian_seharusnya'] += $hasil_seharusnya;
            }
        }

        foreach ($bahanData as &$bahan) {
            $selisih = $bahan['total_keluar'] - $bahan['pemakaian_seharusnya'];

            if ($selisih == 0) {
                $bahan['status_pemantauan'] = "Balance";
            } elseif ($selisih > 0) {
                $bahan['status_pemantauan'] = "Minus: -{$selisih} {$bahan['satuan']}";
            } else {
                $bahan['status_pemantauan'] = "Plus: +" . abs($selisih) . " {$bahan['satuan']}";
            }
        }

        return view('laporan.pemantauan', ['laporan' => collect($bahanData)]);
    }
    // Fungsi Download PDF
    public function downloadPDF(Request $request)
    {
        $dariTanggal = $request->input('dari_tanggal');
        $sampaiTanggal = $request->input('sampai_tanggal');

        if (!$dariTanggal || !$sampaiTanggal) {
            return redirect()->back()->with('error', 'Harap pilih rentang tanggal terlebih dahulu.');
        }

        $menuTerjual = MenuTerjual::with(['menu.bahans'])->get();
        $bahanData = [];

        foreach ($menuTerjual as $item) {
            foreach ($item->menu->bahans as $bahan) {
                $kode_bahan = $bahan->kode_bahan;
                $gramasi = $bahan->pivot->gramasi ?? 0;
                $jumlah_terjual = $item->jumlah_terjual;
                $hasil_seharusnya = $jumlah_terjual * $gramasi;

                if (!isset($bahanData[$kode_bahan])) {
                    $bahanData[$kode_bahan] = [
                        'kode_bahan' => $kode_bahan,
                        'nama_bahan' => $bahan->nama_bahan,
                        'total_masuk' => BahanMasuk::where('kode_bahan', $kode_bahan)
                            ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                            ->sum('jumlah_masuk') ?? 0,
                        'total_keluar' => BahanKeluar::where('kode_bahan', $kode_bahan)
                            ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                            ->sum('jumlah_keluar') ?? 0,
                        'pemakaian_seharusnya' => 0,
                        'status_pemantauan' => "Balance",
                        'satuan' => $bahan->satuan ?? 'g',
                    ];
                }

                $bahanData[$kode_bahan]['pemakaian_seharusnya'] += $hasil_seharusnya;
            }
        }

        foreach ($bahanData as &$bahan) {
            $selisih = $bahan['total_keluar'] - $bahan['pemakaian_seharusnya'];

            if ($selisih == 0) {
                $bahan['status_pemantauan'] = "Balance";
            } elseif ($selisih > 0) {
                $bahan['status_pemantauan'] = "Minus: -{$selisih} {$bahan['satuan']}";
            } else {
                $bahan['status_pemantauan'] = "Plus: +" . abs($selisih) . " {$bahan['satuan']}";
            }
        }

        $pdf = PDF::loadView('laporan.pemantauan_pdf', [
            'laporan' => collect($bahanData),
            'dariTanggal' => $dariTanggal,
            'sampaiTanggal' => $sampaiTanggal
        ]);

        return $pdf->download('Laporan_Pemantauan_Bahan_' . $dariTanggal . '_to_' . $sampaiTanggal . '.pdf');
    }
}
