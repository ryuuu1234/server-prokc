<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('notelp')->nullable()->after('avatar');
            $table->string('nowhatsapp')->nullable()->after('notelp');
            $table->string('alamat')->nullable()->after('nowhatsapp');
            $table->string('provinsi')->nullable()->after('alamat');
            $table->string('kota')->nullable()->after('provinsi');
            $table->tinyInteger('status')->default(1)->after('kota');
            $table->tinyInteger('bidder')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notelp');
            $table->dropColumn('nowhatsapp');
            $table->dropColumn('alamat');
            $table->dropColumn('provinsi');
            $table->dropColumn('kota');
            $table->dropColumn('status');
            $table->dropColumn('bidder');
        });
    }
}
