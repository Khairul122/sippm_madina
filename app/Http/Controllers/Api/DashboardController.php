<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Http\Controllers\Concerns\ApiResponds;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardStatResource;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Illuminate\Http\JsonResponse;

/**
 * Simple aggregate queries for Fase 6. Fase 9 (non-functional) will add
 * response caching on top without changing this controller's shape.
 */
class DashboardController extends Controller
{
    use ApiResponds;

    public function statistics(): JsonResponse
    {
        $byStatus = Complaint::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byCategory = Complaint::query()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $activitiesByStatus = Activity::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return $this->success(new DashboardStatResource([
            'complaints' => [
                'total' => Complaint::query()->count(),
                'by_status' => $this->fillEnumCounts($byStatus, ComplaintStatus::cases()),
                'by_category' => $byCategory,
            ],
            'activities' => [
                'total' => Activity::query()->count(),
                'by_status' => $this->fillEnumCounts($activitiesByStatus, ActivityStatus::cases()),
            ],
        ]));
    }

    /**
     * Performance/monitoring view — Kominfo + Bupati/Wakil Bupati/Sekda
     * only (enforced at route middleware level). Simple counts per
     * disposition target for now; SLA/time-to-resolve metrics belong to
     * Fase 9.
     */
    public function performance(): JsonResponse
    {
        $byTargetType = Complaint::query()
            ->selectRaw('target_type, count(*) as total')
            ->groupBy('target_type')
            ->pluck('total', 'target_type');

        $resolvedCount = Complaint::query()->where('status', ComplaintStatus::SELESAI->value)->count();
        $totalCount = Complaint::query()->count();

        return $this->success(new DashboardStatResource([
            'by_target_type' => $byTargetType,
            'resolution_rate' => $totalCount > 0 ? round($resolvedCount / $totalCount * 100, 2) : 0.0,
        ]));
    }

    /**
     * @param array<int|string, int> $counts
     * @param array<int, \BackedEnum> $cases
     * @return array<string, int>
     */
    private function fillEnumCounts($counts, array $cases): array
    {
        $result = [];
        foreach ($cases as $case) {
            $result[$case->value] = (int) ($counts[$case->value] ?? 0);
        }

        return $result;
    }
}
