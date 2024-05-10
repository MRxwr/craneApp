<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->integer('client_id');
            $table->string('from_location');
            $table->string('to_location');
            $table->string('from_latlong');
            $table->string('to_latlong');
            $table->integer('service_id');
            $table->integer('is_active')->default(1);
            $table->integer('is_deleted')->default(0);
            $table->timestamps();
        });
        
        Schema::create('booking_logs', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->integer('client_id');
            $table->integer('driver_id');
            $table->string('activity');
            $table->integer('flag');
            $table->timestamps();
        });
        Schema::create('request_prices', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->integer('client_id');
            $table->integer('driver_id');
            $table->string('price');
            $table->string('is_accepted');
            $table->timestamps();
        });
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->integer('app_user_id');
            $table->string('amount');
            $table->integer('mode');
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
        Schema::dropIfExists('booking_requests');
        Schema::dropIfExists('request_prices');
        Schema::dropIfExists('booking_logs');
        Schema::dropIfExists('wallets');
    }
}
