<?php

use App\Enums\ManagerEnum;
use App\Http\Controllers\API\ComposerRegistryController;
use App\Http\Controllers\API\NpmRegistryController;
use App\Http\Controllers\API\PackageController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [PackageController::class, 'index']);

// NPM Registry Endpoints
Route::prefix(ManagerEnum::NPM->value)->group(function () {
    // Publish a new package/version (npm publish)
    Route::put('/{packageSlug}', [NpmRegistryController::class, 'publish']);

    // Fetch package metadata (npm install, npm view)
    Route::get('/{packageSlug}', [NpmRegistryController::class, 'show']);
    Route::get('/{packageSlug}/{version}', [NpmRegistryController::class, 'showVersion']);

    // Download tarball
    Route::get('/{packageSlug}/-/{tarball}', [NpmRegistryController::class, 'downloadTarball']);
});

// Composer (Packagist) Endpoints
Route::prefix(ManagerEnum::COMPOSER->value)->group(function () {
    // Submit a new package (Packagist submit)
    Route::post('/packages', [ComposerRegistryController::class, 'submit']);

    // Fetch all packages metadata
    Route::get('/packages.json', [ComposerRegistryController::class, 'allPackages']);

    // Fetch specific package metadata
    Route::get('/p/{vendor}/{package}.json', [ComposerRegistryController::class, 'show']);
});
