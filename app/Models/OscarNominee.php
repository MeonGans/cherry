<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OscarNominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'nomination',
        'user_id',
    ];

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
