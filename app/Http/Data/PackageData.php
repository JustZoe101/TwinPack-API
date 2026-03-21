<?php

namespace App\Http\Data;

use App\Enums\ManagerEnum;
use Spatie\LaravelData\Data;

class PackageData extends Data
{
    public function __construct(
        public string $slug,
        public string $name,
        public string $description,
        public string $path,
        public ManagerEnum $manager,

        /** @var VersionData[] */
        public array $versions,
    ) {}
}
