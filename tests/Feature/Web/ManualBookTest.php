<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Infrastructure\Persistence\Eloquent\Models\ManualBook;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManualBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_role_can_view_manual_book_page(): void
    {
        $this->seed();

        foreach (['masyarakat', 'kominfo', 'opd', 'camat', 'bupati', 'wakil_bupati', 'sekda'] as $role) {
            $user = User::query()->where('email', "{$role}@demo.test")->firstOrFail();
            $this->actingAs($user)->get('/manual-book')->assertOk();
        }
    }

    public function test_shows_empty_state_when_not_uploaded_yet(): void
    {
        $this->seed();

        $user = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $this->actingAs($user)->get('/manual-book')
            ->assertOk()
            ->assertSee('belum diunggah');
    }

    public function test_kominfo_can_upload_manual_book(): void
    {
        Storage::fake('public');
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/manual-book', [
            'file' => UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf'),
        ])->assertRedirect();

        $manualBook = ManualBook::query()->findOrFail(1);
        $this->assertSame('panduan.pdf', $manualBook->original_name);
        $this->assertSame($kominfo->id, $manualBook->uploaded_by);
        Storage::disk('public')->assertExists($manualBook->file_path);
    }

    public function test_non_kominfo_role_cannot_upload_manual_book(): void
    {
        $this->seed();

        $opdUser = User::query()->where('email', 'opd@demo.test')->firstOrFail();

        $this->actingAs($opdUser)->post('/manual-book', [
            'file' => UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf'),
        ])->assertForbidden();

        $this->assertDatabaseCount('manual_books', 0);
    }

    public function test_uploading_again_replaces_old_file(): void
    {
        Storage::fake('public');
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/manual-book', [
            'file' => UploadedFile::fake()->create('lama.pdf', 500, 'application/pdf'),
        ]);
        $oldPath = ManualBook::query()->findOrFail(1)->file_path;

        $this->actingAs($kominfo)->post('/manual-book', [
            'file' => UploadedFile::fake()->create('baru.pdf', 500, 'application/pdf'),
        ]);
        $manualBook = ManualBook::query()->findOrFail(1);

        $this->assertDatabaseCount('manual_books', 1);
        $this->assertSame('baru.pdf', $manualBook->original_name);
        $this->assertNotSame($oldPath, $manualBook->file_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($manualBook->file_path);
    }

    public function test_upload_rejects_non_pdf_file(): void
    {
        Storage::fake('public');
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();

        $this->actingAs($kominfo)->post('/manual-book', [
            'file' => UploadedFile::fake()->image('foto.jpg'),
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseCount('manual_books', 0);
    }

    public function test_any_role_can_download_uploaded_manual_book(): void
    {
        Storage::fake('public');
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $this->actingAs($kominfo)->post('/manual-book', [
            'file' => UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf'),
        ]);

        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->firstOrFail();
        $this->actingAs($masyarakat)->get('/manual-book/download')->assertOk();
    }

    public function test_preview_streams_pdf_inline_not_as_attachment(): void
    {
        Storage::fake('public');
        $this->seed();

        $kominfo = User::query()->where('email', 'kominfo@demo.test')->firstOrFail();
        $this->actingAs($kominfo)->post('/manual-book', [
            'file' => UploadedFile::fake()->create('panduan.pdf', 500, 'application/pdf'),
        ]);

        $response = $this->actingAs($kominfo)->get('/manual-book/preview');

        $response->assertOk();
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }
}
