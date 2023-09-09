<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $tableName = 'students';

    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('enrollment')->unique();
            $table->string('name');
            $table->string('last_name_father');
            $table->string('last_name_mother');
            $table->enum('nationality', ['MEXICANA', 'EXTRANJERA']);
            $table->string('curp');
            $table->date('date_birth');
            $table->string('place_birth');
            $table->enum('sex', ['MASCULINO', 'FEMENINO']);
            $table->string('weight');
            $table->string('height');
            $table->boolean('is_migrant')->default(false);
            $table->string('indigenous_group');
            $table->string('indigenous_language');
            $table->string('disability');
            $table->string('health_insurance');
            $table->string('scholarship');
            $table->string('address');
            $table->string('colony');
            $table->string('postal_code');
            $table->string('municipality');
            $table->string('federal_entity');
            $table->string('home_phone');
            $table->string('email');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
};
