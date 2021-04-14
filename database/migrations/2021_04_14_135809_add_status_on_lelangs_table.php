<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusOnLelangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lelangs', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('deskripsi')->comment = '0. blm publish, 1.publish, 2.berakhir, 3.laku';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lelangs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
