<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OscarVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'nomination',
        'user_id',
        'nominee_user_id',
        'points',
    ];

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nominee()
    {
        return $this->belongsTo(User::class, 'nominee_user_id');
    }
}
