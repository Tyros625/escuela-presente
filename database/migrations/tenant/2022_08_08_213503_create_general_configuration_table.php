<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('general_configuration', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cct');
            $table->string('modality');
            $table->string('address');
            $table->json('coordinates');
            $table->string('email');
            $table->string('phone');
            $table->string('website');
            $table->json('fiscal_data');
            $table->string('logo')->default('/image/no-image.jpg');
            $table->string('last_enrollment')->default(0);
            $table->json('plan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('general_configuration');
    }
};
