<?php

namespace App\Http\Data\Npm;

use App\Models\Version;
use Spatie\LaravelData\Data;

class ShowVersionData extends Data
{
    public function __construct(
        public string $name,
        public string $version,
        public ?string $description,
        /** @var array<string, string> */
        public array $dist,
    ) {}

    public static function fromModel(Version $version): self
    {
        if (! $version->package) {
            throw new \InvalidArgumentException('Version must be associated with a package.');
        }

        $version = [
            'name' => $version->package->slug,
            'version' => $version->versionString(),
            'description' => $version->package->description,
            'dist' => [
                'tarball' => url("/npm/{$version->package->slug}/-/{$version->versionString()}.tgz"),
                'shasum' => $version->shasum,
            ],
            // 'dependencies' => $version->dependencies ?? [], // Add if available
        ];

        return new self(
            $version['name'],
            $version['version'],
            $version['description'],
            $version['dist']
        );
    }
}
