<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'claim_code',
    'item_report_id',
    'claimant_id',
    'claimant_answer',
    'proof_photo_path',
    'status',
    'reviewer_id',
    'review_notes',
    'reviewed_at',
    'handover_pin',
    'handed_over_by',
    'handed_over_at',
])]
class ItemClaim extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ClaimStatus::class,
            'reviewed_at' => 'datetime',
            'handed_over_at' => 'datetime',
        ];
    }

    public function itemReport(): BelongsTo
    {
        return $this->belongsTo(ItemReport::class);
    }

    public function claimant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimant_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    public function proofPhotoUrl(): ?string
    {
        if (! is_string($this->proof_photo_path) || trim($this->proof_photo_path) === '') {
            return null;
        }

        return asset('storage/'.ltrim($this->proof_photo_path, '/'));
    }

    public static function generateClaimCode(): string
    {
        $year = now()->year;
        $sequence = ((int) self::max('id')) + 1;

        do {
            $code = sprintf('CLM-%d-%04d', $year, $sequence);
            $sequence++;
        } while (self::where('claim_code', $code)->exists());

        return $code;
    }

    public static function generateHandoverPin(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function formattedPin(): ?string
    {
        if (! is_string($this->handover_pin) || strlen($this->handover_pin) !== 6) {
            return $this->handover_pin;
        }

        return substr($this->handover_pin, 0, 3).'-'.substr($this->handover_pin, 3, 3);
    }
}
