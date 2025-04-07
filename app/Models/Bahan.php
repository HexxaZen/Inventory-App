<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahans';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'jenis_bahan',
        'kategori_bahan',
        'sisa_stok',
        'batas_minimum',
        'satuan',
        'status'
    ];

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

    /**
     * Relasi dengan tabel BahanKeluar berdasarkan kode_bahan
     */
    public function bahanKeluar()
    {
        return $this->hasMany(BahanKeluar::class, 'kode_bahan', 'kode_bahan');
    }
}
