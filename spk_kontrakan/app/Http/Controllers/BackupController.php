<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Exception;

class BackupController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('backups');
        
        // Create backups directory jika belum ada
        if (!File::isDirectory($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function index(Request $request)
    {
        $backups = [];
        
        if (File::isDirectory($this->backupPath)) {
            $files = File::files($this->backupPath);
            
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'date' => $file->getMTime(),
                    'path' => $file->getRealPath(),
                ];
            }
        }

        // Sort by date descending
        usort($backups, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('admin.backup.index', compact('backups'));
    }

    public function create(Request $request)
    {
        try {
            $timestamp = now()->format('Y-m-d-H-i-s');
            $backupFile = $this->backupPath . "/backup_{$timestamp}.sql";

            // Get database credentials
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');

            // Create SQL dump (Windows MySQL)
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > "%s"',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                $backupFile
            );

            $output = null;
            $exitCode = null;
            exec($command, $output, $exitCode);

            if ($exitCode === 0 && File::exists($backupFile)) {
                // Create zip file
                $zipFile = $this->backupPath . "/backup_{$timestamp}.zip";
                $zip = new ZipArchive();
                
                if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
                    $zip->addFile($backupFile, 'database.sql');
                    $zip->close();

                    // Delete original SQL file
                    File::delete($backupFile);

                    return redirect()->back()->with('success', "Backup berhasil dibuat: backup_{$timestamp}.zip");
                }
            }

            return redirect()->back()->with('error', 'Gagal membuat backup');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function download($backup)
    {
        $backupPath = $this->backupPath . '/' . $backup;

        if (!File::exists($backupPath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan');
        }

        return response()->download($backupPath);
    }

    public function delete($backup)
    {
        try {
            $backupPath = $this->backupPath . '/' . $backup;

            if (!File::exists($backupPath)) {
                return redirect()->back()->with('error', 'File backup tidak ditemukan');
            }

            File::delete($backupPath);

            return redirect()->back()->with('success', 'Backup berhasil dihapus');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, $backup)
    {
        try {
            $backupPath = $this->backupPath . '/' . $backup;

            if (!File::exists($backupPath)) {
                return redirect()->back()->with('error', 'File backup tidak ditemukan');
            }

            // Extract zip jika zip file
            if (pathinfo($backupPath, PATHINFO_EXTENSION) === 'zip') {
                $extractPath = $this->backupPath . '/temp_' . time();
                $zip = new ZipArchive();
                
                if ($zip->open($backupPath) === true) {
                    $zip->extractTo($extractPath);
                    $zip->close();
                    
                    $sqlFile = $extractPath . '/database.sql';
                    if (!File::exists($sqlFile)) {
                        File::deleteDirectory($extractPath);
                        return redirect()->back()->with('error', 'File SQL tidak ditemukan dalam backup');
                    }
                } else {
                    return redirect()->back()->with('error', 'Gagal membuka file zip');
                }
            } else {
                $sqlFile = $backupPath;
            }

            // Restore database
            $database = env('DB_DATABASE');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST');

            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < "%s"',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                $sqlFile
            );

            $output = null;
            $exitCode = null;
            exec($command, $output, $exitCode);

            // Cleanup temp files
            if (isset($extractPath) && File::isDirectory($extractPath)) {
                File::deleteDirectory($extractPath);
            }

            if ($exitCode === 0) {
                return redirect()->back()->with('success', 'Database berhasil di-restore');
            } else {
                return redirect()->back()->with('error', 'Gagal restore database');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
