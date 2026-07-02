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

    public function getElementLogoPathAttribute(): string
    {
        $name = mb_strtolower(trim(($this->element?->name ?? '') . ' ' . $this->name));

        $logos = [
            'вогонь' => 'assets/images/elements/1.png',
            'повітря' => 'assets/images/elements/2.png',
            'вода' => 'assets/images/elements/3.png',
            'земля' => 'assets/images/elements/4.png',
            'метал' => 'assets/images/elements/5.png',
        ];

        foreach ($logos as $needle => $path) {
            if (str_contains($name, $needle)) {
                return $path;
            }
        }

        $fallbackByElementId = [
            2 => 'assets/images/elements/1.png',
            4 => 'assets/images/elements/2.png',
            5 => 'assets/images/elements/3.png',
            3 => 'assets/images/elements/4.png',
        ];

        return $fallbackByElementId[(int) $this->element_id] ?? 'assets/images/file-preview.svg';
    }

    public function getElementLogoUrlAttribute(): string
    {
        return asset($this->element_logo_path);
    }

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
