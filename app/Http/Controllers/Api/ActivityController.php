<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\SubmitActivityDTO;
use App\Application\DTOs\VerifyActivityDTO;
use App\Application\UseCases\Activity\PublishActivityUseCase;
use App\Application\UseCases\Activity\SubmitActivityUseCase;
use App\Application\UseCases\Activity\VerifyActivityUseCase;
use App\Http\Controllers\Concerns\ApiResponds;
use App\Http\Controllers\Controller;
use App\Http\Requests\Activity\SubmitActivityRequest;
use App\Http\Requests\Activity\VerifyActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Infrastructure\Persistence\Eloquent\Models\Activity as ActivityModel;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ActivityController extends Controller
{
    use ApiResponds;

    public function __construct(
        private readonly SubmitActivityUseCase $submitActivity,
        private readonly VerifyActivityUseCase $verifyActivity,
        private readonly PublishActivityUseCase $publishActivity,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = ActivityModel::query()->with('documentations');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $activities = $query->orderByDesc('date')->paginate((int) $request->integer('per_page', 15));

        return $this->success(ActivityResource::collection($activities));
    }

    public function store(SubmitActivityRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $actorType = $user->hasRole('opd') ? 'opd' : 'kecamatan';
        $actorId = $actorType === 'opd' ? $user->opd_id : $user->kecamatan_id;

        $documentationPaths = [];
        foreach ($request->file('documentations', []) as $file) {
            $documentationPaths[] = $file->store('activity-documentations', 'public');
        }

        $activity = $this->submitActivity->execute(new SubmitActivityDTO(
            title: $data['title'],
            description: $data['description'],
            actorType: $actorType,
            actorId: (int) $actorId,
            date: $data['date'],
            location: $data['location'] ?? null,
            documentationPaths: $documentationPaths,
        ));

        $model = ActivityModel::query()->with('documentations')->findOrFail($activity->id);

        return $this->success(new ActivityResource($model), 'Kegiatan berhasil disimpan sebagai draft.', 201);
    }

    public function verify(VerifyActivityRequest $request, int $activity): JsonResponse
    {
        $data = $request->validated();

        try {
            $updated = $this->verifyActivity->execute(new VerifyActivityDTO(
                activityId: $activity,
                verifiedByUserId: $request->user()->id,
                isValid: (bool) $data['is_valid'],
                rejectionReason: $data['rejection_reason'] ?? null,
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $updated->status->value], 'Status verifikasi kegiatan diperbarui.');
    }

    public function publish(int $activity): JsonResponse
    {
        try {
            $updated = $this->publishActivity->execute($activity);
        } catch (DomainException|InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $updated->status->value], 'Kegiatan berhasil dipublikasikan.');
    }
}
