<?php

declare(strict_types=1);

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanKegiatanExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @param Collection<int, Activity> $activities
     */
    public function __construct(
        private readonly Collection $activities,
    ) {
    }

    public function headings(): array
    {
        return ['No', 'Judul Kegiatan', 'Penerbit (OPD/Kecamatan)', 'Lokasi', 'Tanggal', 'Status'];
    }

    public function collection(): Collection
    {
        return $this->activities->values()->map(fn (Activity $activity, int $index) => [
            $index + 1,
            $activity->title,
            $activity->actor ? $activity->actor->name : '-',
            $activity->location ?? '-',
            $activity->date->translatedFormat('d M Y'),
            $activity->status->label(),
        ]);
    }

    public function title(): string
    {
        return 'Laporan Kegiatan';
    }
}
