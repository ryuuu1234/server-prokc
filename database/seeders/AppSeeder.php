<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('apps')->insert([
            'name' => 'prokc',
            'no_cs' => '085204569382',
            'wa_cs' => '085204569382',
        ]);
      
    }
}
