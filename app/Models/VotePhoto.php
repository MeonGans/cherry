<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'title',
        'image_path',
        'sort_order',
    ];

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function photoVotes()
    {
        return $this->hasMany(PhotoVote::class);
    }
}
