<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DatabaseBackupController extends Controller
{
    /**
     * Tampilkan daftar berkas backup database & status penjadwalan.
     */
    public function index(): View
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

        return view('dashboard.backup.index', [
            'title' => 'Backup Database',
            'backups' => $files,
            'totalSize' => $this->formatBytes($totalSize),
            'totalFiles' => $totalFiles,
            'latestBackup' => $latestBackup,
        ]);
    }

    /**
     * Jalankan proses backup database secara manual via Artisan.
     */
    public function run(Request $request): RedirectResponse
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);

            AuditLog::query()->create([
                'user_id' => auth()->id(),
                'action' => 'create_backup',
                'model_type' => 'backup',
                'model_id' => 0,
                'new_data' => ['description' => 'Membuat backup database manual via dashboard'],
                'ip_address' => $request->ip(),
            ]);

            return redirect('/dashboard/backup')->with('success', 'Backup database berhasil dibuat!');
        } catch (\Throwable $e) {
            return redirect('/dashboard/backup')->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
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

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => 'download_backup',
            'model_type' => 'backup',
            'model_id' => 0,
            'new_data' => ['filename' => $safeFilename],
            'ip_address' => $request->ip(),
        ]);

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

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => 'delete_backup',
            'model_type' => 'backup',
            'model_id' => 0,
            'old_data' => ['filename' => $safeFilename],
            'ip_address' => $request->ip(),
        ]);

        return redirect('/dashboard/backup')->with('success', "File backup {$safeFilename} berhasil dihapus.");
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
}
