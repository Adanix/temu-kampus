<?php

namespace Tests\Feature;

use App\Models\CampusZone;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_non_admin_receives_403_on_analytics_export_and_master_routes(): void
    {
        $nonAdminEmails = [
            config('demo.personas.student.email'),
            config('demo.personas.staff.email'),
            config('demo.personas.custodian.email'),
        ];

        foreach ($nonAdminEmails as $email) {
            $user = User::where('email', $email)->firstOrFail();
            $this->actingAs($user)->get('/admin/analytics')->assertForbidden();
            $this->actingAs($user)->get('/admin/export/items')->assertForbidden();
            $this->actingAs($user)->get('/admin/master')->assertForbidden();
        }
    }

    public function test_admin_analytics_renders_kpis_and_hotspots_cleanly_on_sqlite(): void
    {
        $admin = User::where('email', config('demo.personas.admin.email'))->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/analytics');

        $response->assertOk();
        $response->assertSee('FT-01');
        $response->assertSee('22');
    }

    public function test_admin_csv_export_streams_utf8_csv_and_excludes_hidden_traits(): void
    {
        $admin = User::where('email', config('demo.personas.admin.email'))->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin/export/items');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('ticket_code', $content);
        $this->assertStringContainsString('TK-2026-0001', $content);
        $this->assertStringNotContainsString('stiker kecil komunitas Linux', $content);
        $this->assertStringNotContainsString('482910', $content);
    }

    public function test_admin_can_create_and_update_campus_zone_and_custody_post(): void
    {
        $admin = User::where('email', config('demo.personas.admin.email'))->firstOrFail();

        $this->actingAs($admin)->post('/admin/zones', [
            'code' => 'FK-01',
            'name_id' => 'Gedung Fakultas Kedokteran',
            'name_en' => 'Faculty of Medicine Building',
            'zone_group' => 'Kesehatan',
        ])->assertRedirect(route('admin.master.index'));

        $zone = CampusZone::where('code', 'FK-01')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.zones.update', $zone), [
            'code' => 'FK-01',
            'name_id' => 'Gedung Fakultas Kedokteran Utama',
            'name_en' => 'Main Faculty of Medicine Building',
            'zone_group' => 'Kesehatan',
        ])->assertRedirect(route('admin.master.index'));

        $this->assertSame('Gedung Fakultas Kedokteran Utama', $zone->fresh()->name_id);

        $this->actingAs($admin)->post('/admin/custody-posts', [
            'campus_zone_id' => $zone->id,
            'name_id' => 'Pos Kesehatan FK',
            'name_en' => 'Medicine Health Post',
            'operating_hours' => '07:30-16:00 WIB',
            'contact_ext' => 'Ext. 410',
        ])->assertRedirect(route('admin.master.index'));

        $this->assertDatabaseHas('custody_posts', [
            'campus_zone_id' => $zone->id,
            'name_id' => 'Pos Kesehatan FK',
        ]);
    }
}
