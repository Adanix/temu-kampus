<?php

namespace App\Enums;

enum DemoPersona: string
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

    public function landingRoute(): string
    {
        return match ($this) {
            self::Student, self::Staff => 'dashboard',
            self::Custodian => 'custodian.desk',
            self::Admin => 'admin.analytics',
        };
    }
}
