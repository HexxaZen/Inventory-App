<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StokMenipisNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bahans;

    public function __construct($bahans)
    {
        $this->bahans = $bahans;
    }

    public function build()
    {
        return $this->subject('Peringatan: Stok Bahan Menipis atau Habis')
                    ->markdown('emails.stok_menipis');
    }
}
