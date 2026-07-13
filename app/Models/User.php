<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_id',
        'phone_number',
        'date_of_birth',
        'liceum_id',
        'team_id',
        'desired_team_id',
        'gender',
        'pin_code',
        'image_path',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $extension) {
            $path = "images/users/{$this->id}.{$extension}";

            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return asset('assets/images/user-profile.jpeg');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function liceum()
    {
        return $this->belongsTo(Liceum::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function desiredTeam()
    {
        return $this->belongsTo(Team::class, 'desired_team_id');
    }

    public function votePhotos()
    {
        return $this->hasMany(VotePhoto::class);
    }

    public function cherryBalances()
    {
        return $this->hasMany(CherryBalance::class);
    }
}
