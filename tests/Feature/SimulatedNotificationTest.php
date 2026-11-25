<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use App\Enums\NotificationEventType;
use App\Models\CampusZone;
use App\Models\CustodyPost;
use App\Models\NotificationLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulatedNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_student_sees_only_own_notifications_and_can_mark_read(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();

        $studentLog = NotificationLog::where('user_id', $student->id)->whereNull('read_at')->firstOrFail();
        $staffLog = NotificationLog::where('user_id', $staff->id)->firstOrFail();

        $response = $this->actingAs($student)->get('/notifications');
        $response->assertOk();
        $response->assertSee($studentLog->subject_id);
        $response->assertDontSee($staffLog->subject_id);

        $this->actingAs($student)
            ->post(route('notifications.read', $staffLog))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('notifications.read', $studentLog))
            ->assertRedirect();

        $this->assertNotNull($studentLog->fresh()->read_at);

        $this->actingAs($student)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, NotificationLog::where('user_id', $student->id)->whereNull('read_at')->count());
    }

    public function test_creating_matching_found_item_dispatches_simulated_match_notification(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $zone = CampusZone::where('code', 'FT-01')->firstOrFail();
        $post = CustodyPost::firstOrFail();

        $this->actingAs($student)->post('/items', [
            'type' => ItemType::Lost->value,
            'title' => 'Headset HyperX Cloud II Merah',
            'category' => ItemCategory::Electronics->value,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'color' => 'Merah',
            'brand' => 'HyperX',
            'description' => 'Headset gaming HyperX Cloud II merah hilang di Lab Teknik.',
        ])->assertRedirect();

        $this->actingAs($staff)->post('/items', [
            'type' => ItemType::Found->value,
            'title' => 'Headset HyperX Cloud II Merah',
            'category' => ItemCategory::Electronics->value,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'color' => 'Merah',
            'brand' => 'HyperX',
            'description' => 'Ditemukan headset HyperX Cloud II merah di Lab Teknik.',
            'hidden_traits' => 'Busa telinga sebelah kiri agak terkelupas sedikit.',
            'custody_type' => 'at_custody_post',
            'custody_post_id' => $post->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $student->id,
            'event_type' => NotificationEventType::MatchFound->value,
            'delivery_mode' => 'simulated',
        ]);
    }
}
