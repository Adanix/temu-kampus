<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custody_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campus_zone_id')->constrained('campus_zones')->restrictOnDelete();
            $table->string('name_id', 120);
            $table->string('name_en', 120);
            $table->string('operating_hours', 80);
            $table->string('contact_ext', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custody_posts');
    }
};
