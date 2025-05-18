<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokProcessMenu extends Model
{
    use HasFactory;
    protected $table = 'stok_process_menu';
    protected $fillable = ['menu_id', 'bahan_id', 'gramasi'];
    
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
