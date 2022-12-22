<?php

namespace Database\Factories;

use App\Enums\OrderTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $uIds = User::query()->get()->modelKeys();
        $amount = $this->faker->numberBetween(10000, 100000);
        return [
            'user_id' => $this->faker->randomElement($uIds),
            'total_items' => $this->faker->numberBetween(1, 3),
            'total_amount' => $amount,
            'total_amount_after_rebate_code' => $amount,
            'status' => $this->faker->randomElement(OrderTypeEnum::asArray()),
            'created_at' => $this->faker->dateTimeBetween('-90 days','now'),
        ];
    }
}
