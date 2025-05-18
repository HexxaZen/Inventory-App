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
        'jumlah_batch',
        'tipe',
        'satuan',
        'sisa_stok',
        'batas_minimum',
        'kategori_bahan'
    ];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'kode_bahan');
    }

    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'bahan_process_bahan', 'bahan_process_id', 'bahan_id')
            ->withPivot('gramasi')
            ->withTimestamps();
    }

    /**
     * Relasi many-to-many ke BahanProcess lain sebagai komposisi (bahan proses dalam bahan proses)
     */
    public function bahanProcesses()
    {
        return $this->belongsToMany(BahanProcess::class, 'komposisis', 'bahan_process_id', 'bahan_id')
            ->withPivot('gramasi')
            ->withTimestamps();
    }

    /**
     * Relasi hasMany ke komposisi detail, jika kamu ingin pakai model pivot eksplisit
     */
    public function komposisis()
    {
        return $this->hasMany(BahanProcessBahan::class, 'bahan_process_id');
    }
    public function komposisiBahanProses()
    {
        return $this->hasMany(Komposisis::class, 'bahan_process_id');
    }
    // Relasi kebalikannya: bahan proses ini digunakan sebagai komposisi oleh bahan proses lain
    public function digunakanDalam()
    {
        return $this->hasMany(Komposisis::class, 'bahan_id');
    }
    public function bahanMasuk()
    {
        return $this->hasMany(BahanMasuk::class, 'kode_bahan', 'kode_bahan');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    public function stokProses()
{
    return $this->hasOne(StokProses::class, 'bahan_process_id');
}

}
