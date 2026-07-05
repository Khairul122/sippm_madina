<?php

namespace Database\Seeders;

use App\Application\DTOs\SubmitActivityDTO;
use App\Application\DTOs\SubmitComplaintDTO;
use App\Application\UseCases\Activity\SubmitActivityUseCase;
use App\Application\UseCases\Complaint\SubmitComplaintUseCase;
use App\Infrastructure\Persistence\Eloquent\Models\Complaint;
use App\Infrastructure\Persistence\Eloquent\Models\Activity;
use App\Infrastructure\Persistence\Eloquent\Models\Kecamatan;
use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use App\Infrastructure\Persistence\Eloquent\Models\User;
use Illuminate\Database\Seeder;

/**
 * 2 dummy pengaduan + 2 dummy laporan kegiatan untuk kebutuhan demo/QA
 * dashboard & statistik. Dibuat lewat UseCase yang sama dengan alur
 * produksi (bukan insert langsung) supaya nomor tiket, status awal, dan
 * status history-nya konsisten dengan bisnis rule (BR-03, dst).
 */
class DummyReportSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedComplaints();
        $this->seedActivities();
    }

    private function seedComplaints(): void
    {
        $masyarakat = User::query()->where('email', 'masyarakat@demo.test')->first();
        $opd = Opd::query()->orderBy('id')->first();
        $kecamatan = Kecamatan::query()->orderBy('id')->first();

        if (! $masyarakat || Complaint::query()->where('title', 'Jalan Rusak di Depan Kantor Desa')->exists()) {
            return;
        }

        $submitComplaint = app(SubmitComplaintUseCase::class);

        $submitComplaint->execute(new SubmitComplaintDTO(
            userId: $masyarakat->id,
            title: 'Jalan Rusak di Depan Kantor Desa',
            description: 'Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.',
            category: 'Infrastruktur',
            targetType: 'opd',
            targetId: $opd?->id,
            latitude: 0.5333,
            longitude: 99.4167,
        ));

        $submitComplaint->execute(new SubmitComplaintDTO(
            userId: $masyarakat->id,
            title: 'Lampu Jalan Mati di Simpang Tiga',
            description: 'Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.',
            category: 'Pelayanan Publik',
            targetType: 'camat',
            targetId: $kecamatan?->id,
            latitude: 0.5401,
            longitude: 99.4210,
        ));
    }

    private function seedActivities(): void
    {
        $opdUser = User::query()->where('email', 'opd@demo.test')->first();
        $camatUser = User::query()->where('email', 'camat@demo.test')->first();

        if (Activity::query()->where('title', 'Gotong Royong Bersih Desa')->exists()) {
            return;
        }

        $submitActivity = app(SubmitActivityUseCase::class);

        if ($opdUser?->opd_id) {
            $submitActivity->execute(new SubmitActivityDTO(
                title: 'Gotong Royong Bersih Desa',
                description: 'Kegiatan gotong royong membersihkan saluran air dan fasilitas umum bersama warga.',
                actorType: 'opd',
                actorId: $opdUser->opd_id,
                date: now()->subDays(3)->toDateString(),
                location: 'Balai Desa',
            ));
        }

        if ($camatUser?->kecamatan_id) {
            $submitActivity->execute(new SubmitActivityDTO(
                title: 'Musyawarah Perencanaan Pembangunan Kecamatan',
                description: 'Musrenbang tingkat kecamatan membahas prioritas pembangunan tahun berikutnya.',
                actorType: 'kecamatan',
                actorId: $camatUser->kecamatan_id,
                date: now()->subDays(1)->toDateString(),
                location: 'Aula Kantor Camat',
            ));
        }
    }
}
