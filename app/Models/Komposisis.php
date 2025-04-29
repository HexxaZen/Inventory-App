<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komposisis extends Model
{
    use HasFactory;

    protected $table = 'komposisis';

    protected $fillable = [
        'bahan_process_id',
        'bahan_id',
        'gramasi',
    ];

    /**
     * Relasi ke bahan proses utama yang sedang dibuat.
     */
    public function bahanProsesUtama()
    {
        return $this->belongsTo(BahanProcess::class, 'bahan_process_id');
    }

    /**
     * Relasi ke bahan (bisa bahan proses lain) yang menjadi komposisi.
     */
    public function bahan()
    {
        return $this->belongsTo(BahanProcess::class, 'bahan_id');
    }
}
