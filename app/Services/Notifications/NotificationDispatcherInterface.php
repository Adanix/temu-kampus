<?php

namespace App\Services\Notifications;

use App\Enums\NotificationEventType;
use App\Models\NotificationLog;
use App\Models\User;

interface NotificationDispatcherInterface
{
    public function dispatch(
        User $recipient,
        NotificationEventType $eventType,
        string $subjectId,
        string $subjectEn,
        string $bodyId,
        string $bodyEn,
        ?int $relatedItemId = null,
        ?int $relatedClaimId = null,
    ): ?NotificationLog;
}
