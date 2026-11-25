<?php

namespace Tests\Feature;

use App\Enums\CustodyType;
use App\Enums\ItemCategory;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Models\CampusZone;
use App\Models\CustodyPost;
use App\Models\ItemReport;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemReportCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_student_can_report_lost_item_and_auto_generate_ticket_code(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $zone = CampusZone::where('code', 'FT-01')->firstOrFail();

        $response = $this->actingAs($student)->post('/items', [
            'type' => ItemType::Lost->value,
            'title' => 'Mouse Logitech MX Master 3S Hitam',
            'category' => ItemCategory::Electronics->value,
            'campus_zone_id' => $zone->id,
            'location_detail' => 'Ruang Seminar FT',
            'incident_date' => now()->toDateString(),
            'color' => 'Hitam',
            'brand' => 'Logitech',
            'description' => 'Mouse nirkabel hitam tertinggal di meja pojok kiri ruang seminar.',
        ]);

        $created = ItemReport::where('title', 'Mouse Logitech MX Master 3S Hitam')->firstOrFail();
        $response->assertRedirect(route('items.show', $created));
        $this->assertStringStartsWith('TK-', $created->ticket_code);
        $this->assertFalse($created->is_sensitive);
        $this->assertNull($created->photo_path);

        $detail = $this->actingAs($student)->get(route('items.show', $created));
        $detail->assertOk();
        $detail->assertSee($created->displayPhotoUrl(), false);
        $detail->assertSee(__('app.items.category_illustration'));
    }

    public function test_updating_seeded_item_with_uploaded_photo_preserves_shared_demo_asset(): void
    {
        Storage::fake('public');

        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $seededItem = ItemReport::where('ticket_code', 'TK-2026-0001')->firstOrFail();
        $originalDemoPath = $seededItem->photo_path;

        $this->assertFileExists(public_path((string) $originalDemoPath));

        $this->actingAs($student)->put(route('items.update', $seededItem), [
            'title' => $seededItem->title,
            'category' => $seededItem->category->value,
            'campus_zone_id' => $seededItem->campus_zone_id,
            'incident_date' => $seededItem->incident_date->toDateString(),
            'description' => $seededItem->description,
            'photo' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertRedirect(route('items.show', $seededItem));

        $seededItem->refresh();
        $this->assertNotSame($originalDemoPath, $seededItem->photo_path);
        Storage::disk('public')->assertExists((string) $seededItem->photo_path);
        $this->assertFileExists(public_path((string) $originalDemoPath));
    }

    public function test_staff_can_report_found_item_with_hidden_traits_custody_post_and_photo(): void
    {
        Storage::fake('public');

        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $zone = CampusZone::where('code', 'PERPUS-01')->firstOrFail();
        $post = CustodyPost::firstOrFail();

        $response = $this->actingAs($staff)->post('/items', [
            'type' => ItemType::Found->value,
            'title' => 'Dompet Kanvas Navy Ditemukan',
            'category' => ItemCategory::WalletBag->value,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'description' => 'Ditemukan di dekat loker tas Perpustakaan Pusat.',
            'photo' => UploadedFile::fake()->image('wallet.jpg'),
            'hidden_traits' => 'Di dalamnya terdapat kartu member gym dan foto polaroid.',
            'custody_type' => CustodyType::AtCustodyPost->value,
            'custody_post_id' => $post->id,
        ]);

        $created = ItemReport::where('title', 'Dompet Kanvas Navy Ditemukan')->firstOrFail();
        $response->assertRedirect(route('items.show', $created));
        $this->assertNotNull($created->photo_path);
        Storage::disk('public')->assertExists($created->photo_path);
        $this->assertSame($post->id, $created->custody_post_id);
    }

    public function test_id_document_category_automatically_enables_is_sensitive(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $zone = CampusZone::firstOrFail();

        $this->actingAs($student)->post('/items', [
            'type' => ItemType::Lost->value,
            'title' => 'KTM Mahasiswa Baru Teknik',
            'category' => ItemCategory::IdDocument->value,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'description' => 'Kartu mahasiswa terjatuh di selasar gedung.',
        ])->assertRedirect();

        $created = ItemReport::where('title', 'KTM Mahasiswa Baru Teknik')->firstOrFail();
        $this->assertTrue($created->is_sensitive);
    }

    public function test_found_item_requires_hidden_traits_and_custody_type(): void
    {
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $zone = CampusZone::firstOrFail();

        $response = $this->actingAs($staff)->post('/items', [
            'type' => ItemType::Found->value,
            'title' => 'Earbuds Bluetooth Putih',
            'category' => ItemCategory::Electronics->value,
            'campus_zone_id' => $zone->id,
            'incident_date' => now()->toDateString(),
            'description' => 'Ditemukan di ruang kuliah pagi ini.',
        ]);

        $response->assertSessionHasErrors(['hidden_traits', 'custody_type']);
    }

    public function test_owner_can_edit_and_cancel_own_open_report_while_other_student_gets_403(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $studentItem = ItemReport::where('reporter_id', $student->id)
            ->where('status', ItemStatus::Open->value)
            ->firstOrFail();

        $this->actingAs($staff)
            ->get(route('items.edit', $studentItem))
            ->assertForbidden();

        $this->actingAs($student)
            ->put(route('items.update', $studentItem), [
                'title' => 'MacBook Air M2 Silver 13 Inci (Diperbarui)',
                'category' => $studentItem->category->value,
                'campus_zone_id' => $studentItem->campus_zone_id,
                'incident_date' => $studentItem->incident_date->toDateString(),
                'description' => $studentItem->description,
            ])
            ->assertRedirect(route('items.show', $studentItem));

        $this->assertSame('MacBook Air M2 Silver 13 Inci (Diperbarui)', $studentItem->fresh()->title);

        $this->actingAs($staff)
            ->post(route('items.cancel', $studentItem))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('items.cancel', $studentItem))
            ->assertRedirect(route('items.show', $studentItem));

        $this->assertSame(ItemStatus::Cancelled, $studentItem->fresh()->status);
    }

    public function test_only_custodian_or_admin_can_archive_open_item(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $custodian = User::where('email', config('demo.personas.custodian.email'))->firstOrFail();
        $openItem = ItemReport::where('status', ItemStatus::Open->value)->firstOrFail();

        $this->actingAs($student)->post(route('items.archive', $openItem))->assertForbidden();

        $this->actingAs($custodian)->post(route('items.archive', $openItem))->assertRedirect();

        $this->assertSame(ItemStatus::UnclaimedArchived, $openItem->fresh()->status);
        $this->assertNotNull($openItem->fresh()->archived_at);
    }
}
