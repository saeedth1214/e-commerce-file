<?php

namespace Database\Factories;

use App\Enums\VoucherTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->slug(1),
            'rebate' => $this->faker->randomNumber(2),
            'percentage' => 1,
            'expired_at' => $this->faker->dateTime(),
            'status' => $this->faker->randomElement([0, 1]),
            'type' => $this->faker->randomElement(VoucherTypeEnum::asArray()),
        ];
    }
}
