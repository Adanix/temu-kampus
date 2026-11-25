<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_claims', function (Blueprint $table): void {
            $table->id();
            $table->string('claim_code', 24)->unique();
            $table->foreignId('item_report_id')->constrained('item_reports')->cascadeOnDelete();
            $table->foreignId('claimant_id')->constrained('users')->restrictOnDelete();
            $table->text('claimant_answer');
            $table->string('proof_photo_path', 255)->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('handover_pin', 6)->nullable();
            $table->foreignId('handed_over_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_claims');
    }
};
