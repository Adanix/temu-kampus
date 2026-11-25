<?php

namespace App\Services;

use App\Enums\ClaimStatus;
use App\Enums\CustodyType;
use App\Enums\ItemStatus;
use App\Enums\ItemType;
use App\Enums\NotificationEventType;
use App\Enums\UserRole;
use App\Models\ItemClaim;
use App\Models\ItemReport;
use App\Models\User;
use App\Services\Notifications\NotificationDispatcherInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimWorkflowService
{
    public function __construct(
        private readonly NotificationDispatcherInterface $notifier,
    ) {}

    /**
     * Submit a new Blind Challenge claim against an open found item.
     */
    public function submitClaim(ItemReport $item, User $claimant, string $answer, ?UploadedFile $proofPhoto = null): ItemClaim
    {
        $claim = DB::transaction(function () use ($item, $claimant, $answer, $proofPhoto): ItemClaim {
            $lockedItem = ItemReport::whereKey($item->id)->lockForUpdate()->firstOrFail();

            if ($lockedItem->type !== ItemType::Found || $lockedItem->status !== ItemStatus::Open) {
                throw ValidationException::withMessages([
                    'claimant_answer' => __('app.errors.claim_no_longer_open'),
                ]);
            }

            $duplicateExists = ItemClaim::where('item_report_id', $lockedItem->id)
                ->where('claimant_id', $claimant->id)
                ->whereIn('status', [ClaimStatus::Pending->value, ClaimStatus::Approved->value])
                ->exists();

            if ($duplicateExists) {
                throw ValidationException::withMessages([
                    'claimant_answer' => __('app.errors.duplicate_claim'),
                ]);
            }

            $proofPath = $proofPhoto?->store('claim-proofs', 'public');

            return ItemClaim::create([
                'claim_code' => ItemClaim::generateClaimCode(),
                'item_report_id' => $lockedItem->id,
                'claimant_id' => $claimant->id,
                'claimant_answer' => $answer,
                'proof_photo_path' => $proofPath,
                'status' => ClaimStatus::Pending,
            ]);
        });

        $item->loadMissing('reporter');

        $custodians = User::where('role', UserRole::Custodian->value)->get();
        foreach ($custodians as $custodian) {
            $this->notifier->dispatch(
                recipient: $custodian,
                eventType: NotificationEventType::ClaimSubmitted,
                subjectId: sprintf('Klaim baru %s menunggu verifikasi (%s)', $claim->claim_code, $item->ticket_code),
                subjectEn: sprintf('New claim %s awaiting review (%s)', $claim->claim_code, $item->ticket_code),
                bodyId: sprintf(
                    '%s mengajukan klaim untuk barang temuan "%s" (%s). Silakan bandingkan jawaban Blind Challenge dengan ciri rahasia di Meja Petugas Pos.',
                    $claimant->name,
                    $item->title,
                    $item->ticket_code,
                ),
                bodyEn: sprintf(
                    '%s submitted a claim for found item "%s" (%s). Please compare their Blind Challenge answer with the hidden traits at the Custodian Desk.',
                    $claimant->name,
                    $item->title,
                    $item->ticket_code,
                ),
                relatedItemId: $item->id,
                relatedClaimId: $claim->id,
            );
        }

        if ($item->custody_type === CustodyType::WithFinder && $item->reporter !== null && ! $custodians->contains('id', $item->reporter_id)) {
            $this->notifier->dispatch(
                recipient: $item->reporter,
                eventType: NotificationEventType::ClaimSubmitted,
                subjectId: sprintf('Klaim baru untuk temuan %s Anda', $item->ticket_code),
                subjectEn: sprintf('New claim submitted for your found item %s', $item->ticket_code),
                bodyId: sprintf(
                    '%s mengajukan klaim atas barang temuan "%s" (%s) yang masih Anda simpan. Petugas pos akan meninjau klaim terlebih dahulu.',
                    $claimant->name,
                    $item->title,
                    $item->ticket_code,
                ),
                bodyEn: sprintf(
                    '%s submitted a claim for found item "%s" (%s) currently held by you. A custody officer will review the claim first.',
                    $claimant->name,
                    $item->title,
                    $item->ticket_code,
                ),
                relatedItemId: $item->id,
                relatedClaimId: $claim->id,
            );
        }

        return $claim;
    }

    /**
     * Atomically approve a pending claim, generate a 6-digit handover PIN, and reject competing pending claims.
     */
    public function approveClaim(ItemClaim $claim, User $reviewer, ?string $notes = null): ItemClaim
    {
        /** @var array{claim: ItemClaim, item: ItemReport, competing: Collection<int, ItemClaim>} $result */
        $result = DB::transaction(function () use ($claim, $reviewer, $notes): array {
            $lockedItem = ItemReport::with('custodyPost')->whereKey($claim->item_report_id)->lockForUpdate()->firstOrFail();
            $freshClaim = ItemClaim::with('claimant')->whereKey($claim->id)->firstOrFail();

            if ($lockedItem->status !== ItemStatus::Open || $freshClaim->status !== ClaimStatus::Pending) {
                throw ValidationException::withMessages([
                    'claim' => __('app.errors.claim_no_longer_open'),
                ]);
            }

            $pin = ItemClaim::generateHandoverPin();

            $freshClaim->update([
                'status' => ClaimStatus::Approved,
                'reviewer_id' => $reviewer->id,
                'review_notes' => $notes,
                'reviewed_at' => now(),
                'handover_pin' => $pin,
            ]);

            $lockedItem->update([
                'status' => ItemStatus::ClaimApproved,
            ]);

            $competingClaims = ItemClaim::with('claimant')
                ->where('item_report_id', $lockedItem->id)
                ->where('id', '!=', $freshClaim->id)
                ->where('status', ClaimStatus::Pending->value)
                ->get();

            foreach ($competingClaims as $competing) {
                $competing->update([
                    'status' => ClaimStatus::Rejected,
                    'reviewer_id' => $reviewer->id,
                    'review_notes' => __('app.claims.auto_rejected_note'),
                    'reviewed_at' => now(),
                ]);
            }

            return [
                'claim' => $freshClaim->fresh(['claimant', 'itemReport.custodyPost']),
                'item' => $lockedItem,
                'competing' => $competingClaims,
            ];
        });

        $approvedClaim = $result['claim'];
        $lockedItem = $result['item'];

        $custodyInfoId = $lockedItem->custodyPost !== null
            ? sprintf('%s (%s)', $lockedItem->custodyPost->name_id, $lockedItem->custodyPost->operating_hours)
            : 'Penemu barang (sesuai koordinasi pos)';
        $custodyInfoEn = $lockedItem->custodyPost !== null
            ? sprintf('%s (%s)', $lockedItem->custodyPost->name_en, $lockedItem->custodyPost->operating_hours)
            : 'Held by finder (coordinate pickup)';

        if ($approvedClaim->claimant !== null) {
            $this->notifier->dispatch(
                recipient: $approvedClaim->claimant,
                eventType: NotificationEventType::ClaimApproved,
                subjectId: sprintf('Klaim %s disetujui — PIN Serah Terima: %s', $approvedClaim->claim_code, $approvedClaim->handover_pin),
                subjectEn: sprintf('Claim %s approved — Handover PIN: %s', $approvedClaim->claim_code, $approvedClaim->handover_pin),
                bodyId: sprintf(
                    'Selamat! Klaim Anda untuk "%s" (%s) telah disetujui. Tunjukkan PIN 6 digit %s kepada petugas di %s saat mengambil barang.',
                    $lockedItem->title,
                    $lockedItem->ticket_code,
                    $approvedClaim->handover_pin,
                    $custodyInfoId,
                ),
                bodyEn: sprintf(
                    'Your claim for "%s" (%s) has been approved. Present the 6-digit PIN %s at %s when collecting your item.',
                    $lockedItem->title,
                    $lockedItem->ticket_code,
                    $approvedClaim->handover_pin,
                    $custodyInfoEn,
                ),
                relatedItemId: $lockedItem->id,
                relatedClaimId: $approvedClaim->id,
            );
        }

        foreach ($result['competing'] as $competingClaim) {
            if ($competingClaim->claimant !== null) {
                $this->notifier->dispatch(
                    recipient: $competingClaim->claimant,
                    eventType: NotificationEventType::ClaimRejected,
                    subjectId: sprintf('Pembaruan klaim %s (%s)', $competingClaim->claim_code, $lockedItem->ticket_code),
                    subjectEn: sprintf('Claim update for %s (%s)', $competingClaim->claim_code, $lockedItem->ticket_code),
                    bodyId: sprintf(
                        'Klaim Anda pada "%s" (%s) ditutup otomatis karena klaim lain telah diverifikasi dan disetujui.',
                        $lockedItem->title,
                        $lockedItem->ticket_code,
                    ),
                    bodyEn: sprintf(
                        'Your claim for "%s" (%s) was closed automatically because another claim passed verification.',
                        $lockedItem->title,
                        $lockedItem->ticket_code,
                    ),
                    relatedItemId: $lockedItem->id,
                    relatedClaimId: $competingClaim->id,
                );
            }
        }

        return $approvedClaim;
    }

    /**
     * Reject a pending claim with mandatory review notes.
     */
    public function rejectClaim(ItemClaim $claim, User $reviewer, string $notes): ItemClaim
    {
        $updated = DB::transaction(function () use ($claim, $reviewer, $notes): ItemClaim {
            $freshClaim = ItemClaim::with(['claimant', 'itemReport'])->whereKey($claim->id)->lockForUpdate()->firstOrFail();

            if ($freshClaim->status !== ClaimStatus::Pending) {
                throw ValidationException::withMessages([
                    'claim' => __('app.errors.claim_no_longer_open'),
                ]);
            }

            $freshClaim->update([
                'status' => ClaimStatus::Rejected,
                'reviewer_id' => $reviewer->id,
                'review_notes' => $notes,
                'reviewed_at' => now(),
            ]);

            return $freshClaim;
        });

        if ($updated->claimant !== null) {
            $this->notifier->dispatch(
                recipient: $updated->claimant,
                eventType: NotificationEventType::ClaimRejected,
                subjectId: sprintf('Klaim %s ditolak (%s)', $updated->claim_code, $updated->itemReport->ticket_code),
                subjectEn: sprintf('Claim %s rejected (%s)', $updated->claim_code, $updated->itemReport->ticket_code),
                bodyId: sprintf(
                    'Klaim Anda untuk "%s" belum dapat disetujui. Catatan petugas: %s',
                    $updated->itemReport->title,
                    $notes,
                ),
                bodyEn: sprintf(
                    'Your claim for "%s" could not be approved. Reviewer note: %s',
                    $updated->itemReport->title,
                    $notes,
                ),
                relatedItemId: $updated->item_report_id,
                relatedClaimId: $updated->id,
            );
        }

        return $updated;
    }

    /**
     * Verify 6-digit handover PIN and atomically complete custody transfer.
     */
    public function completeHandover(ItemClaim $claim, User $verifier, string $submittedPin): ItemClaim
    {
        /** @var array{claim: ItemClaim, item: ItemReport} $result */
        $result = DB::transaction(function () use ($claim, $verifier, $submittedPin): array {
            $lockedItem = ItemReport::with('reporter')->whereKey($claim->item_report_id)->lockForUpdate()->firstOrFail();
            $freshClaim = ItemClaim::with('claimant')->whereKey($claim->id)->firstOrFail();

            if ($freshClaim->status !== ClaimStatus::Approved) {
                throw ValidationException::withMessages([
                    'handover_pin' => __('app.errors.claim_no_longer_open'),
                ]);
            }

            if (! hash_equals((string) $freshClaim->handover_pin, (string) $submittedPin)) {
                throw ValidationException::withMessages([
                    'handover_pin' => __('app.errors.invalid_handover_pin'),
                ]);
            }

            $now = now();

            $freshClaim->update([
                'status' => ClaimStatus::Completed,
                'handed_over_by' => $verifier->id,
                'handed_over_at' => $now,
            ]);

            $lockedItem->update([
                'status' => ItemStatus::Returned,
                'returned_at' => $now,
            ]);

            return [
                'claim' => $freshClaim->fresh(['claimant', 'handedOverBy', 'itemReport']),
                'item' => $lockedItem,
            ];
        });

        $completedClaim = $result['claim'];
        $lockedItem = $result['item'];

        if ($completedClaim->claimant !== null) {
            $this->notifier->dispatch(
                recipient: $completedClaim->claimant,
                eventType: NotificationEventType::HandoverCompleted,
                subjectId: sprintf('Serah terima selesai untuk %s', $lockedItem->ticket_code),
                subjectEn: sprintf('Handover completed for %s', $lockedItem->ticket_code),
                bodyId: sprintf(
                    'Barang "%s" (%s) telah resmi diserahkan melalui verifikasi PIN dan dicatat sebagai Dikembalikan oleh %s.',
                    $lockedItem->title,
                    $lockedItem->ticket_code,
                    $verifier->name,
                ),
                bodyEn: sprintf(
                    'Item "%s" (%s) has been officially handed over via PIN verification and marked as Returned by %s.',
                    $lockedItem->title,
                    $lockedItem->ticket_code,
                    $verifier->name,
                ),
                relatedItemId: $lockedItem->id,
                relatedClaimId: $completedClaim->id,
            );
        }

        if ($lockedItem->reporter !== null && $lockedItem->reporter_id !== $completedClaim->claimant_id) {
            $this->notifier->dispatch(
                recipient: $lockedItem->reporter,
                eventType: NotificationEventType::HandoverCompleted,
                subjectId: sprintf('Temuan %s telah kembali ke pemilik', $lockedItem->ticket_code),
                subjectEn: sprintf('Found item %s has been returned to owner', $lockedItem->ticket_code),
                bodyId: sprintf(
                    'Terima kasih! Barang temuan "%s" (%s) yang Anda laporkan telah berhasil diserahkan kepada pemilik sahnya.',
                    $lockedItem->title,
                    $lockedItem->ticket_code,
                ),
                bodyEn: sprintf(
                    'Thank you! The found item "%s" (%s) you reported has been verified and returned to its rightful owner.',
                    $lockedItem->title,
                    $lockedItem->ticket_code,
                ),
                relatedItemId: $lockedItem->id,
                relatedClaimId: $completedClaim->id,
            );
        }

        return $completedClaim;
    }
}
