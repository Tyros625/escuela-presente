<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('specialties', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->after('description');
            $table->unsignedBigInteger('grade_id')->nullable()->after('code');
            $table->unsignedBigInteger('school_cycle_id')->nullable()->after('grade_id');
            $table->unsignedSmallInteger('hours_per_week')->nullable()->after('school_cycle_id');
            $table->unsignedSmallInteger('credits')->nullable()->after('hours_per_week');
            $table->string('training_field')->nullable()->after('credits');

            $table->index('code');
            $table->index('grade_id');
            $table->index('school_cycle_id');
        });
    }

    public function down(): void
    {
        Schema::table('specialties', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['grade_id']);
            $table->dropIndex(['school_cycle_id']);

            $table->dropColumn([
                'code',
                'grade_id',
                'school_cycle_id',
                'hours_per_week',
                'credits',
                'training_field',
            ]);
        });
    }
};
