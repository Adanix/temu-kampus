<?php

namespace App\Policies;

use App\Enums\ClaimStatus;
use App\Enums\CustodyType;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Enums\UserRole;
use App\Models\ItemClaim;
use App\Models\ItemReport;
use App\Models\User;

class ItemClaimPolicy
{
    public function create(User $user, ItemReport $item): bool
    {
        return in_array($user->role, [UserRole::Student, UserRole::Staff], true)
            && $item->type === ItemType::Found
            && $item->status === ItemStatus::Open
            && $item->reporter_id !== $user->id;
    }

    public function review(User $user, ItemClaim $claim): bool
    {
        return $user->isCustodianOrAdmin() && $claim->status === ClaimStatus::Pending;
    }

    public function handover(User $user, ItemClaim $claim): bool
    {
        if ($claim->status !== ClaimStatus::Approved) {
            return false;
        }

        if ($user->isCustodianOrAdmin()) {
            return true;
        }

        $claim->loadMissing('itemReport');

        return $claim->itemReport->custody_type === CustodyType::WithFinder
            && $claim->itemReport->reporter_id === $user->id;
    }
}
