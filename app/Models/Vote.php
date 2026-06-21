<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    public const TYPE_TEAM = 'team';
    public const TYPE_PHOTO = 'photo';

    protected $fillable = ['name', 'vote_url', 'type'];

    public function votes()
    {
        return $this->hasMany(UserVote::class);
    }

    public function photos()
    {
        return $this->hasMany(VotePhoto::class)->orderBy('sort_order');
    }

    public function photoVotes()
    {
        return $this->hasMany(PhotoVote::class);
    }

    public function isPhotoVote(): bool
    {
        return $this->type === self::TYPE_PHOTO;
    }
}
