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
                ->create();
        }
    }
}
