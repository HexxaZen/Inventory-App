<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $bahan;

    public function __construct($bahan)
    {
        $this->bahan = $bahan;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ Peringatan: Stok Bahan Menipis')
            ->greeting('Halo, Admin!')
            ->line('Bahan berikut hampir habis:')
            ->line('🛒 Nama: ' . $this->bahan->nama_bahan)
            ->line('📉 Sisa Stok: ' . $this->bahan->sisa_stok . ' ' . $this->bahan->satuan)
            ->line('📌 Batas Minimum: ' . $this->bahan->batas_minimum . ' ' . $this->bahan->satuan)
            ->action('Cek Stok', url('/dashboard/bahan'))
            ->line('Segera lakukan restock sebelum kehabisan.')
            ->salutation('Terima kasih, Inventory App');
    }
}
