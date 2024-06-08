<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'element_id',
        'session_id'
    ];

    public function element()
    {
        return $this->belongsTo(Element::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    // Додайте цей метод для відношення з користувачами
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
