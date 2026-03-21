<?php

namespace App\Models;

use App\Enums\ManagerEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'path',
        'manager',
    ];

    protected $casts = [
        'manager' => ManagerEnum::class,
    ];

    /**
     * @return HasMany<Version, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Version::class)->orderBy('major', 'desc')->orderBy('minor', 'desc')->orderBy('patch', 'desc');
    }

    public function getLatestVersion(): ?Version
    {
        return $this->versions()->orderBy('major', 'desc')
            ->orderBy('minor', 'desc')
            ->orderBy('patch', 'desc')
            ->first();
    }
}
