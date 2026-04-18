<?php

namespace App\Enums;

enum RoleUser: string
{
    case Admin            = 'admin';
    case KepalaKandang    = 'kepala_kandang';
    case PengurusKandang  = 'pengurus_kandang';

    public function label(): string
    {
        return match($this) {
            self::Admin           => 'Admin',
            self::KepalaKandang   => 'Kepala Kandang',
            self::PengurusKandang => 'Pengurus Kandang',
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
