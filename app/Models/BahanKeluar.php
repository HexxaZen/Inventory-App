<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanKeluar extends Model
{
    use HasFactory;

    protected $table = 'bahan_keluar';

    protected $fillable = [
        'tanggal_keluar',
        'kode_bahan',
        'nama_bahan',
        'jumlah_keluar',
        'satuan',
        // 'bahan_id', // Foreign key ke tabel Bahan
    ];

    /**
     * Relasi ke model Bahan.
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    /**
     * Accessor untuk menghitung hasil_seharusnya berdasarkan jumlah terjual dan gramasi.
     */
    public function getHasilSeharusnyaAttribute()
    {
        if (!$this->bahan || !$this->bahan->menus) {
            return 0;
        }

        return $this->bahan->menus->sum(function ($menu) {
            return optional($menu->pivot)->gramasi * ($menu->menuTerjual->sum('jumlah_terjual') ?? 0);
        });
    }

    /**
     * Accessor untuk menghitung hasil_akhir.
     */
    public function getHasilAkhirAttribute()
    {
        $hasil_seharusnya = $this->hasil_seharusnya;
        $jumlah_keluar = $this->jumlah_keluar ?? 0;

        if ($jumlah_keluar == $hasil_seharusnya) {
            return 'Balance';
        } elseif ($jumlah_keluar > $hasil_seharusnya) {
            return 'Minus: ' . abs($jumlah_keluar - $hasil_seharusnya) . ' ' . $this->satuan;
        } else {
            return 'Plus: +' . abs($hasil_seharusnya - $jumlah_keluar) . ' ' . $this->satuan;
        }
    }
}
