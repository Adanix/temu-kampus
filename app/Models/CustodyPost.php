<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['campus_zone_id', 'name_id', 'name_en', 'operating_hours', 'contact_ext'])]
class CustodyPost extends Model
{
    public function campusZone(): BelongsTo
    {
        return $this->belongsTo(CampusZone::class);
    }

    public function itemReports(): HasMany
    {
        return $this->hasMany(ItemReport::class);
    }

    public function localizedName(): string
    {
        return app()->getLocale() === 'en' ? $this->name_en : $this->name_id;
    }
}
