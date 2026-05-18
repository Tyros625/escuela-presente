<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_configuration', function (Blueprint $table) {
            $table->json('school_schedule')->nullable()->after('custom_messages');
            $table->json('tardiness_schedule')->nullable()->after('school_schedule');
        });
    }

    public function down(): void
    {
        Schema::table('general_configuration', function (Blueprint $table) {
            $table->dropColumn(['school_schedule', 'tardiness_schedule']);
        });
    }
};
