<?php

namespace App\Policies;

use App\Models\ItemMatch;
use App\Models\User;

class ItemMatchPolicy
{
    public function dismiss(User $user, ItemMatch $match): bool
    {
        $match->loadMissing(['lostItem', 'foundItem']);

        return $user->isCustodianOrAdmin()
            || $match->lostItem->reporter_id === $user->id
            || $match->foundItem->reporter_id === $user->id;
    }
}
