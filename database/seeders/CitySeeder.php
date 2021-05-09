<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class citySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $city = [
            'Vilnius',
            'Kaunas',
            'Klaipėda', 'Panėvežys',
            'Šiauliai ',
            'Altyus',
            'Marijampolė',
            'Mažeikiai', 'Jonava', 'Utena
        ];
        foreach ($city as $city) {
            DB::table('organisator_city')->insert([
                'city_name' => $city
            ]);
        }
    }
}
