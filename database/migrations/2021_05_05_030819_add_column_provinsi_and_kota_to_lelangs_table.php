<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnProvinsiAndKotaToLelangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lelangs', function (Blueprint $table) {
            $table->string('provinsi')->nullable()->after('deskripsi');
            $table->string('kota')->nullable()->after('provinsi');
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
            $table->dropColumn('provinsi');
            $table->dropColumn('kota');
        });
    }
}
