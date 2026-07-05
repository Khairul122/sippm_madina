<?php

declare(strict_types=1);

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * FR-29: ekspor statistik pengaduan ke Excel. Satu baris per status.
 */
class ComplaintStatisticsExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['Status', 'Jumlah'];
    }

    public function array(): array
    {
        return Complaint::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [$row->status->label(), $row->total])
            ->toArray();
    }
}
