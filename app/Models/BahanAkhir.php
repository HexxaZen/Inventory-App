<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanAkhir extends Model
{
    use HasFactory;
    protected $table = 'bahan_akhir';
    protected $fillable = ['tanggal_input', 'kode_bahan','kategori_bahan', 'nama_bahan', 'stok_terakhir'];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'kode_bahan', 'kode_bahan');
    }
}
