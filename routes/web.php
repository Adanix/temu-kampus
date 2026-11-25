<?php

use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\CampusMasterController;
use App\Http\Controllers\CustodianDeskController;
use App\Http\Controllers\CustodyPostDirectoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DemoAuthController;
use App\Http\Controllers\ItemClaimController;
use App\Http\Controllers\ItemMatchController;
use App\Http\Controllers\ItemReportController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

// Public & Locale Routes
Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/docs', [PublicPageController::class, 'docs'])->name('public.docs');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.switch');

// Demo Persona Auth Routes (guarded by APP_DEMO_MODE)
Route::middleware('demo.enabled')->group(function (): void {
    Route::get('/demo/login', [DemoAuthController::class, 'show'])->name('demo.login');
    Route::post('/demo/quick-login', [DemoAuthController::class, 'login'])
        ->middleware('throttle:20,1')
        ->name('demo.quick-login');
});

// Authenticated Workspace Routes
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [DemoAuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catalog, Reporting, Cancel & Archive
    Route::get('/items', [ItemReportController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemReportController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemReportController::class, 'store'])->name('items.store');
    Route::get('/items/{item}', [ItemReportController::class, 'show'])->name('items.show');
    Route::get('/items/{item}/edit', [ItemReportController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemReportController::class, 'update'])->name('items.update');
    Route::post('/items/{item}/cancel', [ItemReportController::class, 'cancel'])->name('items.cancel');
    Route::post('/items/{item}/archive', [ItemReportController::class, 'archive'])->name('items.archive');

    // Custody Posts Directory
    Route::get('/custody-posts', [CustodyPostDirectoryController::class, 'index'])->name('custody-posts.index');

    // Smart Matches
    Route::get('/matches', [ItemMatchController::class, 'index'])->name('matches.index');
    Route::post('/matches/{match}/dismiss', [ItemMatchController::class, 'dismiss'])->name('matches.dismiss');

    // Blind Challenge Claims & 6-Digit Handover PIN
    Route::get('/claims', [ItemClaimController::class, 'index'])->name('claims.index');
    Route::post('/items/{item}/claims', [ItemClaimController::class, 'store'])->name('claims.store');
    Route::post('/claims/{claim}/approve', [ItemClaimController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{claim}/reject', [ItemClaimController::class, 'reject'])->name('claims.reject');
    Route::post('/claims/{claim}/handover', [ItemClaimController::class, 'handover'])->name('claims.handover');

    // Custodian Desk
    Route::get('/custodian/desk', [CustodianDeskController::class, 'index'])
        ->middleware('can:access-custodian-desk')
        ->name('custodian.desk');

    // Admin Analytics & CSV Export
    Route::middleware('can:access-admin-analytics')->group(function (): void {
        Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics');
        Route::get('/admin/export/items', [AdminAnalyticsController::class, 'exportCsv'])->name('admin.export.items');
    });

    // Admin Campus Master Data
    Route::middleware('can:manage-campus-master')->group(function (): void {
        Route::get('/admin/master', [CampusMasterController::class, 'index'])->name('admin.master.index');
        Route::post('/admin/zones', [CampusMasterController::class, 'storeZone'])->name('admin.zones.store');
        Route::put('/admin/zones/{zone}', [CampusMasterController::class, 'updateZone'])->name('admin.zones.update');
        Route::post('/admin/custody-posts', [CampusMasterController::class, 'storeCustodyPost'])->name('admin.custody-posts.store');
        Route::put('/admin/custody-posts/{post}', [CampusMasterController::class, 'updateCustodyPost'])->name('admin.custody-posts.update');
    });

    // Simulated Notification Log
    Route::get('/notifications', [NotificationLogController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationLogController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationLogController::class, 'markAllRead'])->name('notifications.read-all');
});
