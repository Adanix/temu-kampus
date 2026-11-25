<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['lost_item_id', 'found_item_id', 'score', 'match_reasons', 'is_dismissed'])]
class ItemMatch extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'match_reasons' => 'array',
            'is_dismissed' => 'boolean',
        ];
    }

    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(ItemReport::class, 'lost_item_id');
    }

    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(ItemReport::class, 'found_item_id');
    }
}
