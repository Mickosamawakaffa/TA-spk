<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     */
    protected $description = 'Create a database backup ZIP file in storage/backups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $backupPath = storage_path('backups');

            if (!File::isDirectory($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $timestamp = now()->format('Y-m-d-H-i-s');
            $backupFile = $backupPath . "/backup_{$timestamp}.sql";
            $zipFile = $backupPath . "/backup_{$timestamp}.zip";

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $dumpCommand = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > "%s"',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                $backupFile
            );

            $output = null;
            $exitCode = null;
            exec($dumpCommand, $output, $exitCode);

            if ($exitCode !== 0 || !File::exists($backupFile)) {
                $this->error('Gagal membuat dump database.');
                return self::FAILURE;
            }

            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                File::delete($backupFile);
                $this->error('Gagal membuat file ZIP backup.');
                return self::FAILURE;
            }

            $zip->addFile($backupFile, 'database.sql');
            $zip->close();

            File::delete($backupFile);

            $this->info("Backup berhasil dibuat: backup_{$timestamp}.zip");

            \Log::info('Database backup command executed', [
                'file' => "backup_{$timestamp}.zip",
                'executed_at' => now(),
            ]);

            return self::SUCCESS;
        } catch (Exception $e) {
            $this->error('Error backup database: ' . $e->getMessage());

            \Log::error('Database backup command failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}