<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('general_configuration', function (Blueprint $table) {
            $table->json('prices')->after('plan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('general_configuration', function (Blueprint $table) {
            $table->dropColumn('prices');
        });
    }
};
