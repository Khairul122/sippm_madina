<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Exports\ComplaintStatisticsExport;
use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class StatisticsController extends Controller
{
    public function index(): View
    {
        return view('dashboard.statistics.index', [
            'title' => 'Statistik',
            'complaintsByStatus' => $this->fillCounts(
                Complaint::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
                array_map(fn (ComplaintStatus $s) => $s->value, ComplaintStatus::cases()),
            ),
            'complaintsByCategory' => Complaint::query()->selectRaw('category, count(*) as total')->groupBy('category')->pluck('total', 'category'),
            'activitiesByStatus' => $this->fillCounts(
                Activity::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
                array_map(fn (ActivityStatus $s) => $s->value, ActivityStatus::cases()),
            ),
        ]);
    }

    public function performance(): View
    {
        $byTarget = Complaint::query()->selectRaw('target_type, count(*) as total')->groupBy('target_type')->pluck('total', 'target_type');
        $total = Complaint::query()->count();
        $resolved = Complaint::query()->where('status', ComplaintStatus::SELESAI->value)->count();

        return view('dashboard.statistics.performance', [
            'title' => 'Kinerja OPD / Kecamatan',
            'targetLabels' => array_map(fn (string $key) => ucfirst(str_replace('_', ' ', $key)), $byTarget->keys()->all()),
            'targetTotals' => $byTarget->values()->all(),
            'resolutionRate' => $total > 0 ? round($resolved / $total * 100, 2) : 0.0,
            'totalComplaints' => $total,
            'resolvedComplaints' => $resolved,
        ]);
    }

    public function exportPdf(): Response
    {
        $rows = Complaint::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $pdf = Pdf::loadView('dashboard.statistics.export-pdf', [
            'rows' => $rows,
            'generatedAt' => now(),
        ]);

        return $pdf->download('statistik-pengaduan-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ComplaintStatisticsExport(), 'statistik-pengaduan-'.now()->format('Ymd-His').'.xlsx');
    }

    /**
     * @param array<int|string, int> $counts
     * @param array<int, string> $keys
     * @return array<string, int>
     */
    private function fillCounts($counts, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = (int) ($counts[$key] ?? 0);
        }

        return $result;
    }
}
