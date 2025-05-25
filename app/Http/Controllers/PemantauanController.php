<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bahan;
use App\Models\BahanKeluar;
use App\Models\MenuTerjual;
use App\Models\BahanProcess;
use Barryvdh\DomPDF\Facade\Pdf;

class PemantauanController extends Controller
{
    public function index(Request $request)
{
    $dariTanggal = $request->input('dari_tanggal') ? date('Y-m-d 00:00:00', strtotime($request->input('dari_tanggal'))) : null;
    $sampaiTanggal = $request->input('sampai_tanggal') ? date('Y-m-d 23:59:59', strtotime($request->input('sampai_tanggal'))) : null;

    if ($sampaiTanggal && !$dariTanggal) {
        $dariTanggal = Bahan::min('updated_at') ?? $sampaiTanggal;
    }

    if (!$dariTanggal || !$sampaiTanggal) {
        return view('laporan.pemantauan', ['laporan' => collect([])]);
    }

    $menuTerjual = MenuTerjual::with(['menu.bahans', 'menu.bahanProcesses'])->get();
    $bahanData = [];

    // Bahan biasa
    foreach ($menuTerjual as $item) {
        foreach ($item->menu->bahans as $bahan) {
            $kode_bahan = $bahan->kode_bahan;
            $gramasi = $bahan->pivot->gramasi ?? 0;
            $jumlah_terjual = $item->jumlah_terjual;
            $hasil_seharusnya = $jumlah_terjual * $gramasi;

            if (!isset($bahanData[$kode_bahan])) {
                $total_keluar = BahanKeluar::where('kode_bahan', $kode_bahan)
                    ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                    ->sum('jumlah_keluar') ?? 0;

                $bahanData[$kode_bahan] = [
                    'kode_bahan' => $kode_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'total_masuk' => Bahan::where('kode_bahan', $kode_bahan)
                        ->whereBetween('updated_at', [$dariTanggal, $sampaiTanggal])
                        ->sum('sisa_stok') ?? 0,
                    'total_keluar' => $total_keluar,
                    'pemakaian_seharusnya' => 0,
                    'status_pemantauan' => '',
                    'satuan' => $bahan->satuan ?? 'g',
                ];
            }

            $bahanData[$kode_bahan]['pemakaian_seharusnya'] += $hasil_seharusnya;
        }

        // Bahan proses
        foreach ($item->menu->bahanProcesses as $bahanProcess) {
            $kode_bahan = $bahanProcess->kode_bahan;
            $gramasi = $bahanProcess->pivot->gramasi ?? 0;
            $jumlah_terjual = $item->jumlah_terjual;
            $hasil_seharusnya = $jumlah_terjual * $gramasi;

            if (!isset($bahanData[$kode_bahan])) {
                $total_keluar = BahanKeluar::where('kode_bahan', $kode_bahan)
                    ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                    ->sum('jumlah_keluar') ?? 0;

                $bahanData[$kode_bahan] = [
                    'kode_bahan' => $kode_bahan,
                    'nama_bahan' => $bahanProcess->nama_bahan,
                    'total_masuk' => Bahan::where('kode_bahan', $kode_bahan)
                        ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                        ->sum('sisa_stok') ?? 0,
                    'total_keluar' => $total_keluar,
                    'pemakaian_seharusnya' => 0,
                    'status_pemantauan' => '',
                    'satuan' => $bahanProcess->satuan ?? 'g',
                ];
            }

            $bahanData[$kode_bahan]['pemakaian_seharusnya'] += $hasil_seharusnya;
        }
    }

    // Penentuan status pemantauan
    foreach ($bahanData as &$bahan) {
        $selisih = $bahan['total_keluar'] - $bahan['pemakaian_seharusnya'];
        $satuan = $bahan['satuan'];

        if ($selisih == 0) {
            $bahan['status_pemantauan'] = "Balance";
        } elseif ($selisih > 0) {
            $bahan['status_pemantauan'] = "Waste ({$selisih} {$satuan})";
        } else {
            $bahan['status_pemantauan'] = "Plus (+" . abs($selisih) . " {$satuan})";
        }
    }

    return view('laporan.pemantauan', ['laporan' => collect($bahanData)]);
}



