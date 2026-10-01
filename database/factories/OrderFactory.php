<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'status' => OrderStatus::Pending,
            'locale' => 'de',
            'subtotal' => 1990,
            'shipping' => 490,
            'total' => 2480,
            'stripe_session_id' => 'cs_test_'.$this->faker->unique()->bothify('????????'),
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
            'email' => 'kunde@example.ch',
            'phone' => '+41791234567',
            'stripe_payment_intent_id' => 'pi_test_123',
            'shipping_address' => [
                'name' => 'Anna Muster', 'line1' => 'Bahnhofstrasse 1', 'line2' => null,
                'postal_code' => '8001', 'city' => 'Zürich', 'state' => null, 'country' => 'CH',
            ],
        ]);
    }
}
