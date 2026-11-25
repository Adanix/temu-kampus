<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Models\CustodyPost;
use Illuminate\View\View;

class CustodyPostDirectoryController extends Controller
{
    public function index(): View
    {
        $custodyPosts = CustodyPost::with('campusZone')
            ->withCount([
                'itemReports as open_items_count' => fn ($query) => $query
                    ->where('type', ItemType::Found->value)
                    ->where('status', ItemStatus::Open->value),
            ])
            ->orderBy('name_id')
            ->get();

        return view('custody-posts.index', [
            'custodyPosts' => $custodyPosts,
        ]);
    }
}
