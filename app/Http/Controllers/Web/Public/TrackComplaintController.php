<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Web wrapper around the same public tracking data as
 * Api\PublicComplaintController — view rendering only, no business logic.
 */
class TrackComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $complaint = null;
        $ticketNumber = $request->query('ticket_number');

        if ($ticketNumber) {
            $complaint = Complaint::query()
                ->with('statusHistories')
                ->where('ticket_number', $ticketNumber)
                ->first();
        }

        return view('public.track', [
            'title' => 'Lacak Pengaduan',
            'complaint' => $complaint,
            'ticketNumber' => $ticketNumber,
        ]);
    }
}
