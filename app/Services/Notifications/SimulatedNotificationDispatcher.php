<?php

namespace App\Services\Notifications;

use App\Enums\NotificationEventType;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class SimulatedNotificationDispatcher implements NotificationDispatcherInterface
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
    ): ?NotificationLog {
        try {
            $status = blank($recipient->email) ? 'failed' : 'simulated_sent';

            return NotificationLog::create([
                'user_id' => $recipient->id,
                'event_type' => $eventType,
                'channel' => 'email_in_app',
                'delivery_mode' => 'simulated',
                'status' => $status,
                'subject_id' => $subjectId,
                'subject_en' => $subjectEn,
                'body_id' => $bodyId,
                'body_en' => $bodyEn,
                'related_item_id' => $relatedItemId,
                'related_claim_id' => $relatedClaimId,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Simulated notification dispatch failed', [
                'user_id' => $recipient->id,
                'event_type' => $eventType->value,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }
}
