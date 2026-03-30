<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1) Ensure specializations table exists
        if (! Schema::hasTable('specializations')) {
            throw new RuntimeException('specializations table is missing; run migrations in order.');
        }

        // 2) Rename teachers.specialty_id -> specialization_id (keeping existing numeric ids temporarily)
        Schema::table('teachers', function (Blueprint $table) {
            // Drop existing FK first to allow rename cleanly
            $table->dropForeign(['specialty_id']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->renameColumn('specialty_id', 'specialization_id');
        });

        // 3) Create specializations from the old linked specialties' descriptions
        // Build map: old specialty_id -> description
        $old = DB::table('teachers')
            ->select('specialization_id')
            ->whereNotNull('specialization_id')
            ->distinct()
            ->pluck('specialization_id')
            ->all();

        if (! empty($old)) {
            $rows = DB::table('specialties')
                ->whereIn('id', $old)
                ->select('id', 'description')
                ->get();

            foreach ($rows as $r) {
                if (! $r->description) {
                    continue;
                }
                DB::table('specializations')->updateOrInsert(
                    ['description' => $r->description],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }

            // Map description -> new specialization id
            $specMap = DB::table('specializations')->pluck('id', 'description');

            // Update teachers.specialization_id to the new id (by joining old specialty description)
            foreach ($rows as $r) {
                $newId = $specMap[$r->description] ?? null;
                if (! $newId) {
                    continue;
                }
                DB::table('teachers')
                    ->where('specialization_id', $r->id)
                    ->update(['specialization_id' => $newId]);
            }
        }

        // 4) Re-add FK to specializations
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign('specialization_id')->references('id')->on('specializations');
        });
    }

    public function down()
    {
        // Revert FK, column name back, but we can't reliably restore old specialty ids.
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['specialization_id']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->renameColumn('specialization_id', 'specialty_id');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->foreign('specialty_id')->references('id')->on('specialties');
        });
    }
};

