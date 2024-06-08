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
        'gender',
        'pin_code'
    ];

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
}
