<?php

namespace Database\Factories;

use App\Models\DeviceModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeviceModel>
 */
class DeviceModelFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Phone '.$this->faker->unique()->numberBetween(1, 9999);

        return ['brand' => 'Acme', 'name' => $name, 'slug' => str('acme '.$name)->slug()->toString()];
    }
}
