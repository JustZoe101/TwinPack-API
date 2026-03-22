<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Data\Composer\ShowData;
use App\Jobs\DownloadRepoJob;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ComposerRegistryController extends Controller
{
    // Submit a new package (Packagist submit)
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'repository.url' => 'required|url',
        ]);

        $repoUrl = $request->input('repository.url');

        // Try to extract vendor/package from the URL (supports GitHub, GitLab, Bitbucket)
        $matches = [];
        if (preg_match('#(?:github|gitlab|bitbucket)\\.com[:/](.+?)/(.+?)(?:\\.git)?$#', $repoUrl, $matches)) {
            $vendor = $matches[1];
            $packageName = $matches[2];
            $slug = $vendor.'/'.$packageName;
        } else {
            return response()->json(['error' => 'Could not extract vendor/package from repository URL'], 422);
        }

        $package = Package::firstOrCreate(
            [
                'slug' => $slug,
                'manager' => 'composer',
            ],
            [
                'name' => $packageName,
                'description' => '', // Optionally fetch from composer.json
                'path' => '', // Optionally set a storage path
            ]
        );

        DownloadRepoJob::dispatch($package, $repoUrl);

        return response()->json(['ok' => 'package submitted', 'slug' => $slug], 201);
    }

    // Fetch all packages metadata

    /**
     * @return Collection<int, ShowData>
     */
    public function allPackages(): Collection
    {
        // Return a list of all composer packages in Packagist format
        $packages = Package::where('manager', 'composer')->get();

        return ShowData::collect($packages);
    }

    // Fetch specific package metadata
    public function show(string $vendor, string $package): ShowData
    {
        // Find the package by vendor/package slug
        $slug = $vendor.'/'.$package;
        $package = Package::with('versions')
            ->where('slug', $slug)
            ->where('manager', 'composer')
            ->firstOrFail();

        return ShowData::from($package);
    }
}
