<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected  $primaryKey = 'nota';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nota',
        'id_user_pesanan',
        'id_alamat_pesanan',
        'total_harga',
        'status_pesanan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_pesanan');
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class, 'id_alamat_pesanan');
    }
}
