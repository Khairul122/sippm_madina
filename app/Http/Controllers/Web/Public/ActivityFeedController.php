<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Public;

use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Public feed of published activities. Queries the Eloquent model
 * directly (not ActivityRepositoryInterface::paginatePublished(), which
 * intentionally returns plain Domain entities without relations) so the
 * view can eager-load documentation thumbnails + the OPD/Kecamatan actor
 * name — same pattern as Web\Dashboard\ComplaintDashboardController.
 */
class ActivityFeedController extends Controller
{
    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->with(['documentations', 'actor'])
            ->where('status', ActivityStatus::DIPUBLIKASIKAN->value)
            ->orderByDesc('date')
            ->paginate(9, page: (int) $request->integer('page', 1));

        return view('public.activities', [
            'title' => 'Kegiatan Publik',
            'activities' => $activities,
            'total' => $activities->total(),
        ]);
    }
}
