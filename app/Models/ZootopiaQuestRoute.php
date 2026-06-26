<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ZootopiaQuestRoute extends Model
{
    use HasFactory;

    public const HINT_NUMBERS = [2, 3, 4, 5, 6, 7, 8, 9];

    public const HINT_CODES = [
        2 => '108713',
        3 => '418',
        4 => 'кулебра',
        5 => '43',
        6 => '352',
        7 => 'ідужд',
        8 => 'секретність',
        9 => '248075',
    ];

    public const FINAL_CODE = 'шмиг';

    protected $fillable = [
        'name',
        'agent_code',
        'hint_2',
        'hint_3',
        'hint_4',
        'hint_5',
        'hint_6',
        'hint_7',
        'hint_8',
        'hint_9',
    ];

    public static function normalizeCode(?string $code): string
    {
        return Str::lower(trim((string) $code));
    }

    public static function hintNumberForCode(string $code): ?int
    {
        $normalizedCode = self::normalizeCode($code);

        foreach (self::HINT_CODES as $number => $hintCode) {
            if (self::normalizeCode($hintCode) === $normalizedCode) {
                return $number;
            }
        }

        return null;
    }

    public function hintFor(int $number): ?string
    {
        if (!in_array($number, self::HINT_NUMBERS, true)) {
            return null;
        }

        return $this->getAttribute("hint_{$number}");
    }
}
