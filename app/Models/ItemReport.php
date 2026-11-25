<?php

namespace App\Models;

use App\Enums\CustodyType;
use App\Enums\ItemCategory;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ticket_code',
    'reporter_id',
    'type',
    'title',
    'category',
    'campus_zone_id',
    'location_detail',
    'incident_date',
    'color',
    'brand',
    'description',
    'photo_path',
    'is_sensitive',
    'hidden_traits',
    'custody_type',
    'custody_post_id',
    'status',
    'returned_at',
    'archived_at',
])]
class ItemReport extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ItemType::class,
            'category' => ItemCategory::class,
            'custody_type' => CustodyType::class,
            'status' => ItemStatus::class,
            'incident_date' => 'date',
            'is_sensitive' => 'boolean',
            'returned_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function campusZone(): BelongsTo
    {
        return $this->belongsTo(CampusZone::class);
    }

    public function custodyPost(): BelongsTo
    {
        return $this->belongsTo(CustodyPost::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(ItemClaim::class);
    }

    public function lostMatches(): HasMany
    {
        return $this->hasMany(ItemMatch::class, 'lost_item_id');
    }

    public function foundMatches(): HasMany
    {
        return $this->hasMany(ItemMatch::class, 'found_item_id');
    }

    public function hasUploadedOrSeededPhoto(): bool
    {
        return is_string($this->photo_path) && trim($this->photo_path) !== '';
    }

    public function isDemoAssetPhoto(): bool
    {
        return is_string($this->photo_path) && str_starts_with($this->photo_path, 'images/demo/');
    }

    public function photoUrl(): ?string
    {
        if (! $this->hasUploadedOrSeededPhoto()) {
            return null;
        }

        if (str_starts_with((string) $this->photo_path, 'http://') || str_starts_with((string) $this->photo_path, 'https://')) {
            return $this->photo_path;
        }

        if (str_starts_with((string) $this->photo_path, 'images/')) {
            return asset($this->photo_path);
        }

        return asset('storage/'.ltrim((string) $this->photo_path, '/'));
    }

    public function displayPhotoUrl(): string
    {
        return $this->photoUrl() ?? asset($this->category->fallbackPhotoPath());
    }

    public static function generateTicketCode(): string
    {
        $year = now()->year;
        $sequence = ((int) self::max('id')) + 1;

        do {
            $code = sprintf('TK-%d-%04d', $year, $sequence);
            $sequence++;
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }
}
