<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_messages', function (Blueprint $table) {
            $table->id();
            $table->timestamp('server_created_at');
            $table->timestamp('device_created_at');
            $table->string('device_login');
            $table->string('cow_code');
            $table->integer('yield')->unsigned();
            $table->integer('impulses')->unsigned();
            $table->integer('battery')->unsigned();
            $table->integer('error')->unsigned();
            $table->integer('message_num')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_messages');
    }
}
