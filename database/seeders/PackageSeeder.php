<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Version;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $package = Package::factory()->create();

            Version::factory(10)
                ->for($package)
                ->afterCreating(function (Version $version) {
                    // Simulate different update times for versions
                    $version->updated_at = now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));
                    $version->save();
                })
                ->create();
        }
    }
}
