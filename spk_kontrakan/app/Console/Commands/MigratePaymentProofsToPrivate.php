<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigratePaymentProofsToPrivate extends Command
{
    protected $signature = 'bookings:migrate-payment-proofs {--dry-run : Only report what would change} {--limit= : Max number of bookings to process}';

    protected $description = 'Move legacy payment proof files from public disk to private disk';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('private');

        $dryRun = (bool) $this->option('dry-run');
        $limit = $this->option('limit');
        $limit = is_numeric($limit) ? (int) $limit : null;

        $query = Booking::query()->whereNotNull('payment_proof')->orderBy('id');
        if ($limit && $limit > 0) {
            $query->limit($limit);
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('No bookings with payment_proof found.');
            return self::SUCCESS;
        }

        $migrated = 0;
        $skipped = 0;
        $missing = 0;
        $failed = 0;

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Processing {$total} booking(s)...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(200, function ($bookings) use ($public, $private, $dryRun, &$migrated, &$skipped, &$missing, &$failed, $bar) {
            foreach ($bookings as $booking) {
                $path = $booking->payment_proof;
                if (!$path) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                try {
                    if ($private->exists($path)) {
                        // Already private
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    if (!$public->exists($path)) {
                        $missing++;
                        $bar->advance();
                        continue;
                    }

                    if ($dryRun) {
                        $migrated++;
                        $bar->advance();
                        continue;
                    }

                    $readStream = $public->readStream($path);
                    if ($readStream === false) {
                        $failed++;
                        $bar->advance();
                        continue;
                    }

                    $private->writeStream($path, $readStream);
                    if (is_resource($readStream)) {
                        fclose($readStream);
                    }

                    $public->delete($path);
                    $migrated++;
                } catch (\Throwable $e) {
                    $failed++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->line('Done.');
        $this->line('Migrated : ' . $migrated);
        $this->line('Skipped  : ' . $skipped);
        $this->line('Missing  : ' . $missing);
        $this->line('Failed   : ' . $failed);

        if ($dryRun) {
            $this->comment('Dry-run mode: no files were changed.');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
