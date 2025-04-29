<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['kode_menu', 'nama_menu', 'kategori_id', 'status_menu'];

    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'menu_bahan', 'menu_id', 'bahan_id')
                    ->withPivot('gramasi')
                    ->withTimestamps();
    }

    public function bahanProcesses()
    {
        return $this->belongsToMany(BahanProcess::class, 'bahan_process_menu', 'menu_id', 'bahan_id')
                    ->withPivot('gramasi')
                    ->withTimestamps();
    }
    public function getStatusMenuAttribute()
    {
        $bahanHabis = $this->bahans->whereNotNull('sisa_stok')->where('sisa_stok', '<=', 0);
        return $bahanHabis->isNotEmpty() ? 'Tidak Tersedia' : 'Tersedia';
    }
}
