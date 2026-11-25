<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('student')->index()->after('password');
            $table->string('campus_unit', 120)->nullable()->after('role');
            $table->string('student_staff_code', 40)->nullable()->after('campus_unit');
            $table->string('preferred_locale', 5)->default('id')->after('student_staff_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'campus_unit', 'student_staff_code', 'preferred_locale']);
        });
    }
};
