<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToDeviceMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_messages', function (Blueprint $table) {
            $table->index('device_login');
            $table->index('cow_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_messages', function (Blueprint $table) {
            $table->dropIndex(['device_login']);
            $table->dropIndex(['cow_code']);
        });
    }
}
