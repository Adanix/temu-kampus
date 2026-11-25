<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('ticket_code', 24)->unique();
            $table->foreignId('reporter_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 10)->index();
            $table->string('title', 120);
            $table->string('category', 30)->index();
            $table->foreignId('campus_zone_id')->constrained('campus_zones')->restrictOnDelete();
            $table->string('location_detail', 160)->nullable();
            $table->date('incident_date')->index();
            $table->string('color', 40)->nullable();
            $table->string('brand', 60)->nullable();
            $table->text('description');
            $table->string('photo_path', 255)->nullable();
            $table->boolean('is_sensitive')->default(false);
            $table->text('hidden_traits')->nullable();
            $table->string('custody_type', 20)->nullable();
            $table->foreignId('custody_post_id')->nullable()->constrained('custody_posts')->nullOnDelete();
            $table->string('status', 24)->default('open')->index();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_reports');
    }
};
