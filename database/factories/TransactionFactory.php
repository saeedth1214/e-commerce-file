<?php

namespace Database\Factories;

use App\Enums\TransactionStatusEnum;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $oIds = Order::query()->get()->modelKeys();

        return [
            'uuid' => $this->faker->uuid(),
            'order_id' => $this->faker->randomElement($oIds),
            'gateway_type' => $this->faker->creditCardType,
            'amount' => $this->faker->numberBetween(1000, 100000),
            'reference_code' => $this->faker->creditCardNumber,
            'authority' => $this->faker->swiftBicNumber,
            'status' => $this->faker->randomElement(TransactionStatusEnum::asArray()),
            'payed_at' => $this->faker->dateTime,
        ];
    }
}
