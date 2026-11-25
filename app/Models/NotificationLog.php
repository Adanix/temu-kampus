<?php

namespace App\Models;

use App\Enums\NotificationEventType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'event_type',
    'channel',
    'delivery_mode',
    'status',
    'subject_id',
    'subject_en',
    'body_id',
    'body_en',
    'related_item_id',
    'related_claim_id',
    'read_at',
])]
class NotificationLog extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_type' => NotificationEventType::class,
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedItem(): BelongsTo
    {
        return $this->belongsTo(ItemReport::class, 'related_item_id');
    }

    public function relatedClaim(): BelongsTo
    {
        return $this->belongsTo(ItemClaim::class, 'related_claim_id');
    }

    /**
     * Scope visible simulated notification logs according to user role.
     *
     * @param  Builder<NotificationLog>  $query
     * @return Builder<NotificationLog>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isCustodian()) {
            return $query->where(function (Builder $sub) use ($user): void {
                $sub->where('user_id', $user->id)
                    ->orWhereIn('event_type', [
                        NotificationEventType::ClaimSubmitted->value,
                        NotificationEventType::HandoverCompleted->value,
                    ]);
            });
        }

        return $query->where('user_id', $user->id);
    }

    public function localizedSubject(): string
    {
        return app()->getLocale() === 'en' ? $this->subject_en : $this->subject_id;
    }

    public function localizedBody(): string
    {
        return app()->getLocale() === 'en' ? $this->body_en : $this->body_id;
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
