<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Dashboard;

use App\Domain\Complaint\ValueObjects\ComplaintStatus;
use App\Exports\LaporanPengaduanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Laporan\UpdateTtdRequest;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\TtdSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Laporan pengaduan yang bisa dicetak (Kominfo-only), dengan sub-halaman
 * pengaturan profil TTD (tanda tangan) yang dipakai di blok tanda tangan
 * cetakan PDF. Satu controller, mengikuti pola StatisticsController.
 */
class LaporanController extends Controller
{
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'];

    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request): View
    {
        $baseQuery = $this->filteredComplaints($request);

        // Hitung statistik untuk data terfilter
        $stats = [
            'total' => $baseQuery->clone()->count(),
            'selesai' => $baseQuery->clone()->where('status', ComplaintStatus::SELESAI->value)->count(),
            'proses' => $baseQuery->clone()->whereIn('status', [
                ComplaintStatus::DIPROSES->value,
                ComplaintStatus::DITINDAKLANJUTI->value
            ])->count(),
            'pending' => $baseQuery->clone()->whereIn('status', [
                ComplaintStatus::DIAJUKAN->value,
                ComplaintStatus::DIVERIFIKASI->value
            ])->count(),
        ];

        $complaints = $baseQuery
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.laporan.index', [
            'title' => 'Laporan Pengaduan',
            'activeTab' => 'complaints',
            'complaints' => $complaints,
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
            'statuses' => ComplaintStatus::cases(),
            'hariOptions' => self::HARI,
            'bulanOptions' => self::BULAN,
            'tahunOptions' => \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite'
                ? Complaint::query()->selectRaw("DISTINCT strftime('%Y', created_at) as tahun")->orderByDesc('tahun')->pluck('tahun')
                : Complaint::query()->selectRaw('DISTINCT YEAR(created_at) as tahun')->orderByDesc('tahun')->pluck('tahun'),
            'ttd' => TtdSignature::query()->find(1),
            'stats' => $stats,
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $complaints = $this->filteredComplaints($request)->orderBy('created_at')->get();

        $pdf = Pdf::loadView('dashboard.laporan.export-pdf', [
            'complaints' => $complaints,
            'opdNames' => Opd::query()->pluck('name', 'id'),
            'kecamatanNames' => Kecamatan::query()->pluck('name', 'id'),
            'filters' => $this->filterSummary($request),
            'ttd' => TtdSignature::query()->find(1),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        // Preview di browser (buka tab baru), bukan langsung download —
        // "Content-Disposition: inline" via stream() supaya PDF viewer
        // browser yang menampilkan, unduh cuma kalau user pilih sendiri
        // dari situ (mis. tombol download di PDF viewer).
        return $pdf->stream('laporan-pengaduan-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $complaints = $this->filteredComplaints($request)->orderBy('created_at')->get();

        return Excel::download(
            new LaporanPengaduanExport($complaints, Opd::query()->pluck('name', 'id'), Kecamatan::query()->pluck('name', 'id')),
            'laporan-pengaduan-'.now()->format('Ymd-His').'.xlsx',
        );
    }

    public function updateTtd(UpdateTtdRequest $request): RedirectResponse
    {
        TtdSignature::query()->updateOrCreate(['id' => 1], $request->validated());

        return redirect('/dashboard/laporan')->with('status', 'Data TTD berhasil disimpan.');
    }

    private function filteredComplaints(Request $request): Builder
    {
        $query = Complaint::query()->with(['user.kecamatan.desas']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('target') && str_contains((string) $request->string('target'), ':')) {
            [$targetType, $targetId] = explode(':', (string) $request->string('target'), 2);
            $query->where('target_type', $targetType)->where('target_id', (int) $targetId);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('created_at', (int) $request->integer('tahun'));
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', (int) $request->integer('bulan'));
        }

        if ($request->filled('hari')) {
            // Filter "hari" = hari dalam seminggu (0=Senin..6=Minggu, sama
            // dengan urutan MySQL WEEKDAY()) — independen dari bulan/tahun,
            // BUKAN tanggal 1-31 dalam bulan.
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
                $sqliteVal = ((int) $request->integer('hari') + 1) % 7;
                $query->whereRaw("CAST(strftime('%w', created_at) AS INTEGER) = ?", [$sqliteVal]);
            } else {
                $query->whereRaw('WEEKDAY(created_at) = ?', [(int) $request->integer('hari')]);
            }
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * @return array<string, string>
     */
    private function filterSummary(Request $request): array
    {
        $summary = [];

        if ($request->filled('status')) {
            $summary['Status'] = ComplaintStatus::from((string) $request->string('status'))->label();
        }

        if ($request->filled('target') && str_contains((string) $request->string('target'), ':')) {
            [$targetType, $targetId] = explode(':', (string) $request->string('target'), 2);
            $summary['Tujuan'] = $targetType === 'opd'
                ? Opd::query()->find((int) $targetId)?->name ?? '-'
                : Kecamatan::query()->find((int) $targetId)?->name ?? '-';
        }

        if ($request->filled('hari')) {
            $summary['Hari'] = self::HARI[(int) $request->integer('hari')] ?? '-';
        }

        if ($request->filled('bulan')) {
            $summary['Bulan'] = self::BULAN[(int) $request->integer('bulan')] ?? '-';
        }

        if ($request->filled('tahun')) {
            $summary['Tahun'] = (string) $request->integer('tahun');
        }

        if ($request->filled('search')) {
            $summary['Pencarian'] = (string) $request->string('search');
        }

        return $summary;
    }

    public function activitiesIndex(Request $request): View
    {
        $baseQuery = $this->filteredActivities($request);

        // Hitung statistik kegiatan
        $stats = [
            'total' => $baseQuery->clone()->count(),
            'dipublikasikan' => $baseQuery->clone()->where('status', 'dipublikasikan')->count(),
            'draft' => $baseQuery->clone()->where('status', 'draft')->count(),
            'diverifikasi' => $baseQuery->clone()->where('status', 'diverifikasi')->count(),
        ];

        $activities = $baseQuery
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.laporan.index', [
            'title' => 'Laporan Kegiatan',
            'activeTab' => 'activities',
            'activities' => $activities,
            'opds' => Opd::query()->orderBy('name')->get(),
            'kecamatans' => Kecamatan::query()->orderBy('name')->get(),
            'ttd' => TtdSignature::query()->find(1),
            'stats' => $stats,
        ]);
    }

    public function exportActivitiesPdf(Request $request): Response
    {
        $activities = $this->filteredActivities($request)->orderBy('date')->get();

        $pdf = Pdf::loadView('dashboard.laporan.export-activities-pdf', [
            'activities' => $activities,
            'filters' => $this->filterActivitiesSummary($request),
            'ttd' => TtdSignature::query()->find(1),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-kegiatan-'.now()->format('Ymd-His').'.pdf');
    }

    public function exportActivitiesExcel(Request $request): BinaryFileResponse
    {
        $activities = $this->filteredActivities($request)->orderBy('date')->get();

        return Excel::download(
            new \App\Exports\LaporanKegiatanExport($activities),
            'laporan-kegiatan-'.now()->format('Ymd-His').'.xlsx',
        );
    }

    private function filteredActivities(Request $request): Builder
    {
        $query = Activity::query()->with('documentations');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('target') && str_contains((string) $request->string('target'), ':')) {
            [$actorType, $actorId] = explode(':', (string) $request->string('target'), 2);
            $query->where('actor_type', $actorType)->where('actor_id', (int) $actorId);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date('date_to'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * @return array<string, string>
     */
    private function filterActivitiesSummary(Request $request): array
    {
        $summary = [];

        if ($request->filled('status')) {
            $summary['Status'] = ucfirst((string) $request->string('status'));
        }

        if ($request->filled('target') && str_contains((string) $request->string('target'), ':')) {
            [$actorType, $actorId] = explode(':', (string) $request->string('target'), 2);
            $summary['Penerbit'] = $actorType === 'opd'
                ? Opd::query()->find((int) $actorId)?->name ?? '-'
                : Kecamatan::query()->find((int) $actorId)?->name ?? '-';
        }

        if ($request->filled('date_from')) {
            $summary['Dari Tanggal'] = $request->string('date_from');
        }

        if ($request->filled('date_to')) {
            $summary['Sampai Tanggal'] = $request->string('date_to');
        }

        if ($request->filled('search')) {
            $summary['Pencarian'] = (string) $request->string('search');
        }

        return $summary;
    }
}
