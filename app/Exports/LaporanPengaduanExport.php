<?php

declare(strict_types=1);

namespace App\Exports;

use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Export Excel untuk halaman Laporan Pengaduan (Kominfo) — satu baris per
 * pengaduan sesuai filter yang sedang aktif di layar.
 */
class LaporanPengaduanExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @param Collection<int, Complaint> $complaints
     * @param Collection<int, string> $opdNames keyed by Opd id
     * @param Collection<int, string> $kecamatanNames keyed by Kecamatan id
     */
    public function __construct(
        private readonly Collection $complaints,
        private readonly Collection $opdNames,
        private readonly Collection $kecamatanNames,
    ) {
    }

    public function headings(): array
    {
        return ['No', 'Tiket', 'Judul', 'Kategori', 'Status', 'Tujuan', 'Tanggal'];
    }

    public function collection(): Collection
    {
        return $this->complaints->values()->map(fn (Complaint $complaint, int $index) => [
            $index + 1,
            $complaint->ticket_number,
            $complaint->title,
            ucfirst($complaint->category),
            $complaint->status->label(),
            $this->targetLabel($complaint),
            $complaint->created_at?->translatedFormat('d M Y'),
        ]);
    }

    public function title(): string
    {
        return 'Laporan Pengaduan';
    }

    private function targetLabel(Complaint $complaint): string
    {
        if ($complaint->target_type === 'opd') {
            return $this->opdNames->get($complaint->target_id, '-');
        }

        if ($complaint->target_type === 'camat') {
            return $this->kecamatanNames->get($complaint->target_id, '-');
        }

        return ucfirst(str_replace('_', ' ', (string) $complaint->target_type));
    }
}
