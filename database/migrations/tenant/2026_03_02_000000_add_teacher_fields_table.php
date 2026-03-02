<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('last_name_father')->nullable()->after('name');
            $table->string('last_name_mother')->nullable()->after('last_name_father');
            $table->string('rfc')->nullable()->after('last_name_mother');
            $table->decimal('max_hours_per_week', 5, 2)->nullable()->after('specialty_id');
            $table->string('available_hours')->nullable()->after('max_hours_per_week');
            $table->string('institutional_email')->nullable()->after('email');
        });

        // Migrar datos existentes: last_name -> last_name_father (compatibilidad)
        DB::table('teachers')->whereNotNull('last_name')->update([
            'last_name_father' => DB::raw('last_name'),
        ]);
    }

    public function down()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'last_name_father',
                'last_name_mother',
                'rfc',
                'max_hours_per_week',
                'available_hours',
                'institutional_email',
            ]);
        });
    }
};
