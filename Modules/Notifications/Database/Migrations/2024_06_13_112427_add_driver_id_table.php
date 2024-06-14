<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDriverIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('client_id')->default(0);
            $table->integer('driver_id')->default(0);
            $table->longText('message')->nullable();
            $table->string('NotificationReciver')->default('');
            $table->dropColumn('app_user_id');
            $table->dropColumn('text');
            $table->dropColumn('token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
