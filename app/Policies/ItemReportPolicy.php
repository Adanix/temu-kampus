<?php

namespace App\Policies;

use App\Enums\ClaimStatus;
use App\Enums\ItemStatus;
use App\Models\ItemReport;
use App\Models\User;

class ItemReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ItemReport $item): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ItemReport $item): bool
    {
        return $item->status === ItemStatus::Open
            && ($user->id === $item->reporter_id || $user->isCustodianOrAdmin());
    }

    public function cancel(User $user, ItemReport $item): bool
    {
        if ($item->status !== ItemStatus::Open) {
            return false;
        }

        if ($user->id !== $item->reporter_id && ! $user->isAdmin()) {
            return false;
        }

        return ! $item->claims()
            ->whereIn('status', [ClaimStatus::Approved->value, ClaimStatus::Completed->value])
            ->exists();
    }

    public function archive(User $user, ItemReport $item): bool
    {
        return $user->isCustodianOrAdmin() && $item->status === ItemStatus::Open;
    }

    public function viewHiddenTraits(User $user, ItemReport $item): bool
    {
        return $user->id === $item->reporter_id || $user->isCustodianOrAdmin();
    }

    public function viewSensitivePhoto(User $user, ItemReport $item): bool
    {
        return $user->id === $item->reporter_id || $user->isCustodianOrAdmin();
    }
}
