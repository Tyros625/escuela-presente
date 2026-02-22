<?php

use Illuminate\Database\Migrations\Migration;

class EnsureDefaultRolesExist extends Migration
{
    /**
     * Ensure the 4 default roles (Administrador, Docente, Estudiante, Padre/Tutor)
     * exist in every tenant so they appear on Roles page and when adding users.
     */
    public function up(): void
    {
        $seeder = app()->make(\Database\Seeders\RolesSeeder::class);
        $seeder->run();
    }

    /**
     * Reverse not required - we do not remove default roles.
     */
    public function down(): void
    {
        //
    }
}
