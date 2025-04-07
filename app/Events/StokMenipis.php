<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StokMenipis
{
    use Dispatchable, SerializesModels;

    public $bahan;

    public function __construct($bahan)
    {
        $this->bahan = $bahan;
    }
}
