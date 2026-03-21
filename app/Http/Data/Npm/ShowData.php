<?php

namespace App\Http\Data\Npm;

use App\Models\Package;
use Spatie\LaravelData\Data;

class ShowData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        /** @var array<string, string> */
        public array $distTags,
        /** @var array<ShowVersionData> */
        public array $versions,
        /** @var array<string, string|null> */
        public array $time,
    ) {}

    public static function fromModel(Package $package): self
    {
        $distTags = [];
        $latestVersion = $package->getLatestVersion();
        if ($latestVersion) {
            $distTags['latest'] = $latestVersion->versionString();
        }

        $versions = [];
        $time = [
            'created' => $package->created_at?->toIso8601String(),
            'modified' => $package->modifiedAt()?->toIso8601String(),
        ];

        foreach ($package->versions as $version) {
            $versionString = $version->versionString();
            $versionUpdated = $version->updated_at ? $version->updated_at->toIso8601String() : now()->toIso8601String();
            $time[$versionString] = $versionUpdated;

            $versions[$versionString] = ShowVersionData::from($version);
        }

        return new self(
            name: $package->slug,
            description: $package->description,
            distTags: $distTags,
            versions: $versions,
            time: $time
        );
    }
}
