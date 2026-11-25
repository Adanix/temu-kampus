<?php

namespace App\Enums;

enum CustodyType: string
{
    case WithFinder = 'with_finder';
    case AtCustodyPost = 'at_custody_post';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.custody_type.'.$this->value);
    }
}
