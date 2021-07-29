<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceOwnerChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_owner_changes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('old_client_id');
            $table->bigInteger('new_client_id');
            $table->string('device_login');
            $table->date('changed_at');
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
        Schema::dropIfExists('device_owner_changes');
    }
}
