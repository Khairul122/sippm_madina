<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManualBook\UploadManualBookRequest;
use App\Infrastructure\Persistence\Eloquent\Models\ManualBook;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Manual book (buku panduan) sistem — bisa dilihat/diunduh oleh SEMUA
 * role yang login (termasuk masyarakat), tapi cuma Kominfo yang boleh
 * mengunggah/menggantinya (dijaga middleware role:kominfo di
 * routes/web.php pada rute upload). Satu file aktif, pola sama persis
 * dengan TtdSignature — selalu diakses/diupdate lewat
 * updateOrCreate(['id' => 1], ...).
 */
class ManualBookController extends Controller
{
    public function show(Request $request): View
    {
        return view('manual-book.show', [
            'title' => 'Manual Book',
            'manualBook' => ManualBook::query()->with('uploader')->find(1),
        ]);
    }

    public function download(): StreamedResponse
    {
        $manualBook = ManualBook::query()->findOrFail(1);

        return Storage::disk('public')->download($manualBook->file_path, $manualBook->original_name);
    }

    /**
     * Sama seperti download(), tapi "Content-Disposition: inline" (bukan
     * "attachment") — supaya browser merender PDF langsung di dalam
     * <iframe> pada halaman, bukan memaksa dialog unduh. Dipakai sebagai
     * `src` iframe preview di manual-book/show.blade.php.
     */
    public function preview(): Response
    {
        $manualBook = ManualBook::query()->findOrFail(1);

        return Storage::disk('public')->response($manualBook->file_path, $manualBook->original_name);
    }

    public function upload(UploadManualBookRequest $request): RedirectResponse
    {
        $existing = ManualBook::query()->find(1);
        $file = $request->file('file');

        $path = $file->store('manual-books', 'public');

        if ($existing?->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        ManualBook::query()->updateOrCreate(['id' => 1], [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Manual book berhasil diunggah.');
    }
}
