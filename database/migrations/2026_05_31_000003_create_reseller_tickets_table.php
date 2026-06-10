<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reseller_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reseller_id')->index();
            $table->unsignedInteger('order_id')->nullable()->index(); // kon order niye issue
            $table->string('subject');
            $table->string('status')->default('open'); // open | answered | closed
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reseller_tickets');
    }
};
