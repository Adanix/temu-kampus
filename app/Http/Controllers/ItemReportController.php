<?php

namespace App\Http\Controllers;

use App\Enums\ClaimStatus;
use App\Enums\CustodyType;
use App\Enums\ItemCategory;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Http\Requests\StoreItemReportRequest;
use App\Http\Requests\UpdateItemReportRequest;
use App\Models\CampusZone;
use App\Models\CustodyPost;
use App\Models\ItemClaim;
use App\Models\ItemMatch;
use App\Models\ItemReport;
use App\Services\SmartMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ItemReportController extends Controller
{
    public function __construct(
        private readonly SmartMatchingService $smartMatching,
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ItemReport::class);

        $type = (string) $request->input('type', '');
        $category = (string) $request->input('category', '');
        $zoneId = (string) $request->input('zone_id', '');
        $status = $request->has('status') ? (string) $request->input('status') : ItemStatus::Open->value;
        $search = trim((string) $request->input('search', ''));
        $sort = (string) $request->input('sort', 'latest');

        $query = ItemReport::with(['campusZone', 'custodyPost', 'reporter']);

        if (in_array($type, ItemType::values(), true)) {
            $query->where('type', $type);
        }

        if (in_array($category, ItemCategory::values(), true)) {
            $query->where('category', $category);
        }

        if ($zoneId !== '' && ctype_digit($zoneId)) {
            $query->where('campus_zone_id', (int) $zoneId);
        }

        if ($status !== 'all' && in_array($status, ItemStatus::values(), true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($sub) use ($like): void {
                $sub->where('ticket_code', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('brand', 'like', $like)
                    ->orWhere('color', 'like', $like)
                    ->orWhere('location_detail', 'like', $like);
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('incident_date')->orderBy('id');
        } else {
            $query->orderByDesc('incident_date')->orderByDesc('id');
        }

        $items = $query->paginate(12)->withQueryString();

        $activeFilterCount = collect([
            in_array($category, ItemCategory::values(), true),
            $zoneId !== '' && ctype_digit($zoneId),
            $status !== ItemStatus::Open->value,
            $sort === 'oldest',
        ])->filter()->count();

        return view('items.index', [
            'items' => $items,
            'zones' => CampusZone::orderBy('code')->get(),
            'categories' => ItemCategory::cases(),
            'statuses' => ItemStatus::cases(),
            'filters' => [
                'type' => $type,
                'category' => $category,
                'zone_id' => $zoneId,
                'status' => $status,
                'search' => $search,
                'sort' => $sort,
            ],
            'activeFilterCount' => $activeFilterCount,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', ItemReport::class);

        $defaultType = in_array($request->input('type'), ItemType::values(), true)
            ? (string) $request->input('type')
            : ItemType::Lost->value;

        return view('items.create', [
            'zones' => CampusZone::orderBy('code')->get(),
            'custodyPosts' => CustodyPost::with('campusZone')->orderBy('name_id')->get(),
            'categories' => ItemCategory::cases(),
            'defaultType' => $defaultType,
        ]);
    }

    public function store(StoreItemReportRequest $request): RedirectResponse
    {
        Gate::authorize('create', ItemReport::class);

        $validated = $request->validated();
        $isFound = $validated['type'] === ItemType::Found->value;
        $category = ItemCategory::from($validated['category']);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('items', 'public')
            : null;

        $custodyType = $isFound ? ($validated['custody_type'] ?? null) : null;
        $custodyPostId = ($isFound && $custodyType === CustodyType::AtCustodyPost->value)
            ? ($validated['custody_post_id'] ?? null)
            : null;

        $item = ItemReport::create([
            'ticket_code' => ItemReport::generateTicketCode(),
            'reporter_id' => $request->user()->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'category' => $category->value,
            'campus_zone_id' => $validated['campus_zone_id'],
            'location_detail' => $validated['location_detail'] ?? null,
            'incident_date' => $validated['incident_date'],
            'color' => $validated['color'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'is_sensitive' => $category->isAutoSensitive(),
            'hidden_traits' => $isFound ? ($validated['hidden_traits'] ?? null) : null,
            'custody_type' => $custodyType,
            'custody_post_id' => $custodyPostId,
            'status' => ItemStatus::Open->value,
        ]);

        $matches = $this->smartMatching->generateMatchesForItem($item);

        $flashMessage = $matches->isNotEmpty()
            ? __('app.flash.report_created_with_matches', ['code' => $item->ticket_code, 'count' => $matches->count()])
            : __('app.flash.report_created', ['code' => $item->ticket_code]);

        return redirect()
            ->route('items.show', $item)
            ->with('status', $flashMessage);
    }

    public function show(Request $request, ItemReport $item): View
    {
        Gate::authorize('view', $item);

        $item->load([
            'campusZone',
            'custodyPost.campusZone',
            'reporter',
            'claims.claimant',
            'claims.reviewer',
            'claims.handedOverBy',
        ]);

        $user = $request->user();
        $canViewHidden = $user->can('viewHiddenTraits', $item);
        $canViewSensitive = $user->can('viewSensitivePhoto', $item);

        $userClaim = $item->claims->where('claimant_id', $user->id)->sortByDesc('id')->first();
        $hasActiveUserClaim = $item->claims
            ->where('claimant_id', $user->id)
            ->whereIn('status', [ClaimStatus::Pending, ClaimStatus::Approved])
            ->isNotEmpty();

        $canSubmitClaim = $user->can('create', [ItemClaim::class, $item]) && ! $hasActiveUserClaim;
        $activeApprovedClaim = $item->claims->firstWhere('status', ClaimStatus::Approved);
        $canHandover = $activeApprovedClaim !== null && $user->can('handover', $activeApprovedClaim);

        $relatedMatches = collect();
        if ($user->id === $item->reporter_id || $user->isCustodianOrAdmin()) {
            $matchRelation = $item->type === ItemType::Lost ? 'lost_item_id' : 'found_item_id';
            $relatedMatches = ItemMatch::with(['lostItem.campusZone', 'foundItem.campusZone'])
                ->where($matchRelation, $item->id)
                ->where('is_dismissed', false)
                ->orderByDesc('score')
                ->limit(4)
                ->get();
        }

        return view('items.show', [
            'item' => $item,
            'canViewHidden' => $canViewHidden,
            'canViewSensitive' => $canViewSensitive,
            'canSubmitClaim' => $canSubmitClaim,
            'userClaim' => $userClaim,
            'activeApprovedClaim' => $activeApprovedClaim,
            'canHandover' => $canHandover,
            'relatedMatches' => $relatedMatches,
        ]);
    }

    public function edit(ItemReport $item): View
    {
        Gate::authorize('update', $item);

        return view('items.edit', [
            'item' => $item,
            'zones' => CampusZone::orderBy('code')->get(),
            'custodyPosts' => CustodyPost::with('campusZone')->orderBy('name_id')->get(),
            'categories' => ItemCategory::cases(),
        ]);
    }

    public function update(UpdateItemReportRequest $request, ItemReport $item): RedirectResponse
    {
        Gate::authorize('update', $item);

        $validated = $request->validated();
        $isFound = $item->type === ItemType::Found;
        $category = ItemCategory::from($validated['category']);

        $photoPath = $item->photo_path;
        if ($request->hasFile('photo')) {
            if ($item->hasUploadedOrSeededPhoto() && ! $item->isDemoAssetPhoto() && ! str_starts_with((string) $item->photo_path, 'http')) {
                Storage::disk('public')->delete((string) $item->photo_path);
            }
            $photoPath = $request->file('photo')->store('items', 'public');
        }

        $custodyType = $isFound ? ($validated['custody_type'] ?? null) : null;
        $custodyPostId = ($isFound && $custodyType === CustodyType::AtCustodyPost->value)
            ? ($validated['custody_post_id'] ?? null)
            : null;

        $item->update([
            'title' => $validated['title'],
            'category' => $category->value,
            'campus_zone_id' => $validated['campus_zone_id'],
            'location_detail' => $validated['location_detail'] ?? null,
            'incident_date' => $validated['incident_date'],
            'color' => $validated['color'] ?? null,
            'brand' => $validated['brand'] ?? null,
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'is_sensitive' => $category->isAutoSensitive(),
            'hidden_traits' => $isFound ? ($validated['hidden_traits'] ?? null) : null,
            'custody_type' => $custodyType,
            'custody_post_id' => $custodyPostId,
        ]);

        $this->smartMatching->generateMatchesForItem($item->fresh());

        return redirect()
            ->route('items.show', $item)
            ->with('status', __('app.flash.report_updated', ['code' => $item->ticket_code]));
    }

    public function cancel(Request $request, ItemReport $item): RedirectResponse
    {
        Gate::authorize('cancel', $item);

        DB::transaction(function () use ($item, $request): void {
            $item->update([
                'status' => ItemStatus::Cancelled->value,
            ]);

            $item->claims()
                ->where('status', ClaimStatus::Pending->value)
                ->update([
                    'status' => ClaimStatus::Rejected->value,
                    'reviewer_id' => $request->user()->id,
                    'reviewed_at' => now(),
                    'review_notes' => __('app.claims.auto_rejected_note'),
                ]);
        });

        return redirect()
            ->route('items.show', $item)
            ->with('status', __('app.flash.report_cancelled', ['code' => $item->ticket_code]));
    }

    public function archive(ItemReport $item): RedirectResponse
    {
        Gate::authorize('archive', $item);

        $item->update([
            'status' => ItemStatus::UnclaimedArchived->value,
            'archived_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('status', __('app.flash.report_archived', ['code' => $item->ticket_code]));
    }
}
