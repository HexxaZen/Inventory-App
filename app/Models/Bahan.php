<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahans';

    const TIPE_PROCESS = 'process';
    const TIPE_NON_PROCESS = 'non-process';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'tipe',
        'jenis_bahan',
        'kategori_bahan',
        'sisa_stok',
        'batas_minimum',
        'satuan',
        'status'
    ];

    public function scopeProcess($query)
    {
        return $query->where('tipe', self::TIPE_PROCESS);
    }

    public function scopeNonProcess($query)
    {
        return $query->where('tipe', self::TIPE_NON_PROCESS);
    }

    /**
     * Relasi dengan tabel Menu melalui tabel pivot menu_bahan
     */
    public function menu()
    {
        return $this->belongsToMany(Menu::class, 'menu_bahan')
                    ->withPivot('gramasi')
                    ->withTimestamps();
    }

    /**
     * Relasi dengan tabel MenuTerjual melalui tabel pivot menu_bahan
     * Digunakan untuk menghitung bahan yang digunakan dalam menu terjual
     */
    public function menuTerjual()
    {
        return $this->belongsToMany(MenuTerjual::class, 'menu_bahan')
                    ->withPivot('gramasi')
                    ->withTimestamps();
    }

    public function bahanProcess()
{
    return $this->hasOne(BahanProcess::class, 'kode_bahan'); // Sesuaikan dengan foreign key
}

    public function bahanKeluar()
    {
        return $this->hasMany(BahanKeluar::class, 'kode_bahan', 'kode_bahan');
    }
    public function bahanMasuk()
    {
        return $this->hasMany(BahanMasuk::class,'kode_bahan','kode_bahan');
    }
}
