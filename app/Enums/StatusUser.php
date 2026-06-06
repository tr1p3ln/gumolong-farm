<?php

namespace App\Enums;

enum StatusUser: string
{
    case Aktif    = 'aktif';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match($this) {
            self::Aktif    => 'Aktif',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public static function options(): array
    {
        return array_column(
            array_map(fn($case) => ['value' => $case->value, 'label' => $case->label()], self::cases()),
            'label',
            'value'
        );
    }
}
