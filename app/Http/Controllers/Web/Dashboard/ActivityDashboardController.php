<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Application\DTOs\SubmitActivityDTO;
use App\Application\DTOs\VerifyActivityDTO;
use App\Application\UseCases\Activity\PublishActivityUseCase;
use App\Application\UseCases\Activity\SubmitActivityUseCase;
use App\Application\UseCases\Activity\VerifyActivityUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Activity\SubmitActivityRequest;
use App\Http\Requests\Activity\VerifyActivityRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
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
    ) {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Activity::query()->with('documentations');

        if ($user->hasRole('opd') && $user->opd_id) {
            $query->where('actor_type', 'opd')->where('actor_id', $user->opd_id);
        } elseif ($user->hasRole('camat') && $user->kecamatan_id) {
            $query->where('actor_type', 'kecamatan')->where('actor_id', $user->kecamatan_id);
        }

        $activities = $query->orderByDesc('date')->paginate(15);

        return view('dashboard.activities.index', [
            'title' => 'Kegiatan',
            'activities' => $activities,
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
}
