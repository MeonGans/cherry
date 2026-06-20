<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WednesdayQuestRoute extends Model
{
    use HasFactory;

    public const HINT_NUMBERS = [2, 3, 4, 5, 6, 7, 8, 9];

    public const HINT_CODES = [
        2 => '10125',
        3 => '579',
        4 => '150',
        5 => '2364',
        6 => '412',
        7 => '217',
        8 => '53781',
        9 => '81325',
    ];

    public const SAFE_CODE = '888';

    protected $fillable = [
        'name',
        'victim_code',
        'hint_2',
        'hint_3',
        'hint_4',
        'hint_5',
        'hint_6',
        'hint_7',
        'hint_8',
        'hint_9',
    ];

    public function hintFor(int $number): ?string
    {
        if (!in_array($number, self::HINT_NUMBERS, true)) {
            return null;
        }

        return $this->getAttribute("hint_{$number}");
    }
}
