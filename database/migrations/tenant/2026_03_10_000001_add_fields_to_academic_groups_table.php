<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->string('shift')->default('morning')->after('school_cycle_id');
            $table->string('room_name')->nullable()->after('shift');
            $table->json('subjects')->nullable()->after('student_limit');
        });
    }

    public function down(): void
    {
        Schema::table('academic_groups', function (Blueprint $table) {
            $table->dropColumn(['shift', 'room_name', 'subjects']);
        });
    }
};

