<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaLelangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_lelangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lelang_id');
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->tinyInteger('status')->default(0)->comment = '0. no-thumbnail, 1.thumbnail/default';
            $table->timestamps();


            $table->foreign('lelang_id')->references('id')->on('lelangs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_lelangs');
    }
}
