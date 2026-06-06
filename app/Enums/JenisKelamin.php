<?php

namespace App\Enums;

enum JenisKelamin: string
{
    case Jantan = 'jantan';
    case Betina = 'betina';

    public function label(): string
    {
        return match($this) {
            self::Jantan => 'Jantan',
            self::Betina => 'Betina',
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
