<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectItemClaimRequest;
use App\Http\Requests\StoreItemClaimRequest;
use App\Http\Requests\VerifyHandoverPinRequest;
use App\Models\ItemClaim;
use App\Models\ItemReport;
use App\Services\ClaimWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ItemClaimController extends Controller
{
    public function __construct(
        private readonly ClaimWorkflowService $claimWorkflow,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = ItemClaim::with([
            'itemReport.campusZone',
            'itemReport.custodyPost',
            'itemReport.reporter',
            'reviewer',
            'claimant',
        ])->latest();

        if (! $user->isAdmin()) {
            $query->where('claimant_id', $user->id);
        }

        return view('claims.index', [
            'claims' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function store(StoreItemClaimRequest $request, ItemReport $item): RedirectResponse
    {
        Gate::authorize('create', [ItemClaim::class, $item]);

        $claim = $this->claimWorkflow->submitClaim(
            item: $item,
            claimant: $request->user(),
            answer: $request->validated('claimant_answer'),
            proofPhoto: $request->file('proof_photo'),
        );

        return redirect()
            ->route('items.show', $item)
            ->with('status', __('app.flash.claim_submitted', ['code' => $claim->claim_code]));
    }

    public function approve(Request $request, ItemClaim $claim): RedirectResponse
    {
        Gate::authorize('review', $claim);

        $notes = $request->input('review_notes');
        $cleanNotes = is_string($notes) && trim($notes) !== '' ? trim($notes) : null;

        $approved = $this->claimWorkflow->approveClaim(
            claim: $claim,
            reviewer: $request->user(),
            notes: $cleanNotes,
        );

        return redirect()
            ->back()
            ->with('status', __('app.flash.claim_approved', ['code' => $approved->claim_code]));
    }

    public function reject(RejectItemClaimRequest $request, ItemClaim $claim): RedirectResponse
    {
        Gate::authorize('review', $claim);

        $rejected = $this->claimWorkflow->rejectClaim(
            claim: $claim,
            reviewer: $request->user(),
            notes: $request->validated('review_notes'),
        );

        return redirect()
            ->back()
            ->with('status', __('app.flash.claim_rejected', ['code' => $rejected->claim_code]));
    }

    public function handover(VerifyHandoverPinRequest $request, ItemClaim $claim): RedirectResponse
    {
        Gate::authorize('handover', $claim);

        $completed = $this->claimWorkflow->completeHandover(
            claim: $claim,
            verifier: $request->user(),
            submittedPin: $request->validated('handover_pin'),
        );

        return redirect()
            ->back()
            ->with('status', __('app.flash.handover_completed', ['code' => $completed->claim_code]));
    }
}
