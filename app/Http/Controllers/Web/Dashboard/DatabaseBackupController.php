<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use App\Infrastructure\Persistence\Eloquent\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupController extends Controller
{
    /**
     * Tampilkan daftar berkas backup database & status penjadwalan.
     */
    public function index(): View
    {
        $setting = SiteSetting::query()->firstOrCreate(
            ['id' => 1],
            ['backup_frequency' => 'weekly', 'backup_time' => '01:00']
        );

        $backupDirs = [
            storage_path('app/private/SIPAPA Madina'),
            storage_path('app/private/SIPPM Madina'),
            storage_path('app/private'),
        ];

        $files = [];

        foreach ($backupDirs as $dir) {
            if (!File::isDirectory($dir)) {
                continue;
            }

            $foundFiles = File::files($dir);
            foreach ($foundFiles as $file) {
                if (strtolower($file->getExtension()) !== 'zip') {
                    continue;
                }

                $filename = $file->getFilename();
                if (isset($files[$filename])) {
                    continue;
                }

                $files[$filename] = [
                    'name' => $filename,
                    'path' => $file->getRealPath(),
                    'size' => $this->formatBytes($file->getSize()),
                    'size_bytes' => $file->getSize(),
                    'date' => date('d M Y, H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }

        // Sort by timestamp descending (newest first)
        usort($files, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        $totalSize = array_sum(array_column($files, 'size_bytes'));
        $totalFiles = count($files);
        $latestBackup = $totalFiles > 0 ? $files[0]['date'] : null;

        $nextBackupFormatted = $this->calculateNextBackup(
            $setting->backup_frequency ?? 'weekly',
            $setting->backup_time ?? '01:00'
        );

        return view('dashboard.backup.index', [
            'title' => 'Backup Database',
            'setting' => $setting,
            'backups' => $files,
            'totalSize' => $this->formatBytes($totalSize),
            'totalFiles' => $totalFiles,
            'latestBackup' => $latestBackup,
            'nextBackup' => $nextBackupFormatted,
        ]);
    }

    /**
     * Jalankan proses backup database secara manual via Artisan & opsional langsung unduh.
     */
    public function run(Request $request): BinaryFileResponse|RedirectResponse
    {
        try {
            $latestBefore = $this->getLatestBackupFile();

            try {
                Artisan::call('backup:run', ['--only-db' => true]);
            } catch (\Throwable $ex) {
                // If spatie backup fails due to mysqldump binary missing on hosting, proceed to pure PHP fallback
            }

            $latestAfter = $this->getLatestBackupFile();

            // If Spatie backup did not create a new zip file, generate pure PHP database backup
            if (!$latestAfter || ($latestBefore && $latestBefore['path'] === $latestAfter['path'])) {
                $backupPath = $this->generatePurePhpDatabaseBackup();
                $latestAfter = [
                    'name' => basename($backupPath),
                    'path' => $backupPath,
                ];
            }

            SiteSetting::query()->updateOrCreate(
                ['id' => 1],
                ['last_manual_backup_at' => now(), 'updated_by' => auth()->id()]
            );

            AuditLog::record(
                action: 'Backup Database Manual',
                modelType: 'backup',
                newData: ['description' => 'Membuat backup database manual via dashboard']
            );

            if ($request->boolean('download') && $latestAfter && File::exists($latestAfter['path'])) {
                return response()->download($latestAfter['path'], $latestAfter['name']);
            }

            return redirect('/dashboard/backup')->with('success', 'Backup database berhasil dibuat!');
        } catch (\Throwable $e) {
            return redirect('/dashboard/backup')->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Perbarui jadwal otomatis & frekuensi backup database.
     */
    public function updateSchedule(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_frequency' => ['required', 'string', 'in:daily,every_12_hours,every_3_days,weekly,monthly'],
            'backup_time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
        ]);

        SiteSetting::query()->updateOrCreate(
            ['id' => 1],
            [
                'backup_frequency' => $request->input('backup_frequency'),
                'backup_time' => $request->input('backup_time'),
                'updated_by' => auth()->id(),
            ]
        );

        AuditLog::record(
            action: 'Perbarui Jadwal Backup',
            modelType: 'backup_schedule',
            newData: [
                'backup_frequency' => $request->input('backup_frequency'),
                'backup_time' => $request->input('backup_time'),
            ]
        );

        return redirect('/dashboard/backup')->with('success', 'Pengaturan jadwal backup database berhasil diperbarui!');
    }

    /**
     * Unduh file backup database tertentu.
     */
    public function download(Request $request, string $filename): BinaryFileResponse|RedirectResponse
    {
        $safeFilename = basename($filename);

        if (!str_ends_with(strtolower($safeFilename), '.zip')) {
            return redirect('/dashboard/backup')->with('error', 'Format berkas backup tidak valid.');
        }

        $filePath = $this->findBackupFile($safeFilename);

        if (!$filePath || !File::exists($filePath)) {
            return redirect('/dashboard/backup')->with('error', 'File backup tidak ditemukan.');
        }

        AuditLog::record(
            action: 'Unduh Berkas Backup',
            modelType: 'backup',
            newData: ['filename' => $safeFilename]
        );

        return response()->download($filePath, $safeFilename);
    }

    /**
     * Hapus file backup database tertentu.
     */
    public function destroy(Request $request, string $filename): RedirectResponse
    {
        $safeFilename = basename($filename);

        if (!str_ends_with(strtolower($safeFilename), '.zip')) {
            return redirect('/dashboard/backup')->with('error', 'Format berkas backup tidak valid.');
        }

        $filePath = $this->findBackupFile($safeFilename);

        if (!$filePath || !File::exists($filePath)) {
            return redirect('/dashboard/backup')->with('error', 'File backup tidak ditemukan.');
        }

        File::delete($filePath);

        AuditLog::record(
            action: 'Hapus Berkas Backup',
            modelType: 'backup',
            oldData: ['filename' => $safeFilename]
        );

        return redirect('/dashboard/backup')->with('success', "File backup {$safeFilename} berhasil dihapus.");
    }

    /**
     * Hitung perkiraan jadwal backup selanjutnya dalam Bahasa Indonesia.
     */
    private function calculateNextBackup(string $frequency, string $timeStr): string
    {
        $parts = explode(':', $timeStr);
        $hour = (int) ($parts[0] ?? 1);
        $minute = (int) ($parts[1] ?? 0);
        $now = Carbon::now();

        $next = match ($frequency) {
            'daily' => $now->copy()->setTime($hour, $minute)->isPast()
                ? $now->copy()->addDay()->setTime($hour, $minute)
                : $now->copy()->setTime($hour, $minute),

            'every_12_hours' => $now->copy()->addHours(12),

            'every_3_days' => $now->copy()->setTime($hour, $minute)->isPast()
                ? $now->copy()->addDays(3)->setTime($hour, $minute)
                : $now->copy()->setTime($hour, $minute),

            'monthly' => $now->copy()->day(1)->setTime($hour, $minute)->isPast()
                ? $now->copy()->addMonth()->day(1)->setTime($hour, $minute)
                : $now->copy()->day(1)->setTime($hour, $minute),

            default => $now->copy()->next(0)->setTime($hour, $minute), // weekly
        };

        return $next->translatedFormat('l, d F Y \p\u\k\u\l H:i \W\I\B');
    }

    /**
     * Dapatkan file backup terbaru.
     */
    private function getLatestBackupFile(): ?array
    {
        $backupDirs = [
            storage_path('app/private/SIPAPA Madina'),
            storage_path('app/private/SIPPM Madina'),
            storage_path('app/private'),
        ];

        $files = [];
        foreach ($backupDirs as $dir) {
            if (!File::isDirectory($dir)) {
                continue;
            }

            foreach (File::files($dir) as $file) {
                if (strtolower($file->getExtension()) === 'zip') {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getRealPath(),
                        'timestamp' => $file->getMTime(),
                    ];
                }
            }
        }

        if (empty($files)) {
            return null;
        }

        usort($files, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $files[0];
    }

    /**
     * Cari jalur file backup yang cocok dari beberapa folder backup yang diizinkan.
     */
    private function findBackupFile(string $filename): ?string
    {
        $backupDirs = [
            storage_path('app/private/SIPAPA Madina'),
            storage_path('app/private/SIPPM Madina'),
            storage_path('app/private'),
        ];

        foreach ($backupDirs as $dir) {
            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Format byte ke bentuk ukuran terbaca (KB, MB, GB).
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = (int) floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Backup database menggunakan murni PHP (PDO SQL Dumper) apabila mysqldump
     * tidak tersedia di server hosting (CWP/Shared Hosting).
     */
    private function generatePurePhpDatabaseBackup(): string
    {
        $backupDir = storage_path('app/private/SIPAPA Madina');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true, true);
        }

        $databaseName = (string) config('database.connections.mysql.database', 'sipapa_madina');
        $tables = DB::select('SHOW TABLES');
        $keyName = "Tables_in_{$databaseName}";

        $sqlContent = "-- SIPAPA Madina Database Backup Dump\n";
        $sqlContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sqlContent .= "-- Database: {$databaseName}\n\n";
        $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $tableArr = (array) $tableObj;
            $tableName = $tableArr[$keyName] ?? array_values($tableArr)[0] ?? null;
            if (!$tableName) {
                continue;
            }

            // Get Create Table statement
            $createTableStmt = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createTableStmt)) {
                $createSql = ((array) $createTableStmt[0])['Create Table'] ?? null;
                if ($createSql) {
                    $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $sqlContent .= $createSql . ";\n\n";
                }
            }

            // Get Table Data
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $cols = array_map(fn($col) => "`{$col}`", array_keys($rowArray));
                    $vals = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        if (is_bool($val)) return $val ? '1' : '0';
                        return DB::getPdo()->quote((string) $val);
                    }, array_values($rowArray));

                    $sqlContent .= "INSERT INTO `{$tableName}` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
                }
                $sqlContent .= "\n";
            }
        }

        $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $zipFilename = 'backup-' . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = $backupDir . '/' . $zipFilename;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFromString('db-dump-' . date('Y-m-d-H-i-s') . '.sql', $sqlContent);
            $zip->close();
        }

        return $zipPath;
    }
}
