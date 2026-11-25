<?php

namespace Tests\Unit;

use App\Enums\ItemCategory;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Models\CampusZone;
use App\Models\ItemMatch;
use App\Models\ItemReport;
use App\Models\User;
use App\Services\SmartMatchingService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartMatchingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_exact_weighted_score_calculation_35_25_25_15(): void
    {
        $service = app(SmartMatchingService::class);
        $zone = CampusZone::where('code', 'FT-01')->firstOrFail();
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();

        $lost = ItemReport::create([
            'ticket_code' => 'TK-UNIT-0001',
            'reporter_id' => $student->id,
            'type' => ItemType::Lost,
            'title' => 'Tablet iPad Pro Abu',
            'category' => ItemCategory::Electronics,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->subDays(1)->toDateString(),
            'color' => 'Abu',
            'brand' => 'Apple',
            'description' => 'Tablet iPad Pro warna abu dengan stylus Apple Pencil.',
            'status' => ItemStatus::Open,
        ]);

        $found = ItemReport::create([
            'ticket_code' => 'TK-UNIT-0002',
            'reporter_id' => $staff->id,
            'type' => ItemType::Found,
            'title' => 'Tablet iPad Pro Abu',
            'category' => ItemCategory::Electronics,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'color' => 'Abu',
            'brand' => 'Apple',
            'description' => 'Tablet iPad Pro warna abu dengan stylus Apple Pencil.',
            'status' => ItemStatus::Open,
        ]);

        $result = $service->calculateMatch($lost, $found);

        $this->assertSame(100, $result['score']);
        $this->assertSame(35, $result['reasons']['category']);
        $this->assertSame(25, $result['reasons']['zone']);
        $this->assertSame(25, $result['reasons']['keywords']);
        $this->assertSame(15, $result['reasons']['date']);
    }

    public function test_different_category_returns_zero_score(): void
    {
        $service = app(SmartMatchingService::class);
        $zone = CampusZone::firstOrFail();
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();

        $lost = ItemReport::create([
            'ticket_code' => 'TK-UNIT-0003',
            'reporter_id' => $student->id,
            'type' => ItemType::Lost,
            'title' => 'Laptop Hitam',
            'category' => ItemCategory::Electronics,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'description' => 'Laptop hitam hilang di ruang belajar.',
            'status' => ItemStatus::Open,
        ]);

        $found = ItemReport::create([
            'ticket_code' => 'TK-UNIT-0004',
            'reporter_id' => $student->id,
            'type' => ItemType::Found,
            'title' => 'Kunci Hitam',
            'category' => ItemCategory::Keys,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'description' => 'Kunci hitam ditemukan di ruang belajar.',
            'status' => ItemStatus::Open,
        ]);

        $result = $service->calculateMatch($lost, $found);

        $this->assertSame(0, $result['score']);
    }

    public function test_matches_below_60_are_not_persisted_and_dismissed_flag_is_preserved(): void
    {
        $service = app(SmartMatchingService::class);
        $ftZone = CampusZone::where('code', 'FT-01')->firstOrFail();
        $febZone = CampusZone::where('code', 'FEB-01')->firstOrFail();
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();

        $lost = ItemReport::create([
            'ticket_code' => 'TK-UNIT-0005',
            'reporter_id' => $student->id,
            'type' => ItemType::Lost,
            'title' => 'Kacamata Renang Biru',
            'category' => ItemCategory::Accessories,
            'campus_zone_id' => $ftZone->id,
            'incident_date' => now()->subDays(30)->toDateString(),
            'description' => 'Kacamata renang biru muda.',
            'status' => ItemStatus::Open,
        ]);

        $found = ItemReport::create([
            'ticket_code' => 'TK-UNIT-0006',
            'reporter_id' => $staff->id,
            'type' => ItemType::Found,
            'title' => 'Topi Baseball Putih',
            'category' => ItemCategory::Accessories,
            'campus_zone_id' => $febZone->id,
            'incident_date' => now()->toDateString(),
            'description' => 'Topi kain putih polos.',
            'status' => ItemStatus::Open,
        ]);

        $matches = $service->generateMatchesForItem($lost, notify: false);
        $this->assertFalse($matches->contains(fn (ItemMatch $m): bool => $m->found_item_id === $found->id));

        $existingMatch = ItemMatch::firstOrFail();
        $existingMatch->update(['is_dismissed' => true]);

        $service->generateMatchesForItem($existingMatch->lostItem, notify: false);
        $this->assertTrue($existingMatch->fresh()->is_dismissed);
    }
}
