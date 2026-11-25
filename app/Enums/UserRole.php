<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Staff = 'staff';
    case Custodian = 'custodian';
    case Admin = 'admin';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.role.'.$this->value);
    }

    public function badgeTone(): string
    {
        return match ($this) {
            self::Student => 'neutral',
            self::Staff => 'approved',
            self::Custodian => 'pending',
            self::Admin => 'brand',
        };
    }
}
