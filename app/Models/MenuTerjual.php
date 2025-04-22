<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuTerjual extends Model
{
    use HasFactory;

    protected $table = 'menu_terjual';
    protected $fillable = ['menu_id', 'jumlah_terjual'];

    /**
     * Relasi ke model Menu.
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class)->with('bahans'); // Pastikan mengambil bahan terkait
    }

    /**
     * Relasi ke bahan melalui tabel pivot menu_bahan.
     * Menghubungkan bahan yang digunakan dalam menu yang dijual.
     */
    public function bahans()
    {
        return $this->hasManyThrough(
            Bahan::class,          // Model target (Bahan)
            MenuBahan::class,      // Model perantara (MenuBahan)
            'menu_id',             // Foreign key di tabel perantara (MenuBahan)
            'id',                  // Foreign key di tabel target (Bahan)
            'menu_id',             // Local key di tabel saat ini (MenuTerjual)
            'bahan_id'             // Local key di tabel perantara (MenuBahan)
        )->withPivot('gramasi');   // Mengambil kolom pivot (gramasi)
    }
    public function bahanProcesses()
    {
        return $this->belongsToMany(BahanProcess::class, 'bahan_process_menu', 'menu_id', 'bahan_id')
                    ->withPivot('gramasi')
                    ->withTimestamps();
    }
    /**
     * Relasi ke tabel bahan_keluar berdasarkan bahan yang digunakan dalam menu.
     */
    public function bahanKeluar()
    {
        return $this->hasManyThrough(
            BahanKeluar::class,    // Model target (BahanKeluar)
            MenuBahan::class,      // Model perantara (MenuBahan)
            'menu_id',             // Foreign key di tabel perantara (MenuBahan)
            'bahan_id',            // Foreign key di tabel target (BahanKeluar)
            'menu_id',             // Local key di tabel saat ini (MenuTerjual)
            'bahan_id'             // Local key di tabel perantara (MenuBahan)
        );
    }
}
