<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Public;

use App\Domain\Activity\ValueObjects\ActivityStatus;
use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'title' => 'SIPPM Madina',
            'totalComplaints' => Complaint::query()->count(),
            'resolvedComplaints' => Complaint::query()->where('status', ComplaintStatus::SELESAI->value)->count(),
            'publishedActivities' => Activity::query()->where('status', ActivityStatus::DIPUBLIKASIKAN->value)->count(),
            'recentActivities' => Activity::query()
                ->where('status', ActivityStatus::DIPUBLIKASIKAN->value)
                ->with(['documentations', 'actor'])
                ->latest('date')
                ->take(3)
                ->get(),
        ]);
    }
}
