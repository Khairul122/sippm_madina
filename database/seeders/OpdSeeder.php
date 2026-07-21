<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
            ['name' => 'Sekretariat Daerah Kabupaten', 'code' => 'SETDA'],
            ['name' => 'Sekretariat DPRD', 'code' => 'SETWAN'],
            ['name' => 'Inspektorat Daerah Kabupaten', 'code' => 'INSPEKTORAT'],
            ['name' => 'Dinas Pendidikan dan Kebudayaan', 'code' => 'DISDIKBUD'],
            ['name' => 'Dinas Kesehatan', 'code' => 'DINKES'],
            ['name' => 'Dinas Pekerjaan Umum dan Penataan Ruang', 'code' => 'DISPUPR'],
            ['name' => 'Dinas Perumahan Rakyat dan Kawasan Permukiman serta Pertanahan', 'code' => 'DISPERKIM'],
            ['name' => 'Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak', 'code' => 'DINSOSPPPA'],
            ['name' => 'Dinas Koperasi, Usaha Kecil dan Menengah', 'code' => 'DISKOPUKM'],
            ['name' => 'Dinas Tenaga Kerja', 'code' => 'DISNAKER'],
            ['name' => 'Dinas Pengendalian Penduduk dan Keluarga Berencana', 'code' => 'DPPKB'],
            ['name' => 'Dinas Ketahanan Pangan', 'code' => 'DKP'],
            ['name' => 'Dinas Pertanian', 'code' => 'DISTAN'],
            ['name' => 'Dinas Lingkungan Hidup', 'code' => 'DLH'],
            ['name' => 'Dinas Kependudukan dan Pencatatan Sipil', 'code' => 'DISDUKCAPIL'],
            ['name' => 'Satuan Polisi Pamong Praja dan Pemadam Kebakaran', 'code' => 'SATPOLPP'],
            ['name' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'code' => 'DPMPTSP'],
            ['name' => 'Dinas Komunikasi dan Informatika', 'code' => 'DISKOMINFO'],
            ['name' => 'Dinas Perhubungan', 'code' => 'DISHUB'],
            ['name' => 'Dinas Perdagangan', 'code' => 'DISDAG'],
            ['name' => 'Dinas Pariwisata', 'code' => 'DISPAR'],
            ['name' => 'Dinas Pemberdayaan Masyarakat Desa', 'code' => 'DPMD'],
            ['name' => 'Dinas Pemuda dan Olahraga', 'code' => 'DISPORA'],
            ['name' => 'Dinas Perikanan', 'code' => 'DISKAN'],
            ['name' => 'Badan Penanggulangan Bencana Daerah', 'code' => 'BPBD'],
            ['name' => 'Badan Pendapatan Daerah', 'code' => 'BAPENDA'],
            ['name' => 'Badan Perencanaan Pembangunan Daerah', 'code' => 'BAPPEDA'],
            ['name' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia', 'code' => 'BKPSDM'],
            ['name' => 'Badan Pengelolaan Keuangan, Pendapatan dan Aset Daerah', 'code' => 'BPKPAD'],
            ['name' => 'Badan Penelitian dan Pengembangan', 'code' => 'BALITBANG'],
        ];

        foreach ($opds as $opd) {
            Opd::firstOrCreate(['code' => $opd['code']], $opd);
        }
    }
}
