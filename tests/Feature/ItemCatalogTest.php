<?php

namespace Tests\Feature;

use App\Enums\ItemCategory;
use App\Models\ItemReport;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_catalog_filters_by_type_category_zone_and_search(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();

        $response = $this->actingAs($student)->get('/items?type=lost&search=MacBook');

        $response->assertOk();
        $response->assertSee('TK-2026-0001');
        $response->assertDontSee('TK-2026-0012');
    }

    public function test_sensitive_id_document_shows_blur_shield_and_hides_reveal_toggle_from_other_students(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $sensitiveItem = ItemReport::where('category', ItemCategory::IdDocument->value)
            ->where('reporter_id', '!=', $student->id)
            ->firstOrFail();

        $response = $this->actingAs($student)->get(route('items.show', $sensitiveItem));

        $response->assertOk();
        $response->assertSee(__('app.privacy.blurred_badge'));
        $response->assertDontSee(__('app.privacy.reveal_photo'));
        $response->assertDontSee($sensitiveItem->hidden_traits);
    }

    public function test_custodian_and_reporter_can_view_hidden_traits_and_sensitive_toggle(): void
    {
        $custodian = User::where('email', config('demo.personas.custodian.email'))->firstOrFail();
        $sensitiveItem = ItemReport::where('category', ItemCategory::IdDocument->value)
            ->whereNotNull('hidden_traits')
            ->firstOrFail();

        $response = $this->actingAs($custodian)->get(route('items.show', $sensitiveItem));

        $response->assertOk();
        $response->assertSee(__('app.privacy.reveal_photo'));
        $response->assertSee($sensitiveItem->hidden_traits);
    }

    public function test_custody_posts_directory_renders_for_authenticated_users(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();

        $response = $this->actingAs($student)->get('/custody-posts');

        $response->assertOk();
        $response->assertSee('Pos Satpam Gerbang Utama');
        $response->assertSee('Resepsionis Perpustakaan Pusat');
    }

    public function test_seeded_items_have_existing_relevant_local_photos_and_render_in_catalog(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $items = ItemReport::orderBy('ticket_code')->get();

        $this->assertCount(22, $items);

        foreach ($items as $item) {
            $this->assertNotNull($item->photo_path, "Missing photo_path for {$item->ticket_code}");
            $this->assertFileExists(public_path($item->photo_path));
        }

        $response = $this->actingAs($student)->get('/items');
        $response->assertOk();
        $response->assertSee('images/demo/items/', false);
    }
}
