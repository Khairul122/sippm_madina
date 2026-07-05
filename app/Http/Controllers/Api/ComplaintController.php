<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\DTOs\DisposeComplaintDTO;
use App\Application\DTOs\HandleComplaintDTO;
use App\Application\DTOs\ResolveComplaintDTO;
use App\Application\DTOs\SubmitComplaintDTO;
use App\Application\DTOs\VerifyComplaintDTO;
use App\Application\UseCases\Complaint\DisposeComplaintUseCase;
use App\Application\UseCases\Complaint\HandleComplaintUseCase;
use App\Application\UseCases\Complaint\ResolveComplaintUseCase;
use App\Application\UseCases\Complaint\SubmitComplaintUseCase;
use App\Application\UseCases\Complaint\VerifyComplaintUseCase;
use App\Http\Controllers\Concerns\ApiResponds;
use App\Http\Controllers\Controller;
use App\Http\Requests\Complaint\DisposeComplaintRequest;
use App\Http\Requests\Complaint\HandleComplaintRequest;
use App\Http\Requests\Complaint\ResolveComplaintRequest;
use App\Http\Requests\Complaint\SubmitComplaintRequest;
use App\Http\Requests\Complaint\VerifyComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint as ComplaintModel;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Every write endpoint here is: validate (Form Request) -> build DTO ->
 * call UseCase -> return Resource. No business logic lives in this class
 * (AGENTS.md Do's/Don'ts). Read endpoints (index/show/history) query the
 * Eloquent model directly with eager loading since the Domain repository
 * paginate()/findById() intentionally return plain entities without
 * relations attached.
 */
class ComplaintController extends Controller
{
    use ApiResponds;

    public function __construct(
        private readonly SubmitComplaintUseCase $submitComplaint,
        private readonly VerifyComplaintUseCase $verifyComplaint,
        private readonly DisposeComplaintUseCase $disposeComplaint,
        private readonly HandleComplaintUseCase $handleComplaint,
        private readonly ResolveComplaintUseCase $resolveComplaint,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = ComplaintModel::query()->with(['user']);

        if ($user->hasRole('masyarakat')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        $complaints = $query->orderByDesc('created_at')->paginate((int) $request->integer('per_page', 15));

        return $this->success(ComplaintResource::collection($complaints));
    }

    public function store(SubmitComplaintRequest $request): JsonResponse
    {
        $data = $request->validated();
        $attachmentPaths = [];

        foreach ($request->file('attachments', []) as $file) {
            $attachmentPaths[] = $file->store('complaint-attachments', 'public');
        }

        $complaint = $this->submitComplaint->execute(new SubmitComplaintDTO(
            userId: $request->user()->id,
            title: $data['title'],
            description: $data['description'],
            category: $data['category'],
            targetType: $data['target_type'],
            targetId: isset($data['target_id']) ? (int) $data['target_id'] : null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            attachmentPaths: $attachmentPaths,
        ));

        $model = ComplaintModel::query()->with(['user', 'attachments'])->findOrFail($complaint->id);

        return $this->success(new ComplaintResource($model), 'Pengaduan berhasil diajukan.', 201);
    }

    public function show(int $complaint): JsonResponse
    {
        $model = ComplaintModel::query()
            ->with(['user', 'attachments', 'statusHistories', 'dispositions', 'handlings', 'response'])
            ->findOrFail($complaint);

        return $this->success(new ComplaintResource($model));
    }

    public function history(int $complaint): JsonResponse
    {
        $model = ComplaintModel::query()->with('statusHistories')->findOrFail($complaint);

        return $this->success((new ComplaintResource($model))->toArray(request())['status_histories'] ?? []);
    }

    public function verify(VerifyComplaintRequest $request, int $complaint): JsonResponse
    {
        try {
            $data = $request->validated();

            $updated = $this->verifyComplaint->execute(new VerifyComplaintDTO(
                complaintId: $complaint,
                verifiedByUserId: $request->user()->id,
                isValid: (bool) $data['is_valid'],
                note: $data['note'] ?? null,
                rejectionReason: $data['rejection_reason'] ?? null,
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $updated->status->value], 'Status verifikasi diperbarui.');
    }

    public function dispose(DisposeComplaintRequest $request, int $complaint): JsonResponse
    {
        try {
            $data = $request->validated();

            $updated = $this->disposeComplaint->execute(new DisposeComplaintDTO(
                complaintId: $complaint,
                disposedByUserId: $request->user()->id,
                targets: $data['targets'],
                note: $data['note'] ?? null,
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $updated->status->value], 'Pengaduan berhasil didisposisikan.');
    }

    public function handle(HandleComplaintRequest $request, int $complaint): JsonResponse
    {
        $data = $request->validated();
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaint-handlings', 'public');
        }

        $actingRoleSlug = $request->user()->hasRole('opd') ? 'opd' : 'camat';

        try {
            $updated = $this->handleComplaint->execute(new HandleComplaintDTO(
                complaintId: $complaint,
                dispositionId: (int) $data['disposition_id'],
                handledByUserId: $request->user()->id,
                description: $data['description'],
                attachmentPath: $attachmentPath,
            ), $actingRoleSlug);
        } catch (DomainException|InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $updated->status->value], 'Penanganan pengaduan berhasil dicatat.');
    }

    public function respond(ResolveComplaintRequest $request, int $complaint): JsonResponse
    {
        $data = $request->validated();

        try {
            $updated = $this->resolveComplaint->execute(new ResolveComplaintDTO(
                complaintId: $complaint,
                respondedByUserId: $request->user()->id,
                responseText: $data['response_text'],
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(['status' => $updated->status->value], 'Jawaban resmi berhasil dikirim.');
    }
}
