<?php

namespace App\Http\Controllers;

use App\Models\ItemReport;
use App\Services\AdminAnalyticsService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAnalyticsController extends Controller
{
    public function __construct(
        private readonly AdminAnalyticsService $analytics,
    ) {}

    public function index(): View
    {
        Gate::authorize('access-admin-analytics');

        $summary = $this->analytics->getSummary();

        $recentItems = ItemReport::with(['campusZone', 'custodyPost', 'reporter'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.analytics', [
            'kpis' => $summary['kpis'],
            'hotspots' => $summary['hotspots'],
            'statusBreakdown' => $summary['status_breakdown'],
            'recentItems' => $recentItems,
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        Gate::authorize('access-admin-analytics');

        $filename = 'temukampus-items-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM for clean spreadsheet compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ticket_code',
                'type',
                'status',
                'title',
                'category',
                'campus_zone_code',
                'campus_zone_name',
                'incident_date',
                'custody_type',
                'custody_post',
                'reporter_name',
                'created_at',
                'returned_at',
            ]);

            ItemReport::with(['campusZone', 'custodyPost', 'reporter'])
                ->orderBy('id')
                ->chunk(200, function ($items) use ($handle): void {
                    foreach ($items as $item) {
                        fputcsv($handle, [
                            $this->sanitizeCsvCell($item->ticket_code),
                            $this->sanitizeCsvCell($item->type->value),
                            $this->sanitizeCsvCell($item->status->value),
                            $this->sanitizeCsvCell($item->title),
                            $this->sanitizeCsvCell($item->category->value),
                            $this->sanitizeCsvCell($item->campusZone?->code ?? ''),
                            $this->sanitizeCsvCell($item->campusZone?->localizedName() ?? ''),
                            $this->sanitizeCsvCell($item->incident_date?->toDateString() ?? ''),
                            $this->sanitizeCsvCell($item->custody_type?->value ?? ''),
                            $this->sanitizeCsvCell($item->custodyPost?->localizedName() ?? ''),
                            $this->sanitizeCsvCell($item->reporter?->name ?? ''),
                            $this->sanitizeCsvCell($item->created_at?->toIso8601String() ?? ''),
                            $this->sanitizeCsvCell($item->returned_at?->toIso8601String() ?? ''),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function sanitizeCsvCell(string $value): string
    {
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$value;
        }

        return $value;
    }
}
