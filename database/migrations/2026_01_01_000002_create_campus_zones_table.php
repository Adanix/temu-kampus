<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_zones', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name_id', 100);
            $table->string('name_en', 100);
            $table->string('zone_group', 40)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_zones');
    }
};
