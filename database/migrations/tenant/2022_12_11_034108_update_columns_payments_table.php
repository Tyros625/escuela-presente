<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'payment_status', 'reference']);
            $table->string('payment_type')->after('id')->nullable();
            $table->string('merchant_order_id')->after('id')->nullable();
            $table->string('payment_id')->after('id')->nullable();
            $table->string('status')->after('id')->nullable();
            $table->string('preference_id')->after('id')->nullable();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['conekta_id']);
            $table->string('mercado_pago_id')->after('academic_group_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['preference_id', 'merchant_order_id', 'payment_id', 'status', 'payment_type']);
            $table->string('order_id');
            $table->string('payment_status');
            $table->string('reference')->nullable();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['mercado_pago_id']);
            $table->string('conekta_id')->after('academic_group_id')->nullable();
        });
    }
};
