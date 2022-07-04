<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawDeviceMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_device_messages', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('message', 1000);
            $table->timestamp('parsed_at')->nullable(); // когда скипт ЛК обработал это сырое сообщение
            $table->index(['parsed_at']);
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
        Schema::dropIfExists('raw_device_messages');
    }
}
