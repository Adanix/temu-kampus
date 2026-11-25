<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_matches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lost_item_id')->constrained('item_reports')->cascadeOnDelete();
            $table->foreignId('found_item_id')->constrained('item_reports')->cascadeOnDelete();
            $table->unsignedTinyInteger('score')->index();
            $table->json('match_reasons');
            $table->boolean('is_dismissed')->default(false);
            $table->timestamps();

            $table->unique(['lost_item_id', 'found_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_matches');
    }
};
