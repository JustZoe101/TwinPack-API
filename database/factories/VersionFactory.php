<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Version;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Version>
 */
class VersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
    */
    public function definition(): array
    {
        return [
            'package_id' => Package::factory(),
            'major' => $this->faker->numberBetween(0, 5),
            'minor' => $this->faker->numberBetween(0, 10),
            'patch' => $this->faker->numberBetween(0, 20),
            'suffix' => $this->faker->optional()->lexify('beta-?'),
        ];
    }
}
