<?php

namespace Database\Factories;

use App\Models\organisator;
use Illuminate\Database\Eloquent\Factories\Factory;

class organisatorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = organisator::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 2, //default author by user seeder class
            'organisator_city_id' => 1,
            'logo' => 'images/organisators/logos/',
            'title' => 'Web App developer',
            'description' => 'Sport events',
            'website' => 'https://www.organisatorwebsite.com',
            'cover_img' => 'nocover',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ];
    }
}
