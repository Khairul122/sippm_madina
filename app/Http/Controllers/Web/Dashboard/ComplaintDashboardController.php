<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Application\DTOs\DisposeComplaintDTO;
use App\Application\DTOs\HandleComplaintDTO;
use App\Application\DTOs\ResolveComplaintDTO;
use App\Application\DTOs\VerifyComplaintDTO;
use App\Application\UseCases\Complaint\DisposeComplaintUseCase;
use App\Application\UseCases\Complaint\HandleComplaintUseCase;
use App\Application\UseCases\Complaint\ResolveComplaintUseCase;
use App\Application\UseCases\Complaint\VerifyComplaintUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Complaint\DisposeComplaintRequest;
use App\Http\Requests\Complaint\HandleComplaintRequest;
use App\Http\Requests\Complaint\ResolveComplaintRequest;
use App\Http\Requests\Complaint\VerifyComplaintRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Kominfo/OPD/Camat complaint workflow screens. Every write action here is
 * validate (reuses the same Form Request as the API) -> authorize (Policy)
 * -> call UseCase -> redirect back, mirroring Api\ComplaintController but
 * rendering Blade instead of JSON (AGENTS.md: Controller tetap tipis).
 */
class ComplaintDashboardController extends Controller
{
    public function __construct(
        private readonly VerifyComplaintUseCase $verifyComplaint,
        private readonly DisposeComplaintUseCase $disposeComplaint,
        private readonly HandleComplaintUseCase $handleComplaint,
        private readonly ResolveComplaintUseCase $resolveComplaint,
    ) {
    }

    /**
     * FR-19: filter pengaduan berdasarkan status, kategori, tanggal, dan
     * OPD/Kecamatan tujuan — plus pencarian teks bebas (tiket/judul/desc).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Complaint::query()->with('user');

        if ($user->hasRole('opd') && $user->opd_id) {
            $query->whereHas('dispositions', fn ($q) => $q->where('disposed_to_type', 'opd')->where('disposed_to_id', $user->opd_id));
        } elseif ($user->hasRole('camat') && $user->kecamatan_id) {
            $query->whereHas('dispositions', fn ($q) => $q->where('disposed_to_type', 'camat')->where('disposed_to_id', $user->kecamatan_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        // Combined "tujuan" filter from a single <select>, encoded as
        // "opd:3" / "camat:5" to keep the filter bar to one dropdown
        // instead of a type+id pair.
        if ($request->filled('target') && str_contains((string) $request->string('target'), ':')) {
            [$targetType, $targetId] = explode(':', (string) $request->string('target'), 2);
            $query->where('target_type', $targetType)->where('target_id', (int) $targetId);
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $complaints = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('dashboard.complaints.index', [
            'title' => 'Pengaduan',
            'complaints' => $complaints,
            'categories' => Complaint::query()->distinct()->orderBy('category')->pluck('category'),
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, int $complaint): View
    {
        $model = Complaint::query()
            ->with(['user', 'attachments', 'statusHistories.changedBy', 'dispositions.disposedBy', 'handlings', 'response'])
            ->findOrFail($complaint);

        $this->authorize('view', $model);

        return view('dashboard.complaints.show', [
            'title' => 'Detail Pengaduan #'.$model->ticket_number,
            'complaint' => $model,
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
            'myPendingDisposition' => $this->pendingDispositionFor($request->user(), $model),
        ]);
    }

    public function verify(VerifyComplaintRequest $request, int $complaint): RedirectResponse
    {
        $model = Complaint::query()->findOrFail($complaint);
        $this->authorize('verify', $model);

        $data = $request->validated();

        try {
            $this->verifyComplaint->execute(new VerifyComplaintDTO(
                complaintId: $complaint,
                verifiedByUserId: $request->user()->id,
                isValid: (bool) $data['is_valid'],
                note: $data['note'] ?? null,
                rejectionReason: $data['rejection_reason'] ?? null,
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Status verifikasi diperbarui.');
    }

    public function dispose(DisposeComplaintRequest $request, int $complaint): RedirectResponse
    {
        $model = Complaint::query()->findOrFail($complaint);
        $this->authorize('dispose', $model);

        $data = $request->validated();

        try {
            $this->disposeComplaint->execute(new DisposeComplaintDTO(
                complaintId: $complaint,
                disposedByUserId: $request->user()->id,
                targets: $data['targets'],
                note: $data['note'] ?? null,
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Pengaduan berhasil didisposisikan.');
    }

    public function handle(HandleComplaintRequest $request, int $complaint): RedirectResponse
    {
        $model = Complaint::query()->findOrFail($complaint);
        $this->authorize('handle', $model);

        $data = $request->validated();
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('complaint-handlings', 'public');
        }

        $actingRoleSlug = $request->user()->hasRole('opd') ? 'opd' : 'camat';

        try {
            $this->handleComplaint->execute(new HandleComplaintDTO(
                complaintId: $complaint,
                dispositionId: (int) $data['disposition_id'],
                handledByUserId: $request->user()->id,
                description: $data['description'],
                attachmentPath: $attachmentPath,
            ), $actingRoleSlug);
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Penanganan pengaduan berhasil dicatat.');
    }

    public function respond(ResolveComplaintRequest $request, int $complaint): RedirectResponse
    {
        $model = Complaint::query()->findOrFail($complaint);
        $this->authorize('respond', $model);

        $data = $request->validated();

        try {
            $this->resolveComplaint->execute(new ResolveComplaintDTO(
                complaintId: $complaint,
                respondedByUserId: $request->user()->id,
                responseText: $data['response_text'],
            ));
        } catch (DomainException|InvalidArgumentException $e) {
            return back()->withErrors($e->getMessage());
        }

        return back()->with('status', 'Jawaban resmi berhasil dikirim.');
    }

    private function pendingDispositionFor(mixed $user, Complaint $complaint): ?int
    {
        if ($user->hasRole('opd') && $user->opd_id) {
            return $complaint->dispositions->firstWhere(fn ($d) => $d->disposed_to_type === 'opd' && $d->disposed_to_id === $user->opd_id)?->id;
        }

        if ($user->hasRole('camat') && $user->kecamatan_id) {
            return $complaint->dispositions->firstWhere(fn ($d) => $d->disposed_to_type === 'camat' && $d->disposed_to_id === $user->kecamatan_id)?->id;
        }

        return null;
    }
}
