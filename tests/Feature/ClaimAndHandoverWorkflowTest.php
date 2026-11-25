<?php

namespace Tests\Feature;

use App\Enums\ClaimStatus;
use App\Enums\CustodyType;
use App\Enums\ItemStatus;
use App\Enums\NotificationEventType;
use App\Models\ItemClaim;
use App\Models\ItemReport;
use App\Models\NotificationLog;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimAndHandoverWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_student_can_submit_blind_challenge_claim_and_duplicate_is_blocked(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $item = ItemReport::where('ticket_code', 'TK-2026-0011')->firstOrFail();

        $response = $this->actingAs($student)->post(route('claims.store', $item), [
            'claimant_answer' => 'Ada stiker komunitas Linux kecil di sudut kanan bawah dekat trackpad.',
        ]);

        $response->assertRedirect(route('items.show', $item));
        $this->assertDatabaseHas('item_claims', [
            'item_report_id' => $item->id,
            'claimant_id' => $student->id,
            'status' => ClaimStatus::Pending->value,
        ]);
        $this->assertDatabaseHas('notification_logs', [
            'related_item_id' => $item->id,
            'event_type' => NotificationEventType::ClaimSubmitted->value,
        ]);

        $duplicate = $this->actingAs($student)->post(route('claims.store', $item), [
            'claimant_answer' => 'Mencoba klaim ulang pada barang yang sama.',
        ]);
        $duplicate->assertSessionHasErrors('claimant_answer');
    }

    public function test_reporter_cannot_claim_own_found_item(): void
    {
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $ownItem = ItemReport::where('ticket_code', 'TK-2026-0011')->firstOrFail();

        $this->actingAs($staff)
            ->post(route('claims.store', $ownItem), ['claimant_answer' => 'Klaim barang sendiri'])
            ->assertForbidden();
    }

    public function test_student_cannot_approve_or_reject_claim(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $claim = ItemClaim::where('claim_code', 'CLM-2026-0001')->firstOrFail();

        $this->actingAs($student)->post(route('claims.approve', $claim))->assertForbidden();
        $this->actingAs($student)->post(route('claims.reject', $claim), ['review_notes' => 'Alasan tidak sah'])->assertForbidden();
    }

    public function test_custodian_approves_claim_generates_pin_auto_rejects_competing_claim_and_logs_notification(): void
    {
        $student = User::where('email', config('demo.personas.student.email'))->firstOrFail();
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $custodian = User::where('email', config('demo.personas.custodian.email'))->firstOrFail();

        $item = ItemReport::where('ticket_code', 'TK-2026-0016')->firstOrFail();

        $this->actingAs($student)->post(route('claims.store', $item), [
            'claimant_answer' => 'Ada inisial nama di bagian bawah botol minum.',
        ])->assertRedirect();

        $winningClaim = ItemClaim::where('item_report_id', $item->id)->where('claimant_id', $student->id)->firstOrFail();

        // Create a competing pending claim from staff
        $competingClaim = ItemClaim::create([
            'claim_code' => 'CLM-TEST-COMP',
            'item_report_id' => $item->id,
            'claimant_id' => $staff->id,
            'claimant_answer' => 'Tumbler hijau milik saya tanpa tanda khusus.',
            'status' => ClaimStatus::Pending,
        ]);

        $this->actingAs($custodian)
            ->post(route('claims.approve', $winningClaim), ['review_notes' => 'Cocok dengan alas botol'])
            ->assertRedirect();

        $winningClaim->refresh();
        $competingClaim->refresh();
        $item->refresh();

        $this->assertSame(ClaimStatus::Approved, $winningClaim->status);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $winningClaim->handover_pin);
        $this->assertSame(ItemStatus::ClaimApproved, $item->status);
        $this->assertSame(ClaimStatus::Rejected, $competingClaim->status);

        $log = NotificationLog::where('related_claim_id', $winningClaim->id)
            ->where('event_type', NotificationEventType::ClaimApproved->value)
            ->firstOrFail();
        $this->assertStringContainsString((string) $winningClaim->handover_pin, $log->body_id);
    }

    public function test_wrong_handover_pin_returns_validation_error_with_zero_db_change(): void
    {
        $custodian = User::where('email', config('demo.personas.custodian.email'))->firstOrFail();
        $claim = ItemClaim::where('claim_code', 'CLM-2026-0003')->firstOrFail();

        $response = $this->actingAs($custodian)->post(route('claims.handover', $claim), [
            'handover_pin' => '000000',
        ]);

        $response->assertSessionHasErrors('handover_pin');
        $this->assertSame(ClaimStatus::Approved, $claim->fresh()->status);
        $this->assertSame(ItemStatus::ClaimApproved, $claim->itemReport->fresh()->status);
    }

    public function test_valid_handover_pin_including_dashed_format_completes_handover(): void
    {
        $custodian = User::where('email', config('demo.personas.custodian.email'))->firstOrFail();
        $claim = ItemClaim::where('claim_code', 'CLM-2026-0003')->firstOrFail();

        $response = $this->actingAs($custodian)->post(route('claims.handover', $claim), [
            'handover_pin' => '482-910',
        ]);

        $response->assertRedirect();
        $claim->refresh();

        $this->assertSame(ClaimStatus::Completed, $claim->status);
        $this->assertNotNull($claim->handed_over_at);
        $this->assertSame(ItemStatus::Returned, $claim->itemReport->status);
        $this->assertNotNull($claim->itemReport->returned_at);

        $this->assertDatabaseHas('notification_logs', [
            'related_claim_id' => $claim->id,
            'event_type' => NotificationEventType::HandoverCompleted->value,
        ]);
    }

    public function test_finder_can_verify_pin_when_custody_type_is_with_finder(): void
    {
        $staff = User::where('email', config('demo.personas.staff.email'))->firstOrFail();
        $claim = ItemClaim::where('claim_code', 'CLM-2026-0004')->firstOrFail();

        $this->assertSame(CustodyType::WithFinder, $claim->itemReport->custody_type);

        $this->actingAs($staff)
            ->post(route('claims.handover', $claim), ['handover_pin' => '739204'])
            ->assertRedirect();

        $this->assertSame(ClaimStatus::Completed, $claim->fresh()->status);
        $this->assertSame(ItemStatus::Returned, $claim->itemReport->fresh()->status);
    }
}
