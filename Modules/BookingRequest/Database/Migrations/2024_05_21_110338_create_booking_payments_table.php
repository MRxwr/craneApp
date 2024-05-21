<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->integer('client_id');
            $table->string('transaction_id');
            $table->string('payment_type');
            $table->string('payment_status');
            $table->string('is_coupon')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('coupon_discount')->nullable();
            $table->string('payment_amount');
            $table->string('remark');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_payments');
    }
}
