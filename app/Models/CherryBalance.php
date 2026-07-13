<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CherryBalance extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'user_id', 'amount'];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
