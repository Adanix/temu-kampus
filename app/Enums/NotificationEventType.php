<?php

namespace App\Enums;

enum NotificationEventType: string
{
    case MatchFound = 'match_found';
    case ClaimSubmitted = 'claim_submitted';
    case ClaimApproved = 'claim_approved';
    case ClaimRejected = 'claim_rejected';
    case HandoverCompleted = 'handover_completed';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __('enums.notification_event.'.$this->value);
    }

    public function badgeTone(): string
    {
        return match ($this) {
            self::MatchFound => 'brand',
            self::ClaimSubmitted => 'pending',
            self::ClaimApproved => 'approved',
            self::ClaimRejected => 'danger',
            self::HandoverCompleted => 'found',
        };
    }
}
