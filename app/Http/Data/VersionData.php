<?php

namespace App\Http\Data;

use App\Models\Version;
use Spatie\LaravelData\Data;

class VersionData extends Data
{
    public function __construct(
        public string $version,
        public int $major,
        public int $minor,
        public int $patch,
        public ?string $suffix,
    ) {}

    public static function fromModel(Version $model): self
    {
        return new self(
            version: $model->major.'.'.$model->minor.'.'.$model->patch.($model->suffix ? '-'.$model->suffix : ''),
            major: $model->major,
            minor: $model->minor,
            patch: $model->patch,
            suffix: $model->suffix,
        );
    }
}
