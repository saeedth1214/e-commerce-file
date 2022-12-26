<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $names = ['ورزشی', 'خانوادگی', 'مذهبی', 'جشنواره', 'موسیقی'];
        return [
            'name' => $this->faker->randomElement($names),
            'slug' => $this->faker->slug(1),
        ];
    }
}
