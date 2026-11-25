<?php

namespace App\Enums;

enum ClaimStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.claim_status.'.$this->value);
    }

    public function badgeTone(): string
    {
        return match ($this) {
            self::Pending => 'pending',
            self::Approved => 'approved',
            self::Rejected => 'danger',
            self::Completed => 'found',
        };
    }
}
