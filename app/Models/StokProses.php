<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokProses extends Model
{
    use HasFactory;

    protected $table = 'stok_proses'; 

    protected $fillable = [
        'bahan_process_id',
        'stok_hasil',
    ];

    /**
     * Relasi ke model BahanProcess
     */
    public function bahanProses()
    {
        return $this->belongsTo(BahanProcess::class, 'bahan_process_id');
    }
}
