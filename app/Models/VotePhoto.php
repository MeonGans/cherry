<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'user_id',
        'title',
        'image_path',
        'original_image_path',
        'is_finalist',
        'finalist_selected_at',
        'sort_order',
    ];

    protected $casts = [
        'is_finalist' => 'boolean',
        'finalist_selected_at' => 'datetime',
    ];

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photoVotes()
    {
        return $this->hasMany(PhotoVote::class);
    }

    public function getPrintImagePathAttribute(): string
    {
        return $this->original_image_path ?: $this->image_path;
    }
}
