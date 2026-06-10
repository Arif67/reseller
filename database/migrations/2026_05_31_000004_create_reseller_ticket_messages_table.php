<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reseller_ticket_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id')->index();
            $table->string('sender'); // reseller | admin
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_ticket_messages');
    }
};
