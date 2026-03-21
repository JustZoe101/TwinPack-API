<?php

namespace App\Http\Data\Composer;

use App\Models\Package;
use Spatie\LaravelData\Data;

class ShowData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        /** @var array<string, mixed> */
        public array $versions,
    ) {}

    public static function fromModel(Package $package): self
    {
        $versions = [];
        foreach ($package->versions as $version) {
            $versionString = $version->major.'.'.$version->minor.'.'.$version->patch;
            if (! empty($version->suffix)) {
                $versionString .= '-'.$version->suffix;
            }
            $versions[$versionString] = [
                'name' => $package->name,
                'version' => $versionString,
                'description' => $package->description,
                // Add more composer metadata as needed
            ];
        }

        return new self(
            name: $package->name,
            description: $package->description,
            versions: $versions,
        );
    }
}
