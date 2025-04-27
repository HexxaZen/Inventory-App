<?php
namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use App\Models\Bahan;

$bahans = Bahan::where('sisa_stok', '<=', 5)->get();  // Ambil bahan dengan stok menipis atau habis

// Membuat string pesan untuk stok yang menipis atau habis
$messageContent = "Berikut adalah bahan yang stoknya menipis atau habis:\n\n";

foreach ($bahans as $bahan) {
    $messageContent .= "Nama Bahan: {$bahan->nama_bahan}\n";
    $messageContent .= "Sisa Stok: {$bahan->sisa_stok}\n\n";
}

Mail::raw($messageContent, function ($message) {
    $message->to('your@email.com')
            ->subject('Notifikasi Stok Menipis/Habis');
});
