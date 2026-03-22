<?php

namespace App\Jobs;

use App\Models\Package;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DownloadRepoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Package $package,
        public string $repoUrl
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Define a storage path for the package repo
        $storagePath = storage_path('app/packages/'.$this->package->slug);

        // Ensure the directory exists
        if (! is_dir($storagePath)) {
            mkdir($storagePath, 0775, true);
        }

        // Clone the repository
        $cloneCommand = sprintf(
            'git clone --depth=1 %s %s 2>&1',
            escapeshellarg($this->repoUrl),
            escapeshellarg($storagePath)
        );
        $output = [];
        $returnVar = 0;
        exec($cloneCommand, $output, $returnVar);

        if ($returnVar !== 0) {
            // Optionally: log error, mark package as failed, etc.
            Log::error('Failed to clone repo', [
                'package_id' => $this->package->id,
                'repo_url' => $this->repoUrl,
                'output' => $output,
            ]);

            return;
        }

        // Try to read composer.json for metadata
        $composerJsonPath = $storagePath.'/composer.json';
        if (file_exists($composerJsonPath)) {
            $composerData = json_decode(file_get_contents($composerJsonPath), true);
            if (is_array($composerData)) {
                $this->package->description = $composerData['description'] ?? $this->package->description;
                $this->package->path = 'packages/'.$this->package->slug;
                $this->package->save();
            }
        } else {
            // Still update the path if clone succeeded
            $this->package->path = 'packages/'.$this->package->slug;
            $this->package->save();
        }
    }
}
