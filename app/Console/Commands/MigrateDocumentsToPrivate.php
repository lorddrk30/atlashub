<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateDocumentsToPrivate extends Command
{
    protected $signature = 'documents:migrate-to-private';

    protected $description = 'Copy legacy document PDFs to private storage, verify them, then remove public copies';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $failed = 0;
        $moved = 0;

        foreach (Document::query()->distinct()->orderBy('file_path')->pluck('file_path') as $path) {
            if (! $public->exists($path)) {
                if (! $private->exists($path)) {
                    $this->error("Missing on both disks: {$path}");
                    $failed++;
                }

                continue;
            }

            $source = $public->readStream($path);

            if ($source === false) {
                $this->error("Cannot read public file: {$path}");
                $failed++;

                continue;
            }

            try {
                if (! $private->exists($path) && ! $private->writeStream($path, $source)) {
                    $this->error("Cannot write private file: {$path}");
                    $failed++;

                    continue;
                }
            } finally {
                fclose($source);
            }

            if (! $private->exists($path) || $private->size($path) !== $public->size($path)
                || $private->checksum($path) !== $public->checksum($path)) {
                $this->error("Private copy failed verification; public file retained: {$path}");
                $failed++;

                continue;
            }

            if (! $public->delete($path)) {
                $this->error("Verified private copy but could not remove public file: {$path}");
                $failed++;

                continue;
            }

            $moved++;
        }

        $this->info("Moved {$moved} document files; {$failed} need attention.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
