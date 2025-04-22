<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanProcessMenu extends Model
{
    use HasFactory;
    protected $table = 'bahan_process_menu';
    protected $fillable = ['menu_id', 'bahan_id', 'gramasi'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    public function bahanProcess()
    {
        return $this->belongsTo(BahanProcess::class);
}
}
