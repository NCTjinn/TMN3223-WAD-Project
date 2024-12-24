<?php
// app/Models/Reward.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'user_id', 'mission_id', 'mission_name',
        'status', 'points_earned', 'redeemed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mission()
    {
        return $this->belongsTo(MissionTemplate::class, 'mission_id');
    }
}
