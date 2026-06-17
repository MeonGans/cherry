<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity', 'value', 'image_path'];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }

        foreach (['png', 'jpg', 'jpeg'] as $extension) {
            $path = "fort/images/products/{$this->id}.{$extension}";

            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return asset('assets/images/file-preview.svg');
    }
}
