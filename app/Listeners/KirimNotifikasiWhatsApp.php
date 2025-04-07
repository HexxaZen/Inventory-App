<?php

namespace App\Listeners;

use App\Events\StokMenipis;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;

class KirimNotifikasiWhatsApp implements ShouldQueue
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function handle(StokMenipis $event)
    {
        $bahan = $event->bahan;
        $adminPhone = '6285700498174'; // Nomor admin

        $message = "⚠️ Stok Menipis ⚠️\n\n" .
                   "Bahan: {$bahan->nama_bahan}\n" .
                   "Sisa Stok: {$bahan->sisa_stok} {$bahan->satuan}\n" .
                   "Segera lakukan restock!";

        $this->whatsapp->sendMessage($adminPhone, $message);
    }
}
