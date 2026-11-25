<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Models\ItemMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ItemMatchController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = ItemMatch::with([
            'lostItem.campusZone',
            'lostItem.reporter',
            'foundItem.campusZone',
            'foundItem.custodyPost',
            'foundItem.reporter',
        ])
            ->where('is_dismissed', false)
            ->whereHas('lostItem', fn ($q) => $q->where('status', ItemStatus::Open->value))
            ->whereHas('foundItem', fn ($q) => $q->where('status', ItemStatus::Open->value));

        if (! $user->isCustodianOrAdmin()) {
            $query->where(function ($sub) use ($user): void {
                $sub->whereHas('lostItem', fn ($q) => $q->where('reporter_id', $user->id))
                    ->orWhereHas('foundItem', fn ($q) => $q->where('reporter_id', $user->id));
            });
        }

        $matches = $query->orderByDesc('score')->paginate(10)->withQueryString();

        return view('matches.index', [
            'matches' => $matches,
        ]);
    }

    public function dismiss(ItemMatch $match): RedirectResponse
    {
        Gate::authorize('dismiss', $match);

        $match->update(['is_dismissed' => true]);

        return redirect()
            ->back()
            ->with('status', __('app.flash.match_dismissed'));
    }
}
