<?php

namespace App\Http\Controllers\API;

use App\Enums\ManagerEnum;
use App\Http\Controllers\Controller;
use App\Http\Data\Npm\ShowData;
use App\Http\Data\Npm\ShowVersionData;
use App\Http\Requests\Npm\PublishRequest;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NpmRegistryController extends Controller
{
    // Publish a new package/version (npm publish)
    public function publish(PublishRequest $request, string $packageSlug): JsonResponse
    {
        $disk = Storage::disk('packages');
        $packagePath = $packageSlug;
        if (! $disk->exists($packagePath)) {
            $disk->makeDirectory($packagePath);
        }

        // Find or create the package by slug and manager
        $package = Package::firstOrCreate(
            [
                'slug' => $packageSlug,
                'manager' => ManagerEnum::NPM,
            ],
            [
                'name' => $request->name,
                'description' => $request->description,
                'path' => $packagePath,
            ]
        );

        // Parse version and suffix
        $versionParts = explode('-', $request->version, 2);
        $semver = explode('.', $versionParts[0]);
        $suffix = $versionParts[1] ?? null;

        $package->versions()->updateOrCreate(
            [
                'major' => $semver[0],
                'minor' => $semver[1] ?? 0,
                'patch' => $semver[2] ?? 0,
                'suffix' => $suffix,
            ],
            [
                'shasum' => $request->dist['shasum'] ?? sha1(base64_decode($request->dist['data'] ?? '')),
            ]
        );

        // Handle dist (tarball) upload and storage
        $tarballPath = $packagePath.'/'.$request->version.'.tgz';
        $disk->put($tarballPath, base64_decode($request->dist['data']));

        return response()->json(['ok' => 'package published'], 201);
    }

    // Fetch package metadata (npm install, npm view)
    public function show(string $packageSlug): ShowData
    {
        $package = Package::with('versions')
            ->where('slug', $packageSlug)
            ->where('manager', ManagerEnum::NPM)
            ->firstOrFail();

        return ShowData::from($package);
    }

    public function showVersion(string $packageSlug, string $version): ShowVersionData
    {
        $package = Package::with('versions')
            ->where('slug', $packageSlug)
            ->where('manager', ManagerEnum::NPM)
            ->firstOrFail();

        $targetVersion = $package->versions->first(function ($v) use ($version) {
            $versionString = $v->major.'.'.$v->minor.'.'.$v->patch;
            if (! empty($v->suffix)) {
                $versionString .= '-'.$v->suffix;
            }

            return $versionString === $version;
        });

        if (! $targetVersion) {
            abort(404, 'Version not found');
        }

        return ShowVersionData::from($targetVersion);
    }

    // Download tarball
    public function downloadTarball(string $packageSlug, string $tarball): StreamedResponse
    {
        $disk = Storage::disk('packages');
        $filePath = $packageSlug.'/'.$tarball;
        if (! $disk->exists($filePath)) {
            abort(404, 'Tarball not found');
        }
        $stream = $disk->readStream($filePath);

        return response()->stream(function () use ($stream) {
            if (is_resource($stream)) {
                fpassthru($stream);
            }
        }, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.basename($filePath).'"',
        ]);
    }
}
