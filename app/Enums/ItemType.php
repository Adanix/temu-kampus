<?php

namespace App\Enums;

enum ItemType: string
{
    case Lost = 'lost';
    case Found = 'found';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.item_type.'.$this->value);
    }

    public function badgeTone(): string
    {
        return match ($this) {
            self::Lost => 'lost',
            self::Found => 'found',
        };
    }
}
