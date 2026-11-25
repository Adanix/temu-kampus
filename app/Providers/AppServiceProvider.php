<?php

namespace App\Providers;

use App\Enums\ClaimStatus;
use App\Models\ItemClaim;
use App\Models\ItemMatch;
use App\Models\ItemReport;
use App\Models\NotificationLog;
use App\Models\User;
use App\Policies\ItemClaimPolicy;
use App\Policies\ItemMatchPolicy;
use App\Policies\ItemReportPolicy;
use App\Services\Notifications\NotificationDispatcherInterface;
use App\Services\Notifications\SimulatedNotificationDispatcher;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            NotificationDispatcherInterface::class,
            SimulatedNotificationDispatcher::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::policy(ItemReport::class, ItemReportPolicy::class);
        Gate::policy(ItemClaim::class, ItemClaimPolicy::class);
        Gate::policy(ItemMatch::class, ItemMatchPolicy::class);

        Gate::define('access-custodian-desk', fn (User $user): bool => $user->isCustodianOrAdmin());
        Gate::define('access-admin-analytics', fn (User $user): bool => $user->isAdmin());
        Gate::define('manage-campus-master', fn (User $user): bool => $user->isAdmin());

        View::composer('layouts.app', function (BladeView $view): void {
            $user = Auth::user();
            if (! $user instanceof User) {
                $view->with([
                    'unreadNotificationCount' => 0,
                    'pendingClaimsCount' => 0,
                ]);

                return;
            }

            $unreadCount = NotificationLog::forUser($user)->whereNull('read_at')->count();
            $pendingClaimsCount = $user->isCustodianOrAdmin()
                ? ItemClaim::where('status', ClaimStatus::Pending->value)->count()
                : 0;

            $view->with([
                'unreadNotificationCount' => $unreadCount,
                'pendingClaimsCount' => $pendingClaimsCount,
            ]);
        });
    }
}
