<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu'; // Pastikan sesuai dengan tabel di database
    protected $primaryKey = 'id'; // Primary key tabel
    public $timestamps = true; // Aktifkan timestamps jika digunakan

    protected $fillable = ['kode_menu', 'nama_menu', 'kategori_id', 'status_menu'];

    /**
     * Relasi Many-to-Many dengan model Bahan
     */
    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'menu_bahan', 'menu_id', 'bahan_id')
                    ->withPivot('gramasi')
                    ->withTimestamps(); // Jika tabel pivot memiliki timestamps
    }

    /**
     * Aksesori untuk mendapatkan status menu berdasarkan stok bahan
     */
    public function getStatusMenuAttribute()
    {
        $bahanHabis = $this->bahans->whereNotNull('sisa_stok')->where('sisa_stok', '<=', 0);
        return $bahanHabis->isNotEmpty() ? 'Tidak Tersedia' : 'Tersedia';
    }
}
