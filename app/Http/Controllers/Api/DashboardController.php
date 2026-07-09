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
use Illuminate\Support\Facades\Cache;

/**
 * NFR-16: hasil di-cache 60 detik (lihat Web\Dashboard\StatisticsController
 * untuk rasionalnya — TTL pendek dipilih ketimbang invalidasi manual
 * karena `CACHE_STORE=database` tidak mendukung cache tags).
 */
class DashboardController extends Controller
{
    use ApiResponds;

    private const CACHE_TTL_SECONDS = 60;

    public function statistics(): JsonResponse
    {
        $payload = Cache::remember('api.dashboard.statistics', self::CACHE_TTL_SECONDS, function () {
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

            return [
                'complaints' => [
                    'total' => Complaint::query()->count(),
                    'by_status' => $this->fillEnumCounts($byStatus, ComplaintStatus::cases()),
                    'by_category' => $byCategory->toArray(),
                ],
                'activities' => [
                    'total' => Activity::query()->count(),
                    'by_status' => $this->fillEnumCounts($activitiesByStatus, ActivityStatus::cases()),
                ],
            ];
        });

        return $this->success(new DashboardStatResource($payload));
    }

    /**
     * Performance/monitoring view — Kominfo + Bupati/Wakil Bupati/Sekda
     * only (enforced at route middleware level). Simple counts per
     * disposition target for now; SLA/time-to-resolve metrics belong to
     * Fase 9.
     */
    public function performance(): JsonResponse
    {
        $payload = Cache::remember('api.dashboard.performance', self::CACHE_TTL_SECONDS, function () {
            $byTargetType = Complaint::query()
                ->selectRaw('target_type, count(*) as total')
                ->groupBy('target_type')
                ->pluck('total', 'target_type');

            $resolvedCount = Complaint::query()->where('status', ComplaintStatus::SELESAI->value)->count();
            $totalCount = Complaint::query()->count();

            return [
                'by_target_type' => $byTargetType->toArray(),
                'resolution_rate' => $totalCount > 0 ? round($resolvedCount / $totalCount * 100, 2) : 0.0,
            ];
        });

        return $this->success(new DashboardStatResource($payload));
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
