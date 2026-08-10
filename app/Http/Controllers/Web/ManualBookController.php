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
        $manualBook = ManualBook::query()->with('uploader')->find(1);

        return view('manual-book.show', [
            'title' => 'Manual Book',
            'manualBook' => $manualBook,
            // File PDF bisa saja hilang secara fisik dari disk walau baris
            // DB-nya masih ada (mis. server pindah/redeploy, atau proses
            // upload sebelumnya gagal sebagian) — dicek eksplisit di sini
            // supaya view bisa menampilkan pesan yang jelas alih-alih
            // memuat <iframe>/tombol unduh yang pasti error 500
            // (League\Flysystem\UnableToRetrieveMetadata) saat diklik.
            'manualBookFileMissing' => $manualBook && ! Storage::disk('public')->exists($manualBook->file_path),
        ]);
    }

    public function download(): StreamedResponse|RedirectResponse
    {
        $manualBook = ManualBook::query()->findOrFail(1);

        if (! Storage::disk('public')->exists($manualBook->file_path)) {
            return back()->with('error', 'File manual book tidak ditemukan di server. Silakan hubungi Kominfo untuk mengunggah ulang.');
        }

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

        if (! Storage::disk('public')->exists($manualBook->file_path)) {
            // Dipanggil sebagai `src` <iframe>, bukan navigasi biasa —
            // redirect/flash tidak akan terlihat pengguna di dalam iframe,
            // jadi balas langsung dengan halaman HTML kecil berisi pesan
            // error yang jelas (lebih baik daripada iframe kosong/500 raw
            // Flysystem UnableToRetrieveMetadata).
            return response(
                '<div style="font-family:sans-serif;padding:2rem;text-align:center;color:#B23A3A;">'
                .'File manual book tidak ditemukan di server.<br>Silakan hubungi Kominfo untuk mengunggah ulang.'
                .'</div>',
                404
            );
        }

        return Storage::disk('public')->response($manualBook->file_path, $manualBook->original_name);
    }

    public function upload(UploadManualBookRequest $request): RedirectResponse
    {
        $existing = ManualBook::query()->find(1);
        $isReplacing = (bool) $existing;
        $file = $request->file('file');

        // Simpan file baru DULU sebelum menghapus file lama — supaya kalau
        // penyimpanan file baru gagal di tengah jalan (disk penuh, dst),
        // file lama yang masih valid tidak ikut hilang.
        $path = $file->store('manual-books', 'public');

        if ($existing?->file_path) {
            Storage::disk('public')->delete($existing->file_path);
        }

        ManualBook::singleton([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        return back()->with('status', $isReplacing
            ? 'Manual book berhasil diganti. File lama sudah dihapus dari server.'
            : 'Manual book berhasil diunggah.');
    }
}