    // Fungsi Download PDF
    public function downloadPDF(Request $request)
{
    $dariTanggal = $request->input('dari_tanggal') ? date('Y-m-d 00:00:00', strtotime($request->input('dari_tanggal'))) : null;
    $sampaiTanggal = $request->input('sampai_tanggal') ? date('Y-m-d 23:59:59', strtotime($request->input('sampai_tanggal'))) : null;

    if ($sampaiTanggal && !$dariTanggal) {
        $dariTanggal = Bahan::min('updated_at') ?? $sampaiTanggal;
    }

    if (!$dariTanggal || !$sampaiTanggal) {
        return view('laporan.pemantauan', ['laporan' => collect([])]);
    }

    $menuTerjual = MenuTerjual::with(['menu.bahans', 'menu.bahanProcesses'])->get();
    $bahanData = [];

    // Bahan biasa
    foreach ($menuTerjual as $item) {
        foreach ($item->menu->bahans as $bahan) {
            $kode_bahan = $bahan->kode_bahan;
            $gramasi = $bahan->pivot->gramasi ?? 0;
            $jumlah_terjual = $item->jumlah_terjual;
            $hasil_seharusnya = $jumlah_terjual * $gramasi;

            if (!isset($bahanData[$kode_bahan])) {
                $total_keluar = BahanKeluar::where('kode_bahan', $kode_bahan)
                    ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                    ->sum('jumlah_keluar') ?? 0;

                $bahanData[$kode_bahan] = [
                    'kode_bahan' => $kode_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'total_masuk' => Bahan::where('kode_bahan', $kode_bahan)
                        ->whereBetween('updated_at', [$dariTanggal, $sampaiTanggal])
                        ->sum('sisa_stok') ?? 0,
                    'total_keluar' => $total_keluar,
                    'pemakaian_seharusnya' => 0,
                    'status_pemantauan' => '',
                    'satuan' => $bahan->satuan ?? 'g',
                ];
            }

            $bahanData[$kode_bahan]['pemakaian_seharusnya'] += $hasil_seharusnya;
        }

        // Bahan proses
        foreach ($item->menu->bahanProcesses as $bahanProcess) {
            $kode_bahan = $bahanProcess->kode_bahan;
            $gramasi = $bahanProcess->pivot->gramasi ?? 0;
            $jumlah_terjual = $item->jumlah_terjual;
            $hasil_seharusnya = $jumlah_terjual * $gramasi;

            if (!isset($bahanData[$kode_bahan])) {
                $total_keluar = BahanKeluar::where('kode_bahan', $kode_bahan)
                    ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                    ->sum('jumlah_keluar') ?? 0;

                $bahanData[$kode_bahan] = [
                    'kode_bahan' => $kode_bahan,
                    'nama_bahan' => $bahanProcess->nama_bahan,
                    'total_masuk' => Bahan::where('kode_bahan', $kode_bahan)
                        ->whereBetween('created_at', [$dariTanggal, $sampaiTanggal])
                        ->sum('sisa_stok') ?? 0,
                    'total_keluar' => $total_keluar,
                    'pemakaian_seharusnya' => 0,
                    'status_pemantauan' => '',
                    'satuan' => $bahanProcess->satuan ?? 'g',
                ];
            }

            $bahanData[$kode_bahan]['pemakaian_seharusnya'] += $hasil_seharusnya;
        }
    }

    // Penentuan status pemantauan
    foreach ($bahanData as &$bahan) {
        $selisih = $bahan['total_keluar'] - $bahan['pemakaian_seharusnya'];
        $satuan = $bahan['satuan'];

        if ($selisih == 0) {
            $bahan['status_pemantauan'] = "Balance";
        } elseif ($selisih > 0) {
            $bahan['status_pemantauan'] = "Waste ({$selisih} {$satuan})";
        } else {
            $bahan['status_pemantauan'] = "Plus (+" . abs($selisih) . " {$satuan})";
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
