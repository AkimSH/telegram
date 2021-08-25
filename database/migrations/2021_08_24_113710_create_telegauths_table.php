<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegauthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegauths', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id')->nullable()->default(null);
            $table->string('surname');
            $table->string('verification_code');
            $table->boolean('verified')->default(false);
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
        Schema::dropIfExists('telegauths');
    }
}
