<?php

namespace App\Services;

use App\Enums\ClaimStatus;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Models\CampusZone;
use App\Models\ItemClaim;
use App\Models\ItemReport;
use Illuminate\Support\Collection;

class AdminAnalyticsService
{
    /**
     * Compute database-portable operational KPIs, hotspots, and status breakdown.
     *
     * @return array{
     *     kpis: array{total_reports: int, lost_count: int, found_count: int, recovery_rate: float, avg_recovery_hours: float, pending_claims: int},
     *     hotspots: Collection<int, CampusZone>,
     *     status_breakdown: array<int, array{status: ItemStatus, count: int, percentage: float}>
     * }
     */
    public function getSummary(): array
    {
        $lostCount = ItemReport::where('type', ItemType::Lost->value)->count();
        $foundCount = ItemReport::where('type', ItemType::Found->value)->count();
        $totalReports = $lostCount + $foundCount;

        $returnedFoundCount = ItemReport::where('type', ItemType::Found->value)
            ->where('status', ItemStatus::Returned->value)
            ->count();

        $recoveryRate = $foundCount > 0 ? round(($returnedFoundCount / $foundCount) * 100, 1) : 0.0;

        $returnedItems = ItemReport::where('status', ItemStatus::Returned->value)
            ->whereNotNull('returned_at')
            ->get(['created_at', 'returned_at']);

        $avgRecoveryHours = $returnedItems->isNotEmpty()
            ? round((float) $returnedItems->avg(fn (ItemReport $item): float => max(0, $item->created_at->diffInMinutes($item->returned_at)) / 60), 1)
            : 0.0;

        $pendingClaims = ItemClaim::where('status', ClaimStatus::Pending->value)->count();

        $hotspots = CampusZone::withCount([
            'itemReports as lost_count' => fn ($query) => $query->where('type', ItemType::Lost->value),
            'itemReports as found_count' => fn ($query) => $query->where('type', ItemType::Found->value),
        ])
            ->orderByDesc('lost_count')
            ->limit(5)
            ->get();

        $statusCounts = ItemReport::query()
            ->selectRaw('status, COUNT(*) as aggregate_count')
            ->groupBy('status')
            ->pluck('aggregate_count', 'status');

        $statusBreakdown = [];
        foreach (ItemStatus::cases() as $status) {
            $count = (int) ($statusCounts[$status->value] ?? 0);
            $statusBreakdown[] = [
                'status' => $status,
                'count' => $count,
                'percentage' => $totalReports > 0 ? round(($count / $totalReports) * 100, 1) : 0.0,
            ];
        }

        return [
            'kpis' => [
                'total_reports' => $totalReports,
                'lost_count' => $lostCount,
                'found_count' => $foundCount,
                'recovery_rate' => $recoveryRate,
                'avg_recovery_hours' => $avgRecoveryHours,
                'pending_claims' => $pendingClaims,
            ],
            'hotspots' => $hotspots,
            'status_breakdown' => $statusBreakdown,
        ];
    }
}
