<?php
// app/Models/User.php
namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'username', 'first_name', 'last_name', 'display_name',
        'email', 'password', 'role', 'points'
    ];

    protected $hidden = ['password'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class);
    }
}
