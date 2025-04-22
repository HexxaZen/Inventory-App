<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanProcessBahan extends Model
{
    protected $table = 'bahan_process_bahan';

    protected $fillable = [
        'bahan_process_id',
        'bahan_id',
        'gramasi',
    ];

    public function bahanProcess()
    {
        return $this->belongsTo(BahanProcess::class, 'bahan_process_id');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
