<?php

namespace App\Http\Requests\Npm;

use Spatie\LaravelData\Data;

class PublishRequest extends Data
{
    public function __construct(
        public string $name,
        public string $version,
        public string $description,
        /** @var array<string, string> */
        public array $dist,
    ) {}
}
