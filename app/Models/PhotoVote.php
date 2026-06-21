<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoVote extends Model
{
    use HasFactory;

    public const SOURCE_USER = 'user';
    public const SOURCE_JURY = 'jury';

    protected $fillable = [
        'vote_id',
        'vote_photo_id',
        'user_id',
        'source',
        'points',
    ];

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function photo()
    {
        return $this->belongsTo(VotePhoto::class, 'vote_photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
