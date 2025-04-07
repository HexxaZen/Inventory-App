<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBahanBakuController extends Controller
{
    public function keseluruhanBahanBaku(Request $request)
    {
        $laporan = collect(); // Default kosong jika belum ada input tanggal
        $message = null;

        if ($request->has(['dari_tanggal', 'sampai_tanggal'])) {
            $dari_tanggal = $request->dari_tanggal . ' 00:00:00';
            $sampai_tanggal = $request->sampai_tanggal . ' 23:59:59';

            $laporan = DB::table('bahans')
            ->leftJoin('bahan_masuk', 'bahans.kode_bahan', '=', 'bahan_masuk.kode_bahan')
            ->leftJoin('bahan_keluar', 'bahans.kode_bahan', '=', 'bahan_keluar.kode_bahan')
            ->leftJoin('bahan_akhir', 'bahans.kode_bahan', '=', 'bahan_akhir.kode_bahan')
            ->select(
                'bahans.kode_bahan',
                'bahans.nama_bahan',
                DB::raw('SUM(bahan_masuk.jumlah_masuk) as total_masuk'),
                DB::raw('SUM(bahan_keluar.jumlah_keluar) as total_keluar'),
                'bahan_akhir.stok_terakhir',
                'bahan_akhir.tanggal_input as tanggal_terakhir',
                'bahans.sisa_stok'
            )
            ->groupBy('bahans.kode_bahan', 'bahans.nama_bahan', 'bahan_akhir.stok_terakhir','bahan_akhir.tanggal_input', 'bahans.sisa_stok')
            ->get();

            if ($laporan->isEmpty()) {
                $message = "Maaf, tidak ada data di tanggal ini";
            }
        }

        return view('laporan.keseluruhan-bahan-baku', compact('laporan', 'message'));
    }
    public function downloadPDF(Request $request)
    {
        $dari_tanggal = $request->dari_tanggal . ' 00:00:00';
        $sampai_tanggal = $request->sampai_tanggal . ' 23:59:59';

        $laporan = DB::table('bahans')
            ->leftJoin('bahan_masuk', 'bahans.kode_bahan', '=', 'bahan_masuk.kode_bahan')
            ->leftJoin('bahan_keluar', 'bahans.kode_bahan', '=', 'bahan_keluar.kode_bahan')
            ->leftJoin('bahan_akhir', 'bahans.kode_bahan', '=', 'bahan_akhir.kode_bahan')
            ->select(
                'bahans.kode_bahan',
                'bahans.nama_bahan',
                DB::raw('SUM(bahan_masuk.jumlah_masuk) as total_masuk'),
                DB::raw('SUM(bahan_keluar.jumlah_keluar) as total_keluar'),
                'bahan_akhir.stok_terakhir',
                'bahan_akhir.tanggal_input as tanggal_terakhir',
                'bahans.sisa_stok'
            )
            ->groupBy('bahans.kode_bahan', 'bahans.nama_bahan', 'bahan_akhir.stok_terakhir','bahan_akhir.tanggal_input', 'bahans.sisa_stok')
            ->get();

        $pdf = PDF::loadView('laporan.keseluruhanbb_pdf', compact('laporan'));
        return $pdf->download('laporan_bahan_baku.pdf');
    }
    
}
