<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';

    protected  $primaryKey = 'id_item';

    protected $fillable = [
        'id_menu_item',
        'id_user_item',
        'nota_item',
        'qty',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, "id_menu_item");
    }

}
