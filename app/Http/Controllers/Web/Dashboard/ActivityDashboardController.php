<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Application\DTOs\SubmitActivityDTO;
use App\Application\DTOs\VerifyActivityDTO;
use App\Application\UseCases\Activity\PublishActivityUseCase;
use App\Application\UseCases\Activity\SubmitActivityUseCase;
use App\Application\UseCases\Activity\UnpublishActivityUseCase;
use App\Application\UseCases\Activity\VerifyActivityUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Activity\SubmitActivityRequest;
use App\Http\Requests\Activity\VerifyActivityRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ActivityDashboardController extends Controller
{
    public function __construct(
        private readonly SubmitActivityUseCase $submitActivity,
        private readonly VerifyActivityUseCase $verifyActivity,
        private readonly PublishActivityUseCase $publishActivity,
        private readonly UnpublishActivityUseCase $unpublishActivity,
    ) {
    }

    /**
     * FR-23/FR-24: riwayat kegiatan per OPD/Kecamatan dengan filter status
     * dan periode waktu. OPD/Camat tetap auto-scoped ke unit sendiri
     * (tidak bisa memilih target lain); filter "tujuan" hanya berlaku
     * untuk role yang melihat semua kegiatan (Kominfo/Bupati/Wabup/Sekda).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Activity::query()->with('documentations');

        $canFilterByTarget = $user->hasAnyRole(['kominfo', 'bupati', 'wakil_bupati', 'sekda']);

        if ($user->hasRole('opd') && $user->opd_id) {
            $query->where('actor_type', 'opd')->where('actor_id', $user->opd_id);
        } elseif ($user->hasRole('camat') && $user->kecamatan_id) {
            $query->where('actor_type', 'kecamatan')->where('actor_id', $user->kecamatan_id);
        } elseif ($canFilterByTarget && $request->filled('target') && str_contains((string) $request->string('target'), ':')) {
            // Encoded as "opd:3" / "kecamatan:5" — activities.actor_type
            // uses 'kecamatan' (not 'camat' like complaints.target_type).
            [$actorType, $actorId] = explode(':', (string) $request->string('target'), 2);
            $query->where('actor_type', $actorType)->where('actor_id', (int) $actorId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date('date_to'));
        }

        $activities = $query->orderByDesc('date')->paginate(15)->withQueryString();

        return view('dashboard.activities.index', [
            'title' => 'Kegiatan',
            'activities' => $activities,
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
            'canFilterByTarget' => $canFilterByTarget,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Activity::class);

        return view('dashboard.activities.create', [
            'title' => 'Input Kegiatan',
        ]);
    }

    public function store(SubmitActivityRequest $request): RedirectResponse
    {
        $this->authorize('create', Activity::class);

        $data = $request->validated();
        $user = $request->user();

        $actorType = $user->hasRole('opd') ? 'opd' : 'kecamatan';
        $actorId = $actorType === 'opd' ? $user->opd_id : $user->kecamatan_id;

        $documentationPaths = [];
        foreach ($request->file('documentations', []) as $file) {
            $documentationPaths[] = $file->store('activity-documentations', 'public');
        }

        $this->submitActivity->execute(new SubmitActivityDTO(
            title: $data['title'],
            description: $data['description'],
            actorType: $actorType,
            actorId: (int) $actorId,
            date: $data['date'],
            location: $data['location'] ?? null,
            documentationPaths: $documentationPaths,
        ));

        return redirect('/dashboard/activities')->with('status', 'Kegiatan berhasil disimpan sebagai draft, menunggu verifikasi Kominfo.');
    }

    public function verify(VerifyActivityRequest $request, int $activity): RedirectResponse
    {
        $model = Activity::query()->findOrFail($activity);
        $this->authorize('verify', $model);

        $data = $request->validated();

        try {
            $this->verifyActivity->execute(new VerifyActivityDTO(
                activityId: $activity,
                verifiedByUserId: $request->user()->id,
                isValid: (bool) $data['is_valid'],
                rejectionReason: $data['rejection_reason'] ?? null,
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Status verifikasi kegiatan diperbarui.');
    }

    public function publish(Request $request, int $activity): RedirectResponse
    {
        $model = Activity::query()->findOrFail($activity);
        $this->authorize('publish', $model);

        try {
            $this->publishActivity->execute($activity);
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Kegiatan berhasil dipublikasikan.');
    }

    public function unpublish(Request $request, int $activity): RedirectResponse
    {
        $model = Activity::query()->findOrFail($activity);
        $this->authorize('unpublish', $model);

        try {
            $this->unpublishActivity->execute($activity);
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Kegiatan ditarik kembali ke draft.');
    }

    public function show(int $id): View
    {
        $activity = Activity::query()->with(['documentations'])->findOrFail($id);
        $this->authorize('view', $activity);

        return view('dashboard.activities.show', [
            'title' => 'Detail Kegiatan',
            'activity' => $activity,
        ]);
    }

    public function edit(int $id): View
    {
        $activity = Activity::query()->with(['documentations'])->findOrFail($id);
        $this->authorize('update', $activity);

        return view('dashboard.activities.edit', [
            'title' => 'Ubah Kegiatan',
            'activity' => $activity,
        ]);
    }

    public function update(SubmitActivityRequest $request, int $id): RedirectResponse
    {
        $activity = Activity::query()->with(['documentations'])->findOrFail($id);
        $this->authorize('update', $activity);

        $data = $request->validated();

        // 1. Handle deletion of documentations if checked
        if ($request->filled('delete_documentations')) {
            $deleteIds = $request->input('delete_documentations');
            foreach ($activity->documentations as $doc) {
                if (in_array((string) $doc->id, array_map('strval', $deleteIds))) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
                    $doc->delete();
                }
            }
        }

        // Reload documentation count
        $activity->load('documentations');
        $currentPhotosCount = $activity->documentations->count();
        $newFiles = $request->file('documentations', []);
        $newFilesCount = count($newFiles);

        if (($currentPhotosCount + $newFilesCount) > 5) {
            return back()->withInput()->withErrors('Total foto dokumentasi tidak boleh melebihi 5 foto (saat ini sudah ada ' . $currentPhotosCount . ' foto, dan Anda mencoba mengunggah ' . $newFilesCount . ' foto baru).');
        }

        // 2. Update basic fields
        $activity->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'date' => $data['date'],
            'location' => $data['location'] ?? null,
        ]);

        // 3. Store new uploaded files
        foreach ($newFiles as $file) {
            $filePath = $file->store('activity-documentations', 'public');
            $activity->documentations()->create([
                'file_path' => $filePath,
            ]);
        }

        return redirect('/dashboard/activities/' . $activity->id)->with('status', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $activity = Activity::query()->findOrFail($id);
        $this->authorize('delete', $activity);

        $activity->delete();

        return redirect('/dashboard/activities')->with('status', 'Kegiatan berhasil dihapus.');
    }
}
