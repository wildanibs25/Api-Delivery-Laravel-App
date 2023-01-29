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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';

    protected  $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'tgl_lahir',
        'jk',
        'no_hp',
        'is_admin',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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