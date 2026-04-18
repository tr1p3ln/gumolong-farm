<?php

namespace App\Enums;

enum StatusPerkawinan: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Bunting            = 'bunting';
    case TidakBunting       = 'tidak_bunting';

    public function label(): string
    {
        return match($this) {
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Bunting            => 'Bunting',
            self::TidakBunting       => 'Tidak Bunting',
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
