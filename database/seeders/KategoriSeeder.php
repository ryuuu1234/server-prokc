<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cat = [
            'Aka-muji', 'Asagi', 'Bekko', 'Chagoi', 'Doitsu', 'Ginrin', 'Goromo', 'Goshiki',
            'Hageshiro', 'Hariwake', 'Heisei Nishiki', 'Hikari Utsurimono', 'Hikarimono', 'Hikarimoyomono',
            'Karashi', 'Karasugoi', 'Kawarimono', 'Kigoi', 'Kikokuryu', 'Kikushui', 'Kinsui', 'Kohaku', 'Koromo', 'Kujaku', 'Kumonryu',
            'Midorigoi', 'Mix Koi', 'Ochiba', 'Ogon', 'Platinum', 'Sanke', 'Shiro', 'Showa', 'Shuzui', 'Soragoi', 'Tancho',
            'Utsuri', 'Yamato Nishiki'
        ];

        for ($i=0; $i < count($cat); $i++) { 
            DB::table('Kategoris')->insert([
    			'name' => $cat[$i],
    		]);
        }
    }
}
