<?php

namespace App\Http\Controllers;

use App\Enums\ClaimStatus;
use App\Enums\CustodyType;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Models\ItemClaim;
use App\Models\ItemReport;
use Illuminate\View\View;

class CustodianDeskController extends Controller
{
    public function index(): View
    {
        $pendingClaims = ItemClaim::with([
            'itemReport.campusZone',
            'itemReport.custodyPost',
            'itemReport.reporter',
            'claimant',
        ])
            ->where('status', ClaimStatus::Pending->value)
            ->oldest()
            ->get();

        $approvedClaims = ItemClaim::with([
            'itemReport.campusZone',
            'itemReport.custodyPost',
            'claimant',
            'reviewer',
        ])
            ->where('status', ClaimStatus::Approved->value)
            ->latest('reviewed_at')
            ->get();

        $depositedItems = ItemReport::with(['campusZone', 'custodyPost', 'reporter'])
            ->where('type', ItemType::Found->value)
            ->where('status', ItemStatus::Open->value)
            ->where('custody_type', CustodyType::AtCustodyPost->value)
            ->latest()
            ->get();

        return view('custodian.desk', [
            'pendingClaims' => $pendingClaims,
            'approvedClaims' => $approvedClaims,
            'depositedItems' => $depositedItems,
            'deskStats' => [
                'pending_claims' => $pendingClaims->count(),
                'awaiting_handover' => $approvedClaims->count(),
                'deposited_items' => $depositedItems->count(),
            ],
        ]);
    }
}
