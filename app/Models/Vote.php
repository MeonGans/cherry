<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    public const TYPE_TEAM = 'team';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_OSCAR = 'oscar';

    public const OSCAR_NOMINATIONS = [
        'best_director' => [
            'title' => 'Кращий режисер',
            'limit' => 1,
            'gender' => null,
        ],
        'best_actor' => [
            'title' => 'Краща чоловіча роль',
            'limit' => 3,
            'gender' => 'male',
        ],
        'best_actress' => [
            'title' => 'Краща жіноча роль',
            'limit' => 3,
            'gender' => 'female',
        ],
        'best_editing' => [
            'title' => 'Кращий монтаж',
            'limit' => 1,
            'gender' => null,
        ],
        'best_camera' => [
            'title' => 'Кращий оператор',
            'limit' => 1,
            'gender' => null,
        ],
    ];

    protected $fillable = ['name', 'vote_url', 'type', 'session_id'];

    public function votes()
    {
        return $this->hasMany(UserVote::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function photos()
    {
        return $this->hasMany(VotePhoto::class)->orderBy('sort_order');
    }

    public function photoVotes()
    {
        return $this->hasMany(PhotoVote::class);
    }

    public function oscarVotes()
    {
        return $this->hasMany(OscarVote::class);
    }

    public function isPhotoVote(): bool
    {
        return $this->type === self::TYPE_PHOTO;
    }

    public function isOscarVote(): bool
    {
        return $this->type === self::TYPE_OSCAR;
    }
}
