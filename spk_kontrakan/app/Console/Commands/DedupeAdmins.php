<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DedupeAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:dedupe {--keep=newest : Keep the newest record (created_at) per email, or use oldest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate admin records by email, keeping newest or oldest per email';

    public function handle()
    {
        $keep = $this->option('keep') === 'oldest' ? 'oldest' : 'newest';

        $this->info("Starting dedupe (keeping: {$keep})...");

        $duplicates = DB::table('admins')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate admins found.');
            return 0;
        }

        foreach ($duplicates as $email) {
            $rows = DB::table('admins')->where('email', $email)->orderBy('created_at', $keep === 'newest' ? 'desc' : 'asc')->get();
            $keepId = $rows->first()->id;
            $deleteIds = $rows->pluck('id')->filter(function($id) use($keepId){ return $id !== $keepId; })->all();

            if (! empty($deleteIds)) {
                DB::table('admins')->whereIn('id', $deleteIds)->delete();
                $this->line("Deduped {$email}: kept id={$keepId}, deleted ids=".implode(',', $deleteIds));
            }
        }

        $this->info('Dedupe complete.');
        return 0;
    }
}
