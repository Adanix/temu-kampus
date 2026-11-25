<?php

namespace App\Enums;

enum ItemStatus: string
{
    case Open = 'open';
    case ClaimApproved = 'claim_approved';
    case Returned = 'returned';
    case UnclaimedArchived = 'unclaimed_archived';
    case Cancelled = 'cancelled';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.item_status.'.$this->value);
    }

    public function badgeTone(): string
    {
        return match ($this) {
            self::Open => 'found',
            self::ClaimApproved => 'approved',
            self::Returned => 'neutral',
            self::UnclaimedArchived => 'neutral',
            self::Cancelled => 'danger',
        };
    }
}
