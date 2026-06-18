<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicClipCard extends Model
{
    use HasFactory;

    public const TYPE_GENRE = 'genre';
    public const TYPE_SONG = 'song';

    public const TYPES = [
        self::TYPE_GENRE => 'Жанр',
        self::TYPE_SONG => 'Пісня',
    ];

    protected $fillable = ['type', 'name', 'quantity', 'image_path'];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }

        foreach (['png', 'jpg', 'jpeg', 'webp'] as $extension) {
            $path = "fort/images/music-clip/{$this->id}.{$extension}";

            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return asset('assets/images/file-preview.svg');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
