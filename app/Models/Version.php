<?php

namespace App\Models;

use Database\Factories\VersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Version extends Model
{
    /** @use HasFactory<VersionFactory> */
    use HasFactory;

    protected $fillable = [
        'package_id',
        'major',
        'minor',
        'patch',
        'suffix',
        'shasum',
    ];

    /**
     * @return BelongsTo<Package, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function versionString(): string
    {
        $version = "{$this->major}.{$this->minor}.{$this->patch}";
        if ($this->suffix) {
            $version .= "-{$this->suffix}";
        }

        return $version;
    }
}
