<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $tableName = 'student_relatives';

    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->json('father_data')->nullable();
            $table->json('mother_data')->nullable();
            $table->json('authorized_persons')->nullable();
            $table->json('roommates')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
};
