<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $tableName = 'student_healths';

    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->string('current_general_status')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('chronic_disease')->nullable();
            $table->boolean('has_medical_service')->default(true);
            $table->string('medical_service_number')->nullable();
            $table->string('medical_service_name')->nullable();
            $table->json('familiar_affection')->nullable();
            $table->json('medical_care')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
};
