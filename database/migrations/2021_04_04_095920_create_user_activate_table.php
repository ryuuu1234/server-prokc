<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_activates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->integer('bank_id')->nullable();
            $table->timestamp('tanggal')->nullable();
            $table->string('image')->nullable();
            $table->integer('nominal')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_activates');
    }
}
