<?php

namespace Database\Factories;

use App\Enums\ManagerEnum;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'path' => $this->faker->filePath(),
            'manager' => $this->faker->randomElement(ManagerEnum::cases()),
        ];
    }
}
