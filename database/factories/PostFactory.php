<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sportevent_title' => 'Futbolo varžybos',
            'sport_category' => 'Futbolas',
            'attendance' => rand(1, 10), // password
            'event_type' => 'Mokamas',
            'sportevent_location' => 'Vgtu sporto centras',
            'date' => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +2 days")),
            'level' => 'Profesionalus',
            'age' => '18-30m.',
            'price' => '10',
            'time' => '12:00',
            'description' => '<p></p>',
        ];
    }
}
