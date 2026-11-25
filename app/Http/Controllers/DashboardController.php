<?php

namespace App\Http\Controllers;

use App\Enums\ClaimStatus;
use App\Enums\ItemStatus;
use App\Models\ItemClaim;
use App\Models\ItemMatch;
use App\Models\ItemReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $openReportsCount = ItemReport::where('reporter_id', $user->id)
            ->where('status', ItemStatus::Open->value)
            ->count();

        $returnedCount = ItemReport::where('reporter_id', $user->id)
            ->where('status', ItemStatus::Returned->value)
            ->count();

        $activeMatchesQuery = ItemMatch::with([
            'lostItem.campusZone',
            'foundItem.campusZone',
            'foundItem.custodyPost',
        ])
            ->where('is_dismissed', false)
            ->whereHas('lostItem', fn ($q) => $q->where('status', ItemStatus::Open->value))
            ->whereHas('foundItem', fn ($q) => $q->where('status', ItemStatus::Open->value))
            ->where(function ($query) use ($user): void {
                $query->whereHas('lostItem', fn ($q) => $q->where('reporter_id', $user->id))
                    ->orWhereHas('foundItem', fn ($q) => $q->where('reporter_id', $user->id));
            })
            ->orderByDesc('score');

        $activeMatchesCount = (clone $activeMatchesQuery)->count();
        $topMatches = $activeMatchesQuery->limit(3)->get();

        $activeClaimsCount = ItemClaim::where('claimant_id', $user->id)
            ->whereIn('status', [ClaimStatus::Pending->value, ClaimStatus::Approved->value])
            ->count();

        $approvedClaims = ItemClaim::with(['itemReport.campusZone', 'itemReport.custodyPost', 'itemReport.reporter'])
            ->where('claimant_id', $user->id)
            ->where('status', ClaimStatus::Approved->value)
            ->latest('reviewed_at')
            ->get();

        $myReports = ItemReport::with(['campusZone', 'custodyPost'])
            ->where('reporter_id', $user->id)
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard.index', [
            'summaryStats' => [
                'open_reports' => $openReportsCount,
                'active_matches' => $activeMatchesCount,
                'active_claims' => $activeClaimsCount,
                'returned_items' => $returnedCount,
            ],
            'approvedClaims' => $approvedClaims,
            'topMatches' => $topMatches,
            'myReports' => $myReports,
        ]);
    }
}
