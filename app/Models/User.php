<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected  $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'email_verified_at',
        'password',
        'tgl_lahir',
        'jk',
        'no_hp',
        'is_admin',
        'foto',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function alamat()
    {
        return $this->hasMany(Alamat::class, 'id_user_alamat');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_user_pesanan');
    }
}
