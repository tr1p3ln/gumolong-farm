<?php

namespace App\Enums;

enum StatusDomba: string
{
    case Hidup   = 'hidup';
    case Mati    = 'mati';
    case Dijual  = 'dijual';

    public function label(): string
    {
        return match($this) {
            self::Hidup  => 'Hidup',
            self::Mati   => 'Mati',
            self::Dijual => 'Dijual',
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
