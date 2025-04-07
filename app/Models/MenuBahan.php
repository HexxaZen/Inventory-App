<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuBahan extends Model
{
    use HasFactory;

    protected $table = 'menu_bahan';
    protected $fillable = ['menu_id', 'bahan_id', 'gramasi'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
}
