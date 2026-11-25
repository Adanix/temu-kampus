<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name_id', 'name_en', 'zone_group'])]
class CampusZone extends Model
{
    public function custodyPosts(): HasMany
    {
        return $this->hasMany(CustodyPost::class);
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
