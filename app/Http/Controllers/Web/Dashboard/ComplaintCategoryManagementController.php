<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\ComplaintCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Kominfo-only CRUD management for Complaint Categories.
 */
class ComplaintCategoryManagementController extends Controller
{
    public function index(): View
    {
        $categories = ComplaintCategory::query()->orderBy('name')->paginate(15);

        return view('dashboard.categories.index', [
            'title' => 'Kelola Kategori Pengaduan',
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.categories.create', [
            'title' => 'Tambah Kategori',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:complaint_categories,name'],
        ], [
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        ComplaintCategory::query()->create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return redirect('/dashboard/categories')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $category = ComplaintCategory::query()->findOrFail($id);

        return view('dashboard.categories.edit', [
            'title' => 'Ubah Kategori',
            'category' => $category,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = ComplaintCategory::query()->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('complaint_categories', 'name')->ignore($id)],
        ], [
            'name.unique' => 'Nama kategori sudah terdaftar.',
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        // Keep old name to update complaints category strings if we want,
        // but let's update complaints with the old category name to the new one!
        // This keeps historical complaints in sync since they store category as a string.
        $oldName = $category->name;
        $newName = $data['name'];

        $category->update([
            'name' => $newName,
            'slug' => Str::slug($newName),
        ]);

        if ($oldName !== $newName) {
            Complaint::query()->where('category', $oldName)->update(['category' => $newName]);
        }

        return redirect('/dashboard/categories')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = ComplaintCategory::query()->findOrFail($id);

        // Guard: prevent deletion if the category name is in use in complaints table
        $inUse = Complaint::query()->where('category', $category->name)->exists();
        if ($inUse) {
            return back()->withErrors('Kategori tidak dapat dihapus karena sedang digunakan oleh beberapa pengaduan.');
        }

        $category->delete();

        return redirect('/dashboard/categories')->with('status', 'Kategori berhasil dihapus.');
    }
}
