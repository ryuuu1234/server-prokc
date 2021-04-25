<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->insert([
            'name' => 'BCA',
            'description' => 'BANK BCA Cabang Kota Probolinggo',
            'acc' => '9412894982141',
            'an' => 'Harun Al-Rasyid',
        ]);
    }
}
