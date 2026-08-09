<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    private User $kominfo;
    private User $opd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->kominfo = User::query()->where('email', 'kominfo@gmail.com')->firstOrFail();
        $this->opd = User::query()->where('email', 'opd@gmail.com')->firstOrFail();
    }

    public function test_unauthenticated_user_cannot_access_backup_dashboard(): void
    {
        $this->get('/dashboard/backup')->assertRedirect('/login');
    }

    public function test_non_kominfo_role_cannot_access_backup_dashboard(): void
    {
        $this->actingAs($this->opd)->get('/dashboard/backup')->assertForbidden();
    }

    public function test_kominfo_user_can_view_backup_dashboard(): void
    {
        $this->actingAs($this->kominfo)
            ->get('/dashboard/backup')
            ->assertOk()
            ->assertSee('Backup Database Sistem')
            ->assertSee('Jadwal Otomatis:');
    }

    public function test_kominfo_user_can_trigger_manual_backup(): void
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--only-db' => true])
            ->andReturn(0);

        Artisan::shouldReceive('output')
            ->andReturn('Backup completed successfully!');

        $response = $this->actingAs($this->kominfo)->post('/dashboard/backup/run');

        $response->assertRedirect('/dashboard/backup');
        $response->assertSessionHas('success', 'Backup database berhasil dibuat!');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->kominfo->id,
            'action' => 'create_backup',
        ]);
    }

    public function test_kominfo_user_can_download_existing_backup_file(): void
    {
        $backupDir = storage_path('app/private/SIPAPA Madina');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $testFile = $backupDir . '/test-backup-sample.zip';
        File::put($testFile, 'dummy zip content');

        try {
            $response = $this->actingAs($this->kominfo)->get('/dashboard/backup/download/test-backup-sample.zip');

            $response->assertOk();

            $this->assertDatabaseHas('audit_logs', [
                'user_id' => $this->kominfo->id,
                'action' => 'download_backup',
            ]);
        } finally {
            if (File::exists($testFile)) {
                File::delete($testFile);
            }
        }
    }

    public function test_kominfo_user_can_delete_existing_backup_file(): void
    {
        $backupDir = storage_path('app/private/SIPAPA Madina');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $testFile = $backupDir . '/test-delete-sample.zip';
        File::put($testFile, 'dummy content to delete');

        $this->assertTrue(File::exists($testFile));

        $response = $this->actingAs($this->kominfo)->delete('/dashboard/backup/test-delete-sample.zip');

        $response->assertRedirect('/dashboard/backup');
        $response->assertSessionHas('success');

        $this->assertFalse(File::exists($testFile));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->kominfo->id,
            'action' => 'delete_backup',
        ]);
    }

    public function test_download_rejects_invalid_file_extension(): void
    {
        $response = $this->actingAs($this->kominfo)->get('/dashboard/backup/download/malicious.php');

        $response->assertRedirect('/dashboard/backup');
        $response->assertSessionHas('error', 'Format berkas backup tidak valid.');
    }
}
