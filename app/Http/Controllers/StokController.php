<?php

namespace App\Http\Controllers;

use App\Mail\StokMenipisNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\Bahan;

class StokController extends Controller
{
    public function kirimNotifikasiStok()
    {
        // Ambil bahan dengan stok <= 5 (atau habis)
        $bahans = Bahan::where('sisa_stok', '<=', 5)->get();

        // Jika ada bahan dengan stok menipis atau habis, kirim email
        if ($bahans->count()) {
            Mail::to('owner@coffeeshop.com')->send(new StokMenipisNotification($bahans));
            return 'Email notifikasi stok berhasil dikirim!';
        } else {
            return 'Tidak ada stok yang menipis atau habis.';
        }
    }
}
