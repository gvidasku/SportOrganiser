<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\organisator;
use App\Models\Post;

class organisatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $details = [
            [
                'title' => 'Krepšinis 3x3',
                'level' => 'Mėgėjiškas',
                'sportcategory' => 'Krepšinis',
                'sporttype' => 'mokamas',
            ], [
                'title' => 'Plaukimas 100m.',
                'level' => 'Senior level',
                'sportcategory' => 'Plaukimas',
                'sporttype' => 'nemokamai',
            ], 
        ];
        //user id is 2 that has author role
        $organisator = organisator::factory()->create([
            'organisator_city_id' => 1,
            'title' => 'Sport organisator',
            'logo' => 'images/logo/4.png',
        ]);
        foreach ($details as $index => $detail) {
            $post = Post::factory()->create([
                'organisator_id' => $organisator->id,
                'sportevent_title' => $detail['title'],
                'sport_category' => $detail['level'],
                'event_type' => $detail['sportcategory'],
                'level' => $detail['sporttype'],
            ]);
        }
    }
}
