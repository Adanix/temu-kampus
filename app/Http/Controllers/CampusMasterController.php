<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampusZoneRequest;
use App\Http\Requests\StoreCustodyPostRequest;
use App\Models\CampusZone;
use App\Models\CustodyPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CampusMasterController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-campus-master');

        return view('admin.master', [
            'zones' => CampusZone::withCount('itemReports')->orderBy('code')->get(),
            'custodyPosts' => CustodyPost::with('campusZone')->withCount('itemReports')->orderBy('name_id')->get(),
        ]);
    }

    public function storeZone(StoreCampusZoneRequest $request): RedirectResponse
    {
        Gate::authorize('manage-campus-master');

        CampusZone::create($request->validated());

        return redirect()
            ->route('admin.master.index')
            ->with('status', __('app.flash.zone_saved'));
    }

    public function updateZone(StoreCampusZoneRequest $request, CampusZone $zone): RedirectResponse
    {
        Gate::authorize('manage-campus-master');

        $zone->update($request->validated());

        return redirect()
            ->route('admin.master.index')
            ->with('status', __('app.flash.zone_saved'));
    }

    public function storeCustodyPost(StoreCustodyPostRequest $request): RedirectResponse
    {
        Gate::authorize('manage-campus-master');

        CustodyPost::create($request->validated());

        return redirect()
            ->route('admin.master.index')
            ->with('status', __('app.flash.post_saved'));
    }

    public function updateCustodyPost(StoreCustodyPostRequest $request, CustodyPost $post): RedirectResponse
    {
        Gate::authorize('manage-campus-master');

        $post->update($request->validated());

        return redirect()
            ->route('admin.master.index')
            ->with('status', __('app.flash.post_saved'));
    }
}
