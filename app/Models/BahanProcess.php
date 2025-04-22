<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'tipe',
        'satuan',
        'sisa_stok',
        'batas_minimum',
        'kategori_bahan'
    ];

    /**
     * Relasi many-to-many ke model Bahan dengan pivot gramasi
     */
    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'bahan_process_bahan', 'bahan_process_id', 'bahan_id')
                    ->withPivot('gramasi')
                    ->withTimestamps();
    }
    public function komposisis()
    {
        return $this->hasMany(\App\Models\BahanProcessBahan::class, 'bahan_process_id');
    }    
    public function bahanMasuk()
    {
        return $this->hasMany(BahanMasuk::class, 'kode_bahan', 'kode_bahan');
    }
    /**
     * Relasi ke Kategori (many-to-one)
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
