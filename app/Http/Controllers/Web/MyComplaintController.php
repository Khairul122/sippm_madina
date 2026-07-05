<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Application\DTOs\SubmitComplaintDTO;
use App\Application\UseCases\Complaint\SubmitComplaintUseCase;
use App\Domain\Complaint\ValueObjects\TargetType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Complaint\SubmitComplaintRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Masyarakat-only: ajukan pengaduan baru dan pantau pengaduan milik sendiri.
 * Sengaja TIDAK berada di bawah prefix /dashboard — rute dashboard internal
 * hanya untuk Kominfo/OPD/Camat/pimpinan (AGENTS.md Don'ts).
 */
class MyComplaintController extends Controller
{
    public function __construct(
        private readonly SubmitComplaintUseCase $submitComplaint,
    ) {
    }

    public function index(Request $request): View
    {
        $complaints = Complaint::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('complaints.index', [
            'title' => 'Pengaduan Saya',
            'complaints' => $complaints,
        ]);
    }

    public function create(): View
    {
        return view('complaints.create', [
            'title' => 'Ajukan Pengaduan',
            'targetTypes' => TargetType::cases(),
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
        ]);
    }

    public function store(SubmitComplaintRequest $request): RedirectResponse
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

        return redirect('/pengaduan/'.$complaint->id)->with('status', 'Pengaduan berhasil diajukan dengan nomor tiket '.$complaint->ticketNumber.'.');
    }

    public function show(Request $request, int $complaint): View
    {
        $model = Complaint::query()
            ->with(['attachments', 'statusHistories', 'response'])
            ->findOrFail($complaint);

        $this->authorize('view', $model);

        return view('complaints.show', [
            'title' => 'Pengaduan #'.$model->ticket_number,
            'complaint' => $model,
        ]);
    }
}
