<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_type', 40)->index();
            $table->string('channel', 20)->default('email_in_app');
            $table->string('delivery_mode', 20)->default('simulated');
            $table->string('status', 24)->default('simulated_sent');
            $table->string('subject_id', 160);
            $table->string('subject_en', 160);
            $table->text('body_id');
            $table->text('body_en');
            $table->foreignId('related_item_id')->nullable()->constrained('item_reports')->nullOnDelete();
            $table->foreignId('related_claim_id')->nullable()->constrained('item_claims')->nullOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
